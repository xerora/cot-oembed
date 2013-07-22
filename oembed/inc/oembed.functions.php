<?php defined('COT_CODE') or die('Wrong URL');

$GLOBALS['oembed_providers_whitelist'] = array(
	'https?://(www\.)?twitter\.com/.+/status(es)?/[^\s"<]+' => 'http://api.twitter.com/1/statuses/oembed.{oembed_format}',
	'https?://(www\.)?flickr\.com/[^\s"<]+' => 'http://www.flickr.com/services/oembed',
	'https?://(www\.)?flic\.kr/p/[^\s"<]+' => 'http://www.flickr.com/services/oembed',
	'https?://(www\.)?youtu\.be/[^\s"<]+' => 'http://www.youtube.com/oembed',
	'https?://(www\.)?youtube\.com/watch[^\s"<]+' => 'http://www.youtube.com/oembed',
	'https?://(www\.)?hulu\.com/watch/[^\s"<]+' => 'http://www.hulu.com/api/oembed.{oembed_format}',
	'https?://(www\.)?blip\.tv/[^\s"<]+' => 'http://blip.tv/oembed/',
	'https?://(www\.)?vimeo\.com/[^\s"<]+' => 'http://vimeo.com/api/oembed.{oembed_format}',
	'https?://(\w+\.)?smugmug\.com/[^\s"<]+' => 'http://api.smugmug.com/services/oembed',
	'https?://(www\.)?viddler\.com/[^\s"<]+' => 'http://lab.viddler.com/services/oembed/',
	'https?://(www\.)?qik\.com/[^\s"<]+' => 'http://qik.com/api/oembed.{oembed_format}',
	'https?://(www\.)?revision3\.com/[^\s"<]+' => 'http://revision3.com/api/oembed/',
	'https?://i([0-9]+\.)photobucket\.com/albums/[^\s"<]+' => 'http://photobucket.com/oembed',
	'https?://gi([0-9]+\.)photobucket\.com/groups/[^\s"<]+' => 'http://photobucket.com/oembed',
	'https?://(www\.)?slideshare.net/[^\s"<]+' => 'http://www.slideshare.net/api/oembed/2',
	'https?://(www\.)?scribd\.com/[^\s"<]+' => 'http://www.scribd.com/services/oembed',
	'https?://(www\.)?soundcloud\.com/[^\s"<]+' => 'http://soundcloud.com/oembed',
	'https?://(www\.)?instagr(\.am|am\.com)/p/[^\s"<]+' => 'http://api.instagram.com/oembed',
	'https?://(www\.)?funnyordie\.com/videos/[^\s"<]+' => 'http://www.funnyordie.com/oembed',
);

$GLOBALS['db_oembed_cache'] = (isset($GLOBALS['db_oembed_cache'])) ? $GLOBALS['db_oembed_cache'] : $GLOBALS['db_x'].'oembed_cache';
$GLOBALS['oembed_area_cache'] = null;
$GLOBALS['oembed_area_cache_used'] = array(); // Items in a single text body
$GLOBALS['oembed_area_cache_used_all'] = array(); // Items from entire page load 
$GLOBALS['oembed_current_area'] = '';
$GLOBALS['oembed_current_item'] = '';
$GLOBALS['oembed_current_pageref'] = '';
$GLOBALS['oembed_current_parser'] = '';
$GLOBALS['oembed_current_provider_embed_url'] = '';
$GLOBALS['oembed_limit_counter'] = 0;
$GLOBALS['oembed_count_fetch'] = 0;
$GLOBALS['oembed_formats'] = array('json');
$GLOBALS['oembed_item_sql'] = '';

if(function_exists('simplexml_load_string'))
{
	$GLOBALS['oembed_formats'][] = 'xml';
}

global $R;

require_once cot_incfile('oembed', 'plug', 'resources');

function oembed_fetch_curl($fetchurl)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $fetchurl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Need to follow "Location" header for providers like Photobucket
	$response = curl_exec($ch);
	if(!curl_errno($ch))
	{
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	}
	curl_close($ch);
	return array('status' => $status_code, 'response' => $response);
}

function oembed_fetch_standard($fetchurl)
{
	$response = @file_get_contents($fetchurl, false,
		stream_context_create(array(
			'timeout' => 2
		))
	);
	return array('status' => oembed_http_status_code($http_response_header[0]), 'response' => $response);
}

