<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=pagelist.loop
[END_COT_EXT]
==================== */

if($jj == 1)
{
	require_once cot_incfile('oembed', 'plug');
}

if($cfg['plugin']['oembed']['parse_pagelist'] > 0 && $item <= $cfg['plugin']['oembed']['parse_pagelist'])
{
	$t->vars['PAGE_ROW_TEXT'] = oembed_parser('page', $row['page_id'], $t->vars['PAGE_ROW_TEXT'], 0, $row['page_parser']);
	$t->vars['PAGE_ROW_TEXT_CUT'] = oembed_parser('page', $row['page_id'], $t->vars['PAGE_ROW_CUT'], 0, $row['page_parser']);

	global $oembed_area_cache, $oembed_area_cache_all;
	unset($oembed_area_cache['page'], $oembed_area_cache_all['page']);
}
else
{
	// Keep URLs, just strip the BBcodes
	$t->vars['PAGE_ROW_TEXT'] = oembed_bbcode_strip($t->vars['PAGE_ROW_TEXT']);
	$t->vars['PAGE_ROW_TEXT_CUT'] = oembed_bbcode_strip($t->vars['PAGE_ROW_TEXT_CUT']);
}
