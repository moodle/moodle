<?php

$caslangprefix = 'PHPCAS_LANG_';
$CASLANGUAGES = array ();

$consts = get_defined_constants(true);
foreach ($consts['user'] as $key => $value) {
    if (substr($key, 0, strlen($caslangprefix)) == $caslangprefix) {
        $CASLANGUAGES[$value] = $value;
    }
}
if (empty($CASLANGUAGES)) {
    $CASLANGUAGES = array ('english' => 'english',
                           'french'  => 'french');
}
