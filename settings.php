<?php

$choices = array('140'=>'140', '160'=>'160', '180'=>'180', '200'=>'200',
                                     '220'=>'220', '240'=>'240', '260'=>'260', '280'=>'280', '300'=>'300');


$settings->add(new admin_setting_configselect('book_tocwidth', get_string('book_tocwidth', 'book'),
                   get_string('tocwidth', 'book'), '180', $choices));

