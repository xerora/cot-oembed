<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.loop
[END_COT_EXT]
==================== */

if($jj == 1)
{
	require_once cot_incfile('oembed', 'plug');
	$oembed_page_count = count($sqllist_rowset);
	$oembed_pageids = array();
	for($i = 0; $i < $oembed_page_count; $i++)
	{
		$oembed_pageids[] = $sqllist_rowset[$i]['page_id'];
	}

	$oembed_pageitems = array(
		'items' => $oembed_pageids
	);
}

$oembed_pageitems['current'] = $pag['page_id'];

$t->vars['LIST_ROW_TEXT'] = oembed_parser('page', $oembed_pageitems, $t->vars['LIST_ROW_TEXT'], 0, $pag['page_parser']);
$t->vars['LIST_ROW_TEXT_CUT'] = oembed_parser('page', $oembed_pageitems, $t->vars['LIST_ROW_TEXT_CUT'], 0, $pag['page_parser']);

if($jj == $oembed_page_count)
{
	oembed_parser_cleanup('page');
}




