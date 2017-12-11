# Yet another framework for WordPress plugins

work in progress.

## Update

wp-plugin-mate can provide your users with automatic plugins. Do the following:
1. Create a JSON plugin description file like the example below.
1. Upload the file to a public URL
1. In your plugin class constructor after calling the super-constructor, insert this code:  
   ```$this->update('/absolute/url/to/your.json', 'unique-slug');```
1. Make sure your plugin file holds the correct release number in the ```Version```-tag.
1. Of course, upload the plugin ZIP file as well as the other assets mentioned in the descriptor. 
   
The JSON descriptor contains all information for the current release. This is an example:
```
{
	"name": "My Example Plugin",
	"version": "2.0",
	"download_url": "http://example.com/updates/example-plugin.zip",

	"homepage": "http://example.com/",
	"requires": "4.5",
	"tested": "4.8",
	"last_updated": "2017-01-01 16:17:00",
	"upgrade_notice": "Here's why you should upgrade...",

	"author": "Janis Elsts",
	"author_homepage": "http://example.com/",

	"sections": {
		"description": "(Required) Plugin description. Basic HTML can be used in all sections.",
		"installation": "(Recommended) Installation instructions.",
		"changelog": "(Recommended) Changelog. <p>This section will be displayed by default when the user clicks 'View version x.y.z details'.</p>",
		"custom_section": "This is a custom section labeled 'Custom Section'."
	},

	"banners": {
		"low": "http://w-shadow.com/files/external-update-example/assets/banner-772x250.png",
		"high": "http://w-shadow.com/files/external-update-example/assets/banner-1544x500.png"
	},

	"translations": [
		{
			"language": "fr_FR",
			"version": "4.0",
			"updated": "2016-04-22 23:22:42",
			"package": "http://example.com/updates/translations/french-language-pack.zip"
		},
		{
			"language": "de_DE",
			"version": "5.0",
			"updated": "2016-04-22 23:22:42",
			"package": "http://example.com/updates/translations/german-language-pack.zip"
		}
	],

	"rating": 90,
	"num_ratings": 123,

	"downloaded": 1234,
	"active_installs": 12345
}
```

Thanks to https://github.com/YahnisElsts for https://github.com/YahnisElsts/plugin-update-checker, on top of which this is built.