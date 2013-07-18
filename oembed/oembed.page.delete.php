<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done,page.admin.delete.done
[END_COT_EXT]
==================== */

require_once cot_incfile('oembed', 'plug');
oembed_remove('page', $id);
oembed_remove('comments_page', $id);
