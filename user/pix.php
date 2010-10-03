<?php

define('NOMOODLECOOKIE', 1);

require('../config.php');

//TODO: redirect to new image location

$PAGE->set_url('/user/pix.php');
$PAGE->set_context(null);

redirect($OUTPUT->pix_url('u/f1'));
