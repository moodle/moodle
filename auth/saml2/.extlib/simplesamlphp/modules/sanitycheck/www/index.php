<?php

$config = \SimpleSAML\Configuration::getInstance();

$info = [];
$errors = [];
$hookinfo = [
    'info' => &$info,
    'errors' => &$errors,
];
\SimpleSAML\Module::callHooks('sanitycheck', $hookinfo);

if (isset($_REQUEST['output']) && $_REQUEST['output'] == 'text') {
    if (count($errors) === 0) {
        echo 'OK';
    } else {
        echo 'FAIL';
    }
    exit;
}

$t = new \SimpleSAML\XHTML\Template($config, 'sanitycheck:check.tpl.php');
$t->data['pageid'] = 'sanitycheck';
$t->data['errors'] = $errors;
$t->data['info'] = $info;
$t->show();