function oembed_fetch($provider_embed_url, $url, $maxwidth = 0, $maxheight = 0)
{
	global $cfg, $oembed_formats;

	if(!oembed_url_validate($url))
	{
		return false;
	}

	$url = str_replace('www.', '', $url);
	$maxwidth = oembed_get_max_width($maxwidth);
	$maxheight = oembed_get_max_height($maxheight);

	foreach($oembed_formats as $format)
	{
		$_provider_embed_url = str_replace('{oembed_format}', $format, $provider_embed_url);
		$fetchurl = $_provider_embed_url.'?url='.urlencode($url).'&format='.$format
			.'&maxwidth='.(int)$maxwidth
			.'&maxheight='.(int)$maxheight;

		switch($cfg['plugin']['oembed']['fetch_method'])
		{
			case 'standard':
				$fetch = oembed_fetch_standard($fetchurl);
			break;
			case 'curl':
				$fetch = oembed_fetch_curl($fetchurl);
			break;
		}

		if($fetch['status'] != 501)
		{
			switch($format)
			{
				case 'json':
					$data = oembed_format_json($fetch['response']);
				break;
				case 'xml':
					$data = oembed_format_xml($fetch['response']);
				break;
			}

			break;
		}
	}

	if($data && is_array($data) && !empty($fetch['response']))
	{
		return $data;
	}

	return false;
}

function oembed_format_data($data, $maxwidth = 0, $maxheight = 0)
{
	global $R, $cfg;
	$return = '';
	$temp_html = $data['html'];
	$data['url'] = oembed_url_validate($data['url']) ? $data['url'] : '';
	$data['thumbnail_url'] = oembed_url_validate($data['thumbnail_url']) ? $data['thumbnail_url'] : '';
	$data = array_map('htmlspecialchars', $data);
	$data['html'] = $temp_html;

	$maxwidth = oembed_get_max_width($maxwidth);
	$maxheight = oembed_get_max_height($maxheight);
	$data['width'] = ($maxwidth < $data['width']) ? (int)$maxwidth: (int)$data['width'];
	$data['height'] = ($maxheight < $data['height']) ? (int)$maxheight : (int)$data['height'];

	switch($data['type'])
	{
		case 'video':
		case 'rich':
			if(!empty($data['html']))
			{
				$return = isset($R['oembed_type_'.$data['type']]) ? cot_rc('oembed_type_'.$data['type'], $data) : $data['html']; 
			}
		break;
		case 'photo':
			if(!empty($data['url']) && !empty($data['width']) && !empty($data['height']))
			{
				$title = !empty($data['title']) ? $data['title'] : '';
				$return = cot_rc('oembed_type_photo', $data + array('title' => $title));
			}
		break;
		case 'link':
			if(!empty($data['title']))
			{
				$return = cot_rc('oembed_type_link', $data);
			}
		break;
	}
	return $return;
}

function oembed_get_max_width($maxwidth)
{
	global $cfg;
	return ($maxwidth > 32 && $cfg['plugin']['oembed']['maxwidth'] >= $maxwidth) ? $maxwidth : $cfg['plugin']['oembed']['maxwidth'];
}

function oembed_get_max_height($maxheight)
{
	global $cfg;
	return ($maxheight > 32 && $cfg['plugin']['oembed']['maxheight'] >= $maxheight) ? $maxheight: $cfg['plugin']['oembed']['maxheight'];
}

function oembed_bbcode_parse_attributes($attrs)
{
	$oembed_start_length = mb_strlen($attrs);
	$return_attributes = array();
	$valid_attributes = array(
		'width' => 0,
		'height' => 0
	);
	if($oembed_start_length < 65 && $oembed_start_length > 10)
	{
		$attrs = str_replace('&quot;', '"', $attrs);
		$attr_count = preg_match_all('`('.implode('|', array_keys($valid_attributes)).')="([0-9]+)"`i', $attrs, $attr_match, PREG_SET_ORDER);

		if($attr_count > 0 && is_array($attr_match))
		{
			for($i = 0; $i < $attr_count; $i++)
			{
				if(in_array($attr_match[$i][1], $valid_attributes))
				{
					$return_attributes[$attr_match[$i][1]] = (int)$attr_match[$i][2];
				}
			}
		}
	}
	return array_merge($valid_attributes, $return_attributes);
}

function oembed_bbcode_strip($str)
{
	return preg_replace(array('|\[embed[^]]*\]|i', '|\[/embed\]|i'), '', $str);
}

