<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
<?php
if (array_key_exists('header', $this->data)) {
    echo $this->data['header'];
} else {
    echo 'SimpleSAMLphp';
}
?>
    </title>
    <link rel="stylesheet" type="text/css" href="/<?php echo $this->data['baseurlpath']; ?>resources/default.css" />
    <meta name="robots" content="noindex, nofollow" />

<?php
if (array_key_exists('head', $this->data)) {
    echo '<!-- head -->'.$this->data['head'].'<!-- /head -->';
}
?>
    </head>
    <body class="body-embed">

