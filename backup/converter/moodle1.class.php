<?php
/**
 * This will be the Moodle 1 to Moodle 2 Converter
 */
abstract class moodle1_converter extends plan_converter {
    /**
     * @return boolean
     */
    public function can_convert() {
        // Then look for MOODLE1 (moodle1) format
        $filepath = $this->get_tempdir() . '/moodle.xml';
        if (file_exists($filepath)) { // Looks promising, lets load some information
            $handle = fopen($filepath, "r");
            $first_chars = fread($handle,200);
            fclose($handle);

            // Check if it has the required strings
            if (strpos($first_chars,'<?xml version="1.0" encoding="UTF-8"?>') !== false &&
                strpos($first_chars,'<MOODLE_BACKUP>') !== false &&
                strpos($first_chars,'<INFO>') !== false) {

                return true;
            }
        }
        return false;
    }

}