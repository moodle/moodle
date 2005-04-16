<?php  /// $Id$

       /// Searches modules, filters and blocks for any Javascript files
       /// that should be called on every page

    $nomoodlecookie = true;

    include('../config.php');

    $output = "// Javascript from Moodle modules\n";

    if ($mods = get_list_of_plugins('mod')) {
        foreach ($mods as $mod) {
            if (is_readable($CFG->dirroot.'/mod/'.$mod.'/javascript.php')) {
                $output .= file_get_contents($CFG->dirroot.'/mod/'.$mod.'/javascript.php');
            }
        }
    }

    if ($filters = get_list_of_plugins('filter')) {
        foreach ($filters as $filter) {
            if (is_readable($CFG->dirroot.'/filter/'.$filter.'/javascript.php')) {
                $output .= file_get_contents($CFG->dirroot.'/filter/'.$filter.'/javascript.php');
            }
        }
    }

    if ($blocks = get_list_of_plugins('blocks')) {
        foreach ($blocks as $block) {
            if (is_readable($CFG->dirroot.'/blocks/'.$block.'/javascript.php')) {
                $output .= file_get_contents($CFG->dirroot.'/blocks/'.$block.'/javascript.php');
            }
        }
    }


    $lifetime = '86400';

    @header('Content-type: text/javascript'); 
    @header('Content-length: '.strlen($output)); 
    @header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    @header('Cache-control: max-age='.$lifetime);
    @header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
    @header('Pragma: ');

    echo $output;

?>
