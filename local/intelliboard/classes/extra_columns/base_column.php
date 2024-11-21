<?php

namespace local_intelliboard\extra_columns;

abstract class base_column
{
    protected $params;
    protected $fields;

    public function __construct($params, $fields)
    {
        $this->params = $params;
        $this->fields = $fields;
    }

    abstract public function get_join();
}