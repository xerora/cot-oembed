<?php defined('COT_CODE') or die('Wrong URL');

$L['cfg_parser_action_hint'] = 'How to treat valid whitelisted provider URLs in text bodies.';
$L['cfg_parser_limit'] = 'Parse only this many URLs per text body';
$L['cfg_parser_limit_hint'] = 'Which URLs actually get parsed with the limit are what ever the parser hits first, not the actual order.';
$L['cfg_fetch_method'] = 'Method to use when fetching data from providers\'';
$L['cfg_fetch_method_params'] = array(
	'Standard', 
	'cURL',
);
$L['cfg_maxheight'] = 'Maximum height of the media from the provider';
$L['cfg_maxheight_hint'] = 'In pixels. Providers don\'t have to respect this value, but it will be sent to them regardless.';
$L['cfg_maxwidth'] = 'Maximum width of the media from the provider';
$L['cfg_maxwidth_hint'] = 'In pixels. Providers don\'t have to respect this value, but it will be sent to them regardless.';
$L['cfg_expire_cache'] = 'Expire oEmbed cache on items older than';
$L['cfg_expire_cache_hint'] = 'Caching of oEmbed content is to limit number of outbound requests for performance and to attempt to avoid provider rate limiting issues.';

$L['cfg_prevent_dups'] = 'Prevent parsing duplicate URLs in a single text body ?';
$L['cfg_prevent_dups_hint'] = 'The first URL will be parsed, but any following identical URL will be ignored.';

$L['cfg_user_attr'] = 'Allow use of width and height attributes in the embed BBCode ?';
$L['cfg_user_attr_hint'] = 'If enabled, users can use [embed width="400" height="400"]. The amount the user sets still will not be allow to be greater than the max set above.';

$L['cfg_retry_time'] = 'Retry fetching invalid URLs after';
$L['cfg_retry_time_hint'] = 'Invalid URLs are provider URLs matched but are invalid because they don\'t exist or you hit the provider rate limit. They get cached to 
prevent matching them again for this given length of time.';

$L['cfg_fetch_limit'] = 'Total number of outbound requests to make on a single page load';
$L['cfg_fetch_limit_hint'] = 'This is just to keep page load times down if there are many uncached items. Items not fetched because of this limit will be grabbed on the next page load.';

$L['cfg_retry_time_params'] = array(
	'Never retry',
	'1 hour',
	'2 hours',
	'3 hours',
	'4 hours',
	'6 hours',
	'8 hours',
	'12 hours',
	'14 hours',
	'16 hours',
	'18 hours',
	'20 hours',
	'22 hours',
	'1 day',
	'2 days',
	'1 week',
	'2 weeks',
);

$L['cfg_expire_cache_params'] = array(
	'Never expire',
	'1 hour',
	'2 hours',
	'3 hours',
	'4 hours',
	'6 hours',
	'8 hours',
	'12 hours',
	'14 hours',
	'16 hours',
	'18 hours',
	'20 hours',
	'22 hours',
	'1 day',
	'2 days',
	'1 week',
	'2 weeks',
	'About 1 month',
	'About 2 months',
	'About 4 months',
	'About 8 months',
	'About 1 year',
);