function oembed_parse($match)
{
	global $db_oembed_cache, $db, $cfg, $sys, $oembed_area_cache, $oembed_area_cache_used, $oembed_current_area,
	$oembed_current_item, $oembed_current_pageref, $oembed_current_provider_embed_url, $oembed_limit_counter,
	$oembed_current_parser, $oembed_count_fetch, $oembed_area_cache_used_all, $oembed_item_sql;

	// Only fetch the cache if there is atleast one URL match
	if(is_null($oembed_area_cache[$oembed_current_area]))
	{
		$retrying = FALSE;
		$expire_sql = '';
		$retry_sql = '';

		if((int)$cfg['plugin']['oembed']['retry_time'] > 0)
		{
			$retrying = TRUE;
			$retry_invalid_time = ($sys['now'] - $cfg['plugin']['oembed']['retry_time']);
			$retry_sql = 'oembed_valid=0 AND oembed_added < '.$retry_invalid_time;
		}

		if((int)$cfg['plugin']['oembed']['expire_cache'] > 0)
		{
			$expire_sql = ($retrying) ? ' OR ' : '';
			$expire_sql .= '(oembed_added < '.($sys['now'] - (int)$cfg['plugin']['oembed']['expire_cache']).')';
		}

		if((!empty($retry_sql) || !empty($expire_sql)) && !empty($oembed_item_sql))
		{
			$sql = $db->query("DELETE FROM $db_oembed_cache WHERE oembed_area=? AND $oembed_item_sql AND oembed_pageref=? AND ".
				$retry_sql." ".$expire_sql. " ORDER BY oembed_added ASC LIMIT 2", array($oembed_current_item, $oembed_current_pageref));
		}
		if(!empty($oembed_item_sql))
		{
			$oembed_cache = $db->query("SELECT * FROM $db_oembed_cache ".
				"WHERE oembed_area=? AND $oembed_item_sql AND oembed_pageref=? ORDER BY oembed_added DESC", array($oembed_current_area, $oembed_current_pageref))->fetchAll();
			$oembed_area_cache[$oembed_current_area] = oembed_format_cache_rows($oembed_cache);
		}
		else
		{
			$oembed_area_cache[$oembed_current_area] = array();
		}
	}

	$limit = (int)$cfg['plugin']['oembed']['parser_limit'];
	$isshortcode = false;

	if($match[count($match)-1] == '[/embed]')
	{
		if((bool)$cfg['plugin']['oembed']['user_attr'])
		{	
			$attributes = oembed_bbcode_parse_attributes($match[1]);
			$maxwidth = $attributes['width'];
			$maxheight = $attributes['height'];
		}
		$isshortcode = true;
		$url = $match[3];
	}
	elseif($oembed_current_parser == 'html')
	{
		$url = $match[2];
	}
	elseif($oembed_current_parser == 'bbcode' || $oembed_current_parser == 'none')
	{
		$url = $match[1];
	}

	if(empty($url) || $oembed_limit_counter >= $limit || ((bool)$cfg['plugin']['oembed']['prevent_dups'] && in_array($url, $oembed_area_cache_used[$oembed_current_area])))
	{
		return oembed_bbcode_strip($match[0]);
	}

	$oembed_limit_counter++;
	$url = trim($url);

	if(isset($oembed_area_cache[$oembed_current_area][$url]))
	{
		$data = $oembed_area_cache[$oembed_current_area][$url];
		$oembed_area_cache_used[$oembed_current_area][] = $url;
	}
	elseif($oembed_count_fetch < (int)$cfg['plugin']['oembed']['fetch_limit'])
	{
		$data = oembed_fetch($oembed_current_provider_embed_url, $url, $maxwidth, $maxheight);
		$oembed_count_fetch++;

		$insert = array(
			'oembed_area' => $oembed_current_area,
			'oembed_item' => $oembed_current_item,
			'oembed_url' => (string)$url,
			'oembed_pageref' => (int)$oembed_current_pageref,
			'oembed_added' => $sys['now'],
			'oembed_valid' => oembed_validate_data($data),
		);
		if($data)
		{
			$insert += array(
				'url' => (string)$data['url'],
				'type' => (string)$data['type'],
				'version' => (string)$data['version'],
				'title' => (string)$data['title'],
				'author_name' => (string)$data['author_name'],
				'author_url' => (string)$data['author_url'],
				'provider_name' => (string)$data['provider_name'],
				'provider_url' => (string)$data['provider_url'],
				'cache_age' => (int)$data['cache_age'],
				'width' => (int)$data['width'],
				'height' => (int)$data['height'],
				'thumbnail_url' => (string)$data['thumbnail_url'],
				'thumbnail_width' => (string)$data['thumbnail_width'],
				'thumbnail_height' => (string)$data['thumbnail_height'],
				'html' => (string)str_replace(array("\r\n", "\n"), '', trim($data['html'])),
			);
		}

		$data = $insert;
		$db->insert($db_oembed_cache, $insert);
		$oembed_area_cache[$oembed_current_area][$url] = $insert;
		$oembed_area_cache_used[$oembed_current_area][] = $url;
	}

	$oembed_area_cache_used_all[$oembed_current_area][] = $url;

	if($data && $data['oembed_valid'])
	{
		$formated_data = oembed_format_data($data, $maxwidth, $maxheight);
	}
	else
	{
		return oembed_bbcode_strip($match[0]);
	}

	if(!$isshortcode)
	{
		global $R;
		$parser_wrapper = isset($R['oembed_parser_'.$oembed_current_parser.'_wrapper']) ? $oembed_current_parser : 'none';
		$formated_data = cot_rc('oembed_parser_'.$oembed_current_parser.'_wrapper', array(
			'formated_return' => $formated_data
		));
	}
	else
	{
		if(!empty($match[2]) && $match[2] == '</span>')
		{
			// Add the </span> back that was removed from replacement
			$formated_data = $match[2].$formated_data;
		}
	}

	return $formated_data;
}

