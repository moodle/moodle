<?php  // $Id$

    require_once('../../../../../config.php');

    if (empty($CFG->hivehost) or empty($CFG->hiveport) or empty($CFG->hiveprotocol) or empty($CFG->hivepath)) {
        print_header();
        notify('A Hive repository is not yet configured in Moodle.  Please see Resource settings.');
        print_footer();
        die;
    }

    print_header();
    
    $aliasid   = optional_param('aliasid', '', PARAM_RAW);
    $latest    = optional_param('lastes', '', PARAM_RAW);
    $itemid    = optional_param('itemid', '', PARAM_RAW);
    $format    = optional_param('format', '', PARAM_RAW);
    $filename  = optional_param('filename', '', PARAM_RAW);
    $title     = optional_param('title', '', PARAM_RAW);
    
    /// Generate the HIVE_REF parameter
    $hive_ref = 'HIVE_REF=hii%3A'. $aliasid;
    if ($latest != 'Y') {
        $hive_ref = 'HIVE_REF=hdi%3A'. $itemid;
    }
    /// Generate the HIVE_RET parameter
    $hive_ret = 'HIVE_RET=ORG';
    if ($format != 'orig') {
        $hive_ret = 'HIVE_RET=CNV';
    }

    $reference = $hive_ref.'&amp;'. $hive_ret .'&amp;HIVE_REQ=2113';

    $resource =  $CFG->hiveprotocol .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath . '/'.$filename.'?'. $reference;

    
?>

<script type="text/javascript">
//<![CDATA[
    opener.document.getElementById['id_reference'].value = '<?php echo addslashes_js($resource) ?>';
    opener.document.getElementById['id_name'].value = '<?php echo addslashes_js($title) ?>';
    opener.focus();
    window.close();
//]]>
</script>


