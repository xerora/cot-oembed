<?php defined('COT_CODE') or die('Wrong URL');

$R['oembed_type_rich'] = '{$html}';
$R['oembed_type_video'] = '{$html}';
$R['oembed_type_photo'] = '<a href="{$url}"><img src="{$url}" alt="{$title}" width="{$width}" height="{$height}"></a>';
$R['oembed_type_link'] = '<a href="{$url}">{$title}</a>';
$R['oembed_generate_link'] = '<a href="{$url}">{$url}</a>';

$R['oembed_parser_html_wrapper'] = '<p>{$formated_return}</p>';
$R['oembed_parser_bbcode_wrapper'] = '{$formated_return}<br />';
$R['oembed_parser_none_wrapper'] = '{$formated_return}<br />';