/**
* Replaces URLs associated with a whitelisted group of providers with embedded media content.
*
* @param $area string The area you are parsing in. This is only for caching and can be anything you want or something like extension name
* @param $item int The item ID or something to reference an item in some way. This is only for caching.
* @param $text string The text body to parse URLs out of
* @param $pageref int The page number or offset. This is for caching only. This allows the function to grab only the needed visible items from the cache.
* @param $parser string The parser this text bodies uses. This defaults to $cfg['parser'] if left null
* @param $whitelist array The list of valid URL expressions and their source as the value. Defaults to $oembed_whitelisted_providers
*
* @return string Text body from $text with the URLs now as embedded content
*/
function oembed_parser($area, $item, $text, $pageref = 0, $parser = null, $whitelist = null)
{
	global $cfg, $oembed_current_area, $sys, $oembed_area_cache, $oembed_area_cache_used, $db, $db_oembed_cache, 
	$oembed_providers_whitelist, $oembed_current_item, $oembed_current_pageref, $oembed_current_provider_embed_url,
	$oembed_limit_counter, $oembed_current_parser, $oembed_area_cache_used_all, $oembed_item_sql;

	$oembed_current_area = $area;
	$oembed_current_parser = !empty($parser) ? $parser : $cfg['parser']; 
	$oembed_current_pageref = $pageref;
	$oembed_limit_counter = 0;
	$oembed_area_cache_used = array();

	if(!is_array($oembed_area_cache_used[$area]))
	{
		$oembed_area_cache_used[$area] = array();
	}
	if(!is_array($oembed_area_cache_used_all[$area]))
	{
		$oembed_area_cache_used_all[$area] = array();
	}

	$limit = (int)$cfg['plugin']['oembed']['parser_limit'];
	$parsed = array();

	if(!empty($item['items']) && is_array($item['items']))
	{
		$oembed_item_sql = "oembed_item IN (".implode(',', $item['items']).")";
		$item = $item['current'];
	}
	else
	{
		$oembed_item_sql = "oembed_item=".(int)$item."";
	}

	$oembed_current_item = $item;

	if(is_null($whitelist))
	{
		$whitelist = $oembed_providers_whitelist;
	}

	if(!empty($whitelist))
	{

		$parser_expression_prefix = '';
		$parser_expression_suffix = '';
		$parser_link_replacement = '';

		if($oembed_current_parser == 'bbcode' || $oembed_current_parser == 'none')
		{
			$parser_expression_prefix = '^\s*';
			$parser_expression_suffix = '\s*(<br />)*(\s)*$';
			$link_replacement = '$1';
		}
		if($oembed_current_parser == 'html')
		{
			$parser_expression_prefix = '<p[^>]*>(<span[^>]*>)*\s*';
			$parser_expression_suffix = '\s*(</span>)*</p>';
			$link_replacement = '<p>$2</p>';
		}

		foreach($whitelist as $provider_expression => $provider_embed_url)
		{
			$oembed_current_provider_embed_url = $provider_embed_url; 
			if(!empty($link_replacement))
			{
				$text = preg_replace('`'.$parser_expression_prefix.'<a[^>]*href="('.$provider_expression.')"[^>]*>('.$provider_expression.')</a>'.$parser_expression_suffix.'`imu', $link_replacement, $text);
			}

			$parser_patterns = 	array(
				'`(\[embed[^]]*\])(</span>)*('.$provider_expression.')(\[/embed\])`ui',
				'`'.$parser_expression_prefix.'('.$provider_expression.')'.$parser_expression_suffix.'`mui',
			);

			$text = preg_replace_callback($parser_patterns, 'oembed_parse', $text);
		}
	}
	return $text;
}

