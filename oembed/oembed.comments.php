<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.loop
[END_COT_EXT]
==================== */

if($kk == 1)
{
	require_once cot_incfile('oembed', 'plug');
	$oembed_requested_comments_count = count($sql->fetchAll());
}

$t->vars['COMMENTS_ROW_TEXT'] = oembed_parser('comments_'.$row['com_area'], $row['com_code'], $t->vars['COMMENTS_ROW_TEXT'], $d);

if($oembed_requested_comments_count == $kk)
{
	oembed_parser_cleanup('comments_'.$row['com_area']);
}