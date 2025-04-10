<?php
/**
 * Plugin Name: Custom Author URLs
 * Plugin URI: https://martinmiller.co
 * Description: Changes author permalinks from /author/username/ to a custom base with URLs generated from user nicknames.
 * Version: 1.0
 * Author: Brent Miller
 * Author URI: https://martinmiller.co
 * License: GPL2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class MM_Custom_Author_URLs {
    // Default custom base
    private $author_base = 'team';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add settings page
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Filter author permalinks
        add_filter('init', array($this, 'setup_author_base'));
        add_filter('author_link', array($this, 'replace_author_url'), 10, 3);
        add_filter('pre_get_posts', array($this, 'override_wp_author_query'));
        
        // Register activation hooks
        register_activation_hook(__FILE__, array($this, 'plugin_activation'));
        register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
    }
    
    /**
     * Add settings page under Settings menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Custom Author URLs',
            'Custom Author URLs',
            'manage_options',
            'mm-custom-author-urls',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'mm_custom_author_urls', 
            'mm_author_base',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'team'
            )
        );
        
        // Hook into the sanitize filter to add validation
        add_filter('sanitize_option_mm_author_base', array($this, 'validate_author_base'));
        
        add_settings_section(
            'mm_custom_author_urls_section',
            'Author URL Settings',
            array($this, 'settings_section_callback'),
            'mm-custom-author-urls'
        );
        
        add_settings_field(
            'mm_author_base',
            'Author Base',
            array($this, 'author_base_field_callback'),
            'mm-custom-author-urls',
            'mm_custom_author_urls_section'
        );
    }
    
    /**
     * Validate author base for reserved words after sanitization
     */
    public function validate_author_base($value) {
        // Ensure we don't conflict with other common WordPress URL bases
        $restricted = array('category', 'tag', 'attachment', 'post', 'page');
        if (in_array($value, $restricted)) {
            // Add error message
            add_settings_error(
                'mm_author_base',
                'mm_author_base_error',
                'The author base cannot use reserved words like: category, tag, post, page, or attachment.',
                'error'
            );
            
            // Return previous value instead
            return get_option('mm_author_base', 'team');
        }
        
        return $value;
    }
    
    /**
     * Settings section description
     */
    public function settings_section_callback() {
        echo '<p>Customize your author URL structure. Default value is "team".</p>';
    }
    
    /**
     * Author base field HTML
     */
    public function author_base_field_callback() {
        $author_base = get_option('mm_author_base', $this->author_base);
        echo '<input type="text" name="mm_author_base" value="' . esc_attr($author_base) . '" />';
        echo '<p class="description">This will change author URLs from /author/username/ to /' . esc_html($author_base) . '/nickname-with-hyphens/</p>';
    }
    
    /**
     * Settings page HTML
     */
    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('mm_custom_author_urls');
                do_settings_sections('mm-custom-author-urls');
                submit_button();
                ?>
            </form>
            <div class="card">
                <h2>About This Plugin</h2>
                <p>Created by Brent at <a href="https://martinmiller.co" target="_blank">Martin Miller Software Consulting</a></p>
                <p>If you find this plugin useful, consider <a href="https://buymeacoffee.com/brentmartinmiller" target="_blank">buying me a coffee</a>!</p>
            </div>
            
            <div class="card">
                <h2>Troubleshooting</h2>
                <p>After changing settings, please:</p>
                <ol>
                    <li>Go to Settings > Permalinks</li>
                    <li>Click "Save Changes" without making any changes</li>
                </ol>
                <p>This will refresh WordPress's rewrite rules.</p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Setup author base during WordPress init
     */
    public function setup_author_base() {
        global $wp_rewrite;
        $author_base = get_option('mm_author_base', $this->author_base);
        $wp_rewrite->author_base = $author_base;
    }
    
    /**
     * Replace author URL with custom format
     */
    public function replace_author_url($link, $author_id, $author_nicename) {
        $author_base = get_option('mm_author_base', $this->author_base);
        $nickname = get_user_meta($author_id, 'nickname', true);
        
        if (!empty($nickname)) {
            $nickname_slug = $this->sanitize_nickname($nickname);
            
            // Store the mapping
            update_user_meta($author_id, '_mm_author_slug', $nickname_slug);
            
            return home_url("/$author_base/$nickname_slug/");
        }
        
        return $link;
    }
    
    /**
     * Override WordPress author query to match nickname in URL
     */
    public function override_wp_author_query($query) {
        if (!$query->is_author()) {
            return $query;
        }
        
        // Get the current author name from the query
        if (isset($query->query['author_name'])) {
            $author_slug = $query->query['author_name'];
            
            // First try direct match with username
            $author = get_user_by('slug', $author_slug);
            
            // If no match, try to find by nickname slug
            if (!$author) {
                // Check if this is a nickname slug - use get_users instead of direct DB query
                $users = get_users(array(
                    'meta_key' => '_mm_author_slug',
                    'meta_value' => $author_slug,
                    'number' => 1,
                    'fields' => 'ID'
                ));
                
                if (!empty($users)) {
                    $user_id = $users[0];
                    $user = get_user_by('id', $user_id);
                    if ($user) {
                        // Override the query
                        $query->set('author_name', $user->user_nicename);
                        $query->query['author_name'] = $user->user_nicename;
                        $query->queried_object = $user;
                        $query->queried_object_id = $user->ID;
                    }
                }
            }
        }
        
        return $query;
    }
    
    /**
     * Sanitize nickname for URL
     */
    private function sanitize_nickname($nickname) {
        // Convert to lowercase and replace spaces with hyphens
        $nickname = strtolower($nickname);
        $nickname = str_replace(' ', '-', $nickname);
        
        // Remove any non-alphanumeric characters except hyphens
        $nickname = preg_replace('/[^a-z0-9\-]/', '', $nickname);
        
        // Remove multiple consecutive hyphens
        $nickname = preg_replace('/-+/', '-', $nickname);
        
        // Trim hyphens from beginning and end
        return trim($nickname, '-');
    }
    
    /**
     * Plugin activation actions
     */
    public function plugin_activation() {
        // Set default option if it doesn't exist
        if (!get_option('mm_author_base')) {
            update_option('mm_author_base', $this->author_base);
        }
        
        // Update nickname slugs for all users
        $users = get_users();
        foreach ($users as $user) {
            $nickname = get_user_meta($user->ID, 'nickname', true);
            if (!empty($nickname)) {
                $nickname_slug = $this->sanitize_nickname($nickname);
                update_user_meta($user->ID, '_mm_author_slug', $nickname_slug);
            }
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize the plugin
$mm_custom_author_urls = new MM_Custom_Author_URLs();

// Add plugin action links
function mm_custom_author_urls_action_links($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=mm-custom-author-urls') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'mm_custom_author_urls_action_links');

// Add manual flush rewrite rules action
add_action('admin_init', 'mm_flush_rules_on_plugin_page');
function mm_flush_rules_on_plugin_page() {
    // Only flush rules when settings are saved
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
        flush_rewrite_rules();
    }
}

// Add a settings saved action to flush rewrite rules
add_action('update_option_mm_author_base', 'mm_flush_rules_on_setting_save', 10, 2);
function mm_flush_rules_on_setting_save($old_value, $new_value) {
    // Flush rules when the author base is changed
    flush_rewrite_rules();
}