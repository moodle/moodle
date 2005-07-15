<?php  // $Id$

    require_once('../../../../../config.php');

    print_header();
    
    $urlparts = parse_url($FULLME);
    
    $parts = explode('&', $urlparts['query']);
    
    foreach ($parts as $part) {
        $part = explode('=', $part);
        $$part[0] = $part[1];
    }
    
    $resource = 'HIVE_REF=hii%3A'. $aliasid .'&amp;HIVE_RET=ORG&amp;HIVE_REQ=2001';
?>

<script language="javascript" type="text/javascript">
<!--
    opener.document.forms['form'].reference.value = '<?php echo addslashes($resource) ?>';
    window.close();
-->
</script>

