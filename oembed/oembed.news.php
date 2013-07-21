<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=news.loop
[END_COT_EXT]
==================== */

if($jj == 1)
{
	require_once cot_incfile('oembed', 'plug');
	$oembed_rowcount = count($sql_rowset);
	$oembed_newsids = array();
	for($i = 0; $i < $oembed_rowcount; $i++)
	{
		$oembed_newsids[] = $sql_rowset[$i]['page_id'];
	}

	$oembed_newsitems = array(
		'items' => $oembed_newsids
	);
}

$oembed_newsitems['current'] = $pag['page_id'];

$news->vars['PAGE_ROW_TEXT'] = oembed_parser('page', $oembed_newsitems, $news->vars['PAGE_ROW_TEXT'], 0, $pag['page_parser']);
$news->vars['PAGE_ROW_TEXT_CUT'] = oembed_parser('page', $oembed_newsitems, $news->vars['PAGE_ROW_TEXT_CUT'], 0, $pag['page_parser']);

if($jj == $oembed_rowcount)
{
	oembed_parser_cleanup('page');
}