<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.loop
[END_COT_EXT]
==================== */

if($fp_num == 1)
{
	require_once cot_incfile('oembed', 'plug');
	$oembed_parse_total = $sql_forums->rowCount();
}

$t->vars['FORUMS_POSTS_ROW_TEXT'] = oembed_parser('forums', $q, $t->vars['FORUMS_POSTS_ROW_TEXT'], $d);

if($oembed_parse_total == $fp_num)
{
	oembed_parser_cleanup('forums');
}
