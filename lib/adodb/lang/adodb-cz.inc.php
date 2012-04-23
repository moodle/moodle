<?php

# Czech language, encoding by ISO 8859-2 charset (Iso Latin-2)
# For convert to MS Windows use shell command:
#    iconv -f ISO_8859-2 -t CP1250 < adodb-cz.inc.php
# For convert to ASCII use shell command:
#    unaccent ISO_8859-2 < adodb-cz.inc.php
# v1.0, 19.06.2003 Kamil Jakubovic <jake@host.sk>

$ADODB_LANG_ARRAY = array (
            'LANG'                      => 'cz',
            DB_ERROR                    => 'neznámá chyba',
            DB_ERROR_ALREADY_EXISTS     => 'ji? existuje',
            DB_ERROR_CANNOT_CREATE      => 'nelze vytvo?it',
            DB_ERROR_CANNOT_DELETE      => 'nelze smazat',
            DB_ERROR_CANNOT_DROP        => 'nelze odstranit',
            DB_ERROR_CONSTRAINT         => 'poru?ení omezující podmínky',
            DB_ERROR_DIVZERO            => 'd?lení nulou',
            DB_ERROR_INVALID            => 'neplatné',
            DB_ERROR_INVALID_DATE       => 'neplatné datum nebo ?as',
            DB_ERROR_INVALID_NUMBER     => 'neplatné ?íslo',
            DB_ERROR_MISMATCH           => 'nesouhlasí',
            DB_ERROR_NODBSELECTED       => '?ádná databáze není vybrána',
            DB_ERROR_NOSUCHFIELD        => 'pole nenalezeno',
            DB_ERROR_NOSUCHTABLE        => 'tabulka nenalezena',
            DB_ERROR_NOT_CAPABLE        => 'nepodporováno',
            DB_ERROR_NOT_FOUND          => 'nenalezeno',
            DB_ERROR_NOT_LOCKED         => 'nezam?eno',
            DB_ERROR_SYNTAX             => 'syntaktická chyba',
            DB_ERROR_UNSUPPORTED        => 'nepodporováno',
            DB_ERROR_VALUE_COUNT_ON_ROW => '',
            DB_ERROR_INVALID_DSN        => 'neplatné DSN',
            DB_ERROR_CONNECT_FAILED     => 'p?ipojení selhalo',
            0	                        => 'bez chyb', // DB_OK
            DB_ERROR_NEED_MORE_DATA     => 'málo zdrojových dat',
            DB_ERROR_EXTENSION_NOT_FOUND=> 'roz?í?ení nenalezeno',
            DB_ERROR_NOSUCHDB           => 'databáze neexistuje',
            DB_ERROR_ACCESS_VIOLATION   => 'nedostate?ná práva'
);
?>