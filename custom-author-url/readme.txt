=== Custom Author URLs ===
Contributors: brentmiller
Donate link: https://buymeacoffee.com/brentmartinmiller
Tags: permalinks, authors, url-rewrite, nicknames, custom-urls
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize author permalinks to use a different base word instead of "author" and generate URLs from user nicknames.

== Description ==

Custom Author URLs allows you to customize your WordPress author permalinks in two ways:

1. Change the base from `/author/` to a custom word (default is "team")
2. Generate author URLs from user nicknames instead of usernames

For example, instead of `/author/username/`, your author URLs will look like `/team/first-last/` (where "team" is your custom base word and "First Last" is the user's nickname).

**Features:**

* Simple settings page to customize the author base word
* Automatic URL generation from user nicknames
* Proper formatting with lowercase and hyphens
* No template modifications required
* Works with existing themes and plugins

== Installation ==

1. Upload the `custom-author-urls` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Custom Author URLs to configure the plugin
4. Make sure each user has a nickname set in their profile
5. Visit Settings > Permalinks and click "Save Changes" to refresh rewrite rules

== Frequently Asked Questions ==

= Does this plugin require changes to my theme? =

No, this plugin works at the WordPress permalink level and doesn't require any theme modifications.

= How do I set a user's nickname? =

Edit the user's profile and set their nickname in the "Name" section.

= Do I need to update anything when adding new users? =

No, the plugin will automatically generate URLs for new users based on their nicknames.

= What happens if two users have the same nickname? =

The plugin will use the correct mapping to ensure both users have their own pages. However, for best results, it's recommended to use unique nicknames.

== Screenshots ==

1. Settings page to customize the author URL base

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release