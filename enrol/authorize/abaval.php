<?php // $Id$

/**
 * Validates the supplied ABA number
 * using a simple mod 10 check digit routine.
 *
 * @param string $aba Bank ID
 * @return bool true ABA is valid, false otherwise
 */
function ABAVal($aba)
{
    if (ereg("^[0-9]{9}$", $aba)) {
        $n = 0;
        for($i = 0; $i < 9; $i += 3) {
            $n += (substr($aba, $i, 1) * 3) +
                  (substr($aba, $i + 1, 1) * 7) +
                  (substr($aba, $i + 2, 1));
        }
        if ($n != 0 and $n % 10 == 0) {
            return true;
        }
    }
    return false;
}

?>
