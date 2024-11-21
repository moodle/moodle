<?php

namespace local_intelliboard\reports\entities;

class in_filter
{
    private $raw;

    public function __construct($values, $prefix)
    {
        global $DB;

        if (!$values) {
            $values = ["-1"];
        }

        $this->raw = $DB->get_in_or_equal($values, SQL_PARAMS_NAMED, $prefix);
    }

    /**
     * Returns = -1 for empty values array
     *
     * @return mixed
     */
    public function get_sql()
    {
        return $this->raw[0];
    }

    public function get_params()
    {
        return $this->raw[1];
    }
}