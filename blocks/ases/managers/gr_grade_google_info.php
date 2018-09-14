<?php
if(isset($_POST['course'])){

        $página_inicio = file_get_contents('https://docs.google.com/spreadsheets/d/1uwI9wVYQLwuz8ChxFCMUCiAlPNMsu-rV77wj9UQRK14/pubhtml');
        echo json_encode($página_inicio);
    }