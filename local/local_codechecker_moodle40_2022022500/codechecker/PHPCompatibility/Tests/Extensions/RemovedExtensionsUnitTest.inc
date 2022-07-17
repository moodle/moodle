<?php

activescript();
activescript_foobar();

cpdf();
Cpdf();
cpdf_foo();

dbase();

dbx();

dio();

fam();

fbsql();

fdf();

filepro();

hw_api();

ingres();

ircg();

mcve();

ming();

mnogosearch();

msql();

mysql_connect();

ncurses();

oracle();

ovrimos();

pfpro_init();

sqlite();

sybase();

w32api();

yp();

// Examples of extension names that should not be considered wrong.
mnogosearch;
function pfpro() {}
$ok = new Sybase();
$someObject->ming();

// More removed extensions:
mssql_bind();

ereg();

// @codingStandardsChangeSetting PHPCompatibility.Extensions.RemovedExtensions functionWhitelist mysql_to_rfc3339
mysql_to_rfc3339();

// PHP 7.1 removed extensions:
mcrypt_encrypt();

// @codingStandardsChangeSetting PHPCompatibility.Extensions.RemovedExtensions functionWhitelist ereg_convert,ereg_translate_to_preg
ereg_convert();
ereg_translate_to_preg();
ereg_replace(); // Non-whitelisted.

ibase_trans();
wddx_deserialize();
recode();

// Don't throw errors during live code review.
ereg($a, $b
