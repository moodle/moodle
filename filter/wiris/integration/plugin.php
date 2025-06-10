<?php
if (!class_exists('com_wiris_system_CallWrapper', false)) {
    require_once dirname(__FILE__).'/lib/com/wiris/system/CallWrapper.class.php';
}
com_wiris_system_CallWrapper::getInstance()->init(dirname(__FILE__));
