<?php
/**
 * General Convert Helper
 */
abstract class convert_helper {
    public static function generate_id($entropy) {
        return md5(time() . '-' . $entropy . '-' . random_string(20));
    }
}