<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
[END_COT_EXT]
==================== */

require_once cot_incfile('oembed', 'plug');

$t->vars['PAGE_TEXT'] = oembed_parser('page', $pag['page_id'], $t->vars['PAGE_TEXT'], 0, $pag['page_parser']);
oembed_parser_cleanup('page');
