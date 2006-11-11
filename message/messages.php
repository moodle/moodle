<?php // $Id$

    require('../config.php');

/// Select direction
    if ( get_string('thisdirection') == 'rtl' ) {
        $direction = ' dir="rtl"';
    } else {
        $direction = ' dir="ltr"';
    }
/// Output the header
    @header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html <?php echo $direction ?>>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
  <body class="message course-1" id="message-messages">
