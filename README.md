Replaces URLs associated with a whitelisted group of providers with embedded media content based on the [oEmbed](http://oembed.com) format.

## How it works

#### Automatic Embedding

Simply add a URL on it's own line for one of the supported providers into a forum post, page, or comment text body and it will replace the URL with embedded media from the provider in it's place.

##### Example Usage

```
Hmmm..
https://twitter.com/THErealDVORAK/status/354694500976820225
Ya, that's sounds about right.
```

#### BBCode Embedding (usable in every parser)

Use the BBCode `[embed]url here[/embed]` to embed anywhere in your text body:

##### Example Usage

```
Lorem ipsum dolor [embed]https://www.youtube.com/watch?v=vx50HspXtWA[/embed] sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore  magna  aliqua.
```

The embed BBCode also takes height and width attributes:

##### Example Usage

`[embed width="200" height="200"]url here[/embed]`

## Supported parsers

- HTML (formatted by CKEditor)
- BBCode
- Plain/None

## Supported areas

- Forum posts
- Comments
- News
- Pages

## Supported providers

- Twitter
- Flickr
- YouTube
- Hulu
- blip
- Vimeo
- Smugmug
- Viddler
- qik
- Revision3
- Photobucket
- Slideshare
- Scribd
- SoundCloud
- Instagram
- Funny or Die


## Installation

1. Download, unpack and move the oembed folder to your plugin directory.
2. Install the plugin in the administration panel.
3. Check to see that everything is configured to your preference.


## Available functions

##### oembed_parser( string $area, int $item, string $text [, int $pageref, string $parser, array $whitelist] )

Replaces URLs associated with a whitelisted group of providers with embedded media content.

* `$area` : The area you are parsing in. This is only for caching and can be anything you want or something like extension name
* `$item` : The item ID or something to reference an item in some way. This is only for caching.
* `$text` :  The text body to parse URLs out of
* `$pageref` :  The page number or offset. This is for caching only. This allows the function to grab only the needed visible items from the cache.
* `$parser` : The parser this text bodies uses. This defaults to $cfg['parser'] if left null
* `$whitelist` : The list of valid URL expressions and their source as the value. Defaults to $oembed_whitelisted_providers
* `return` : Text body from $text with the URLs now as embedded content

#### oembed\_parser\_cleanup( string $area )

Removes cached items that are no longer found in the text body.

* `$area` : Area that was used for parsing the links

#### oembed_remove( string $area, int $item [, int $pageref] )

Removes cached URLs for an item

* `$area` : Area
* `$item` : Item
* `$pageref` : Page reference





