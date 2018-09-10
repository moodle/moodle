<?php
require('query.php');
if(isset($_POST['cohorte']) && isset($_POST['idinstancia'])){
    $result = new stdClass();
    $result->cohorts = getConcurrentCohortsSPP($_POST['idinstancia']);
    $result->enfasis = getConcurrentEnfasisSPP();
    echo json_encode($result);
}else{
    echo 'no';
}
?>