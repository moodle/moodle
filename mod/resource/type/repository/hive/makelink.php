<?php  // $Id$

    require_once('../../../../../config.php');

    print_header();
    
    $urlparts = parse_url($FULLME);
    
    $parts = explode('&', $urlparts['query']);
    
    foreach ($parts as $part) {
        $part = explode('=', $part);
        $$part[0] = $part[1];
    }
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
    $resource = $hive_ref.'&amp;'. $hive_ret .'&amp;HIVE_REQ=2113';

    
?>

<script language="javascript" type="text/javascript">
<!--
    opener.document.forms['form'].reference.value = '<?php echo addslashes($resource) ?>';
    opener.document.forms['form'].name.value = '<?php echo addslashes(urldecode($title)) ?>';
    opener.focus();
    window.close();
-->
</script>


