<?php

namespace local_intelliboard\attendance\reports;

abstract class report
{
    abstract public function get_data($params);
}