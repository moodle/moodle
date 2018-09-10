<?php
require_once('query.php');

/**
 * Retorna un elemento html fieldset con los campos encontrados en la consulta  
 * realizada por el metodo getRiskList()
 *  <fieldset id="riesgo">
        <legend>Riesgo</legend>
        <input type="checkbox" name="chk_risk[]" value="academic_risk">Académico<br>
        <input type="checkbox" name="chk_risk[]" value="social_risk">Socioeducativo<br>
        .
        .
        .
    </fieldset>
    @author Edgar Mauricio Ceron
 */
$array = getRiskList();
$html = '<fieldset id="riesgo">
                 <legend>Riesgo</legend>';
foreach($array as $riesgo){
        $value = $riesgo->nombre;
        $label = $riesgo->descripcion;
        $input = '<input type="checkbox" name="chk_risk[]" value="'.$value.'">'.$label.'<br>';
        $html = $html.$input;
}
$html = $html."</fieldset>";
echo $html;
//echo json_encode($html);