/**
* Removes cached items that are no longer found in the text body.
*
* @param string $area Area that was used for parsing the links
*/
function oembed_parser_cleanup($area)
{
	global $db, $sys, $db_oembed_cache, $oembed_area_cache, $oembed_area_cache_used_all;
	// Clean unused cached embeded items
	$count = 0;
	if(empty($oembed_area_cache[$area]))
	{
		return;
	}
	$cache_clear = array_diff(array_keys($oembed_area_cache[$area]), $oembed_area_cache_used_all[$area]);
	if(is_array($cache_clear) && !empty($cache_clear))
	{
		$ids = array();
		foreach($cache_clear as $clear_url)
		{
			if($count > 15)
			{
				break;
			}
			if(!empty($clear_url) && (int)$oembed_area_cache[$area][$clear_url]['oembed_id'] > 0)
			{
				$ids[] = (int)$oembed_area_cache[$area][$clear_url]['oembed_id'];
				$count++;
			}
		}
		if(is_array($ids) && $count > 0)
		{
			$db->query("DELETE FROM $db_oembed_cache WHERE oembed_id IN (".implode(',', $ids).")");
		}
	}
}

/**
* Removes cached URLs for an item
*
* @param string $area Area
* @param int $item Item
* @param int $pageref (optional) Page reference 
*/
function oembed_remove($area, $item, $pageref = null)
{
	global $db, $db_oembed_cache;
	if(!is_null($pageref))
	{
		$pageref_sql = " AND oembed_pageref='".(int)$pageref."'";
	}
	$db->delete($db_oembed_cache, "oembed_area=? AND oembed_item=?".$pageref, array($area, $item));
}

function oembed_format_cache_rows($rows)
{
	$format = array();
	foreach($rows as $row)
	{
		$format[$row['oembed_url']] = $row;
	}
	return $format;
}

function oembed_validate_data($data)
{
	if(empty($data['type']))
	{
		return 0;
	}
	$oembed_valid = 0;
	switch($data['type'])
	{
		case 'video':
		case 'rich':
			if(!empty($data['html']) && is_string($data['html']))
			{
				$oembed_valid = !empty($data['html']) ? 1 : 0;
			}
		break;
		case 'photo':
			if(!empty($data['url']) || !empty($data['width']) || !empty($data['height']))
			{
				$oembed_valid = 1;
			}
		break;
		case 'link':
			if(!empty($data['title']) && is_string($data['title']))
			{
				$oembed_valid = 1;
			}
		break;
	}
	return $oembed_valid;
}

function oembed_url_validate($url, $valid_schemes = null)
{
	if(!empty($valid_schemes) && is_array($valid_schemes))
	{
		$schemes = $valid_schemes;
	}
	else
	{
		$schemes = array('http','https');
	}
	$purl = @parse_url($url);
	if(!$purl || false !== strpos($purl['host'], ':') || !in_array($purl['scheme'], $schemes))
	{
		return false;
	}

	return $url;
}

function oembed_format_json($response)
{
	$data = (array)json_decode($response);
	if(is_array($data) && !empty($data) && isset($data['version']))
	{
		return $data;
	}
	return false;
}

function oembed_format_xml($response)
{
	$data = (array)simplexml_load_string($response);
	if(is_array($data) && !empty($data) && isset($data['version']))
	{
		return $data;
	}
	return false;
}

function oembed_http_status_code($status)
{
	$status = explode(' ', $status);
	return (int)$status[1];
}

function oembed_get_methods()
{
	$methods = array('standard');
	if(function_exists('curl_version'))
	{
		$methods[] = 'curl';
	}
	return $methods;
}