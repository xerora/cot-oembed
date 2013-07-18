<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.delete.done
[END_COT_EXT]
==================== */

require_once cot_incfile('oembed', 'plug');
oembed_remove('forums', $q);