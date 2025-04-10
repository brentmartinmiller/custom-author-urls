# Custom Author URLs plugin for WordPress

A WordPress plugin that customizes author permalinks to use a different base word instead of "author" and generates URLs from user nicknames.

## Description

Custom Author URLs allows you to modify your WordPress author URLs in two ways:

1. Change the base from `/author/` to a custom word (default is "team")
2. Generate URLs from user nicknames instead of usernames

For example, instead of:
```
https://example.com/author/username/
```

Your author URLs will look like:
```
https://example.com/team/first-last/
```

Where "team" is your custom base word and "First Last" is the user's nickname.

## Features

- Simple settings page to customize the author base word
- Automatic URL generation from user nicknames
- Proper formatting with lowercase and hyphens
- Works with existing themes and plugins (no template modifications required)
- Fully compatible with WordPress permalinks system

## Installation

1. Upload the `custom-author-urls` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Custom Author URLs to configure the plugin
4. Make sure each user has a nickname set in their profile
5. Visit Settings > Permalinks and click "Save Changes" to refresh rewrite rules

## Configuration

1. **Set the Author Base**: Go to Settings > Custom Author URLs to change the base word from the default "team" to whatever you prefer
2. **Nickname Setup**: Edit each user and ensure they have a nickname set in their profile
3. **Flush Permalinks**: After making changes, visit Settings > Permalinks and click "Save Changes"

## How It Works

The plugin intercepts author URL requests and translates between nickname-based URLs and WordPress's internal author system. It works by:

1. Changing the author base from "author" to your custom word
2. Converting user nicknames to URL-friendly slugs (lowercase with hyphens)
3. Mapping between the nickname slugs and WordPress usernames

## FAQ

**Does this plugin require changes to my theme?**  
No, this plugin works at the WordPress permalink level and doesn't require any theme modifications.

**How do I set a user's nickname?**  
Edit the user's profile and set their nickname in the "Name" section.

**Do I need to update anything when adding new users?**  
No, the plugin will automatically generate URLs for new users based on their nicknames.

**What happens if two users have the same nickname?**  
While the plugin will handle this correctly, it's recommended to use unique nicknames for best results.

## Support

If you find this plugin useful, consider [buying me a coffee](https://buymeacoffee.com/brentmartinmiller)!

## Credits

Created by [Martin Miller Software Consulting](https://martinmiller.co)

## License

This plugin is licensed under the GPL v2 or later.
