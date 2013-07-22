<?php defined('COT_CODE') or die('Wrong URL');
/* ====================
[BEGIN_COT_EXT]
Name=oEmbed
Code=oembed
Category=editor-parser
Description=Replaces URLs associated with a whitelisted group of providers with embedded media content based on the http://oembed.com format.
Version=1.0
Date=2013-july-22
Author=tyler@xaez.org
Copyright=
Notes=BSD License
Auth_guests=R
Lock_guests=2345A
Auth_members=RW
Lock_members=2345
Recommends_modules=forums,pages
Recommends_plugins=comments,news
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
fetch_method=01:callback:oembed_get_methods():standard:
parser_limit=02:select:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25:5:
maxheight=03:string::500:
maxwidth=04:string::500:
expire_cache=05:select:0,3600,7200,10800,14400,21600,28800,43200,50400,57600,64800,72000,79200,86400,172800,604800,1209600,2419200,4838400,9676800,19353600,29030400:0:
prevent_dups=06:radio::1:
user_attr=07:radio::1:
retry_time=08:select:0,3600,7200,10800,14400,21600,28800,43200,50400,57600,64800,72000,79200,86400,172800,604800,1209600:0:
fetch_limit=09:select:10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,35,40,45,50:15:
parse_pagelist=10:select:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,35,40,45,50:0:
[END_COT_EXT_CONFIG]
==================== */