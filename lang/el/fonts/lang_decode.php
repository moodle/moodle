<?php

function lang_decode($s) {
    $len = strlen($s);
    $out = '';
    for($i=0; $i < $len; $i++) {
        $ch = ord($s[$i]);
        $out .= $ch > 128 && $ch < 256 ? '&#'.(720 + $ch).';' : chr($ch);
    }
    return $out;
}

?>
