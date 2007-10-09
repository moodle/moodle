<?php  // $Id$
    if (!isset($sortorder)) {
        $sortorder = '';
    }    
    if (!isset($sortkey)) {
        $sortkey = '';
    }
    
    //make sure variables are properly cleaned
    $sortkey   = clean_param($sortkey, PARAM_ALPHA);// Sorted view: CREATION | UPDATE | FIRSTNAME | LASTNAME...
    $sortorder = clean_param($sortorder, PARAM_ALPHA);   // it defines the order of the sorting (ASC or DESC)

    $toolsrow = array();
    $browserow = array();
    $inactive = array();
    $activated = array();

    if (!has_capability('mod/glossary:approve', $context) && $tab == GLOSSARY_APPROVAL_VIEW) {
    /// Non-teachers going to approval view go to defaulttab
        $tab = $defaulttab;
    }


    $browserow[] = new tabobject(GLOSSARY_STANDARD_VIEW, 
                                 $CFG->wwwroot.'/mod/glossary/view.php?id='.$id.'&amp;mode=letter',
                                 get_string('standardview', 'glossary'));

    $browserow[] = new tabobject(GLOSSARY_CATEGORY_VIEW, 
                                 $CFG->wwwroot.'/mod/glossary/view.php?id='.$id.'&amp;mode=cat', 
                                 get_string('categoryview', 'glossary'));

    $browserow[] = new tabobject(GLOSSARY_DATE_VIEW, 
                                 $CFG->wwwroot.'/mod/glossary/view.php?id='.$id.'&amp;mode=date',
                                 get_string('dateview', 'glossary'));

    $browserow[] = new tabobject(GLOSSARY_AUTHOR_VIEW, 
                                 $CFG->wwwroot.'/mod/glossary/view.php?id='.$id.'&amp;mode=author',
                                 get_string('authorview', 'glossary'));

    if ($tab < GLOSSARY_STANDARD_VIEW || $tab > GLOSSARY_AUTHOR_VIEW) {   // We are on second row
        $inactive = array('edit');
        $activated = array('edit');

        $browserow[] = new tabobject('edit', '#', get_string('edit'));
    }

/// Put all this info together

    $tabrows = array();
    $tabrows[] = $browserow;     // Always put these at the top
    if ($toolsrow) {
        $tabrows[] = $toolsrow;
    }


?>
  <div class="glossarydisplay">


<?php if ($showcommonelements) { print_tabs($tabrows, $tab, $inactive, $activated); } ?>

  <div class="entrybox">

<?php
 
    if (!isset($category)) {
        $category = "";
    }

    
    switch ($tab) {
        case GLOSSARY_CATEGORY_VIEW:
            glossary_print_categories_menu($cm, $glossary, $hook, $category);
        break;
        case GLOSSARY_APPROVAL_VIEW:
            glossary_print_approval_menu($cm, $glossary, $mode, $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_AUTHOR_VIEW:
            $search = "";
            glossary_print_author_menu($cm, $glossary, "author", $hook, $sortkey, $sortorder, 'print');
        break;
        case GLOSSARY_IMPORT_VIEW:
            $search = "";
            $l = "";
            glossary_print_import_menu($cm, $glossary, 'import', $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_EXPORT_VIEW:
            $search = "";
            $l = "";
            glossary_print_export_menu($cm, $glossary, 'export', $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_DATE_VIEW:
            if (!$sortkey) {
                $sortkey = 'UPDATE';
            }
            if (!$sortorder) {
                $sortorder = 'desc';
            }
            glossary_print_alphabet_menu($cm, $glossary, "date", $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_STANDARD_VIEW:
        default:
            glossary_print_alphabet_menu($cm, $glossary, "letter", $hook, $sortkey, $sortorder);
            if ($mode == 'search' and $hook) {
                echo "<h3>$strsearch: $hook</h3>";
            } 
        break;
    } 
    echo '<hr />';
?>
