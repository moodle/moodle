<?php

namespace theme_essential_uv\output;

use core_user\output\myprofile;


trait core_renderer_user{

    public static function render_tree($user, $iscurrentuser, $course = null) {
        $return = "Hello world"; 
        return $return;
    }

}