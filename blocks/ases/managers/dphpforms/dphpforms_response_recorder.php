<?php 

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../config.php');

    function dphpforms_generate_html_recorder($id_form, $rol_, $student_id, $id_monitor){

        global $DB;

        $FORM_ID = $id_form;
        $ROL = $rol_;

        $html = null;

        if(!is_numeric($id_form)){
            $sql_alias = "SELECT id FROM {talentospilos_df_formularios} WHERE alias = '$id_form' AND estado = 1";
            $form_record = $DB->get_record_sql($sql_alias);
            if($form_record){
                $FORM_ID = $form_record->id;
            }
        }

        if(!is_numeric($FORM_ID)){
            return '';
        }

        
        $sql = '
        
            SELECT * FROM {talentospilos_df_tipo_campo} AS TC 
            INNER JOIN (
                SELECT * FROM {talentospilos_df_preguntas} AS P 
                INNER JOIN (
                    SELECT *, F.id AS mod_id_formulario, FP.id AS mod_id_formulario_pregunta FROM {talentospilos_df_formularios} AS F
                    INNER JOIN {talentospilos_df_form_preg} AS FP
                    ON F.id = FP.id_formulario WHERE F.id = '.$FORM_ID.'
                    ) AS AA ON P.id = AA.id_pregunta
                ) AS AAA
            ON TC.id = AAA.tipo_campo
            ORDER BY posicion
        
        ';

        $result = $DB->get_records_sql($sql);
        $result = (array) $result;
        $result = array_values($result);

        $row = $result[0];
        $form_name = $row->{'nombre'};
        $form_name_formatted = strtolower($form_name);
        $form_name_formatted = str_replace(" ", "_", $form_name_formatted);
        $form_name_formatted = str_replace("   ", "_", $form_name_formatted);
        $form_name_formatted = str_replace(' ', "_", $form_name_formatted);
        $form_name_formatted = str_replace("á", "a", $form_name_formatted);
        $form_name_formatted = str_replace("é", "e", $form_name_formatted);
        $form_name_formatted = str_replace("í", "i", $form_name_formatted);
        $form_name_formatted = str_replace("ó", "o", $form_name_formatted);
        $form_name_formatted = str_replace("ú", "u", $form_name_formatted);
        $form_name_formatted = str_replace("ü", "u", $form_name_formatted);
        $form_name_formatted = str_replace("ñ", "n", $form_name_formatted);
        $form_name_formatted = utf8_encode($form_name_formatted);
        $form_name_formatted = $form_name_formatted . "_" . $row->{'mod_id_formulario'};


        $html = $html .  '<form id="'. $form_name_formatted .'" method="'. $row->{'method'} .'" action="'. $row->{'action'} .'" class="dphpforms dphpforms-response col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:0.7em">' ;
        $html = $html .  '<h1>'.$form_name.'</h1><hr style="border-color:red;">';
        $html = $html .  '<input name="id" value="'.$row->{'mod_id_formulario'}.'" style="display:none;">';
        
        for($i = 0; $i < count($result); $i++){
            $row = null;
            $row = $result[$i];

            $campo = $row->{'campo'};
            $enunciado = $row->{'enunciado'};
            
            $atributos = json_decode($row->{'atributos_campo'});

            //Consulta de permisos
            $sql_permisos = '
                SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '.$row->{'id_pregunta'}.'
            ';
            
            $result_permisos = $DB->get_record_sql($sql_permisos);

            $permisos = $result_permisos;
            $permisos_JSON = json_decode($permisos->permisos);
            
            foreach ($permisos_JSON as $key => $v_rol) {

            
                if($v_rol->{'rol'} == $ROL){

                    $lectura = false;
                    $escritura = false;

                    foreach ($v_rol->{'permisos'} as $key2 => $value) {
                        if($value == "lectura"){
                            $lectura = true;
                        }
                        if($value == "escritura"){
                            $escritura = true;
                        }

                    }

                    if($lectura){

                        $enabled = null;
                        if(!$escritura){
                            $enabled = "disabled";
                        }

                        $field_attr_class = '';
                        $field_attr_type = '';
                        $field_attr_placeholder = '';
                        $field_attr_maxlength = '';
                        $field_attr_inputclass = '';
                        $field_attr_required = '';
                        $field_attr_local_alias = '';
                        $field_attr_max = '';
                        $field_attr_min = '';

                        if(property_exists($atributos, 'class')){
                            $field_attr_class = $atributos->{'class'};
                        }

                        if(property_exists($atributos, 'type')){
                            $field_attr_type = $atributos->{'type'};
                        }

                        if(property_exists($atributos, 'placeholder')){
                            $field_attr_placeholder = $atributos->{'placeholder'};
                        }

                        if(property_exists($atributos, 'maxlength')){
                            $field_attr_maxlength = $atributos->{'maxlength'};
                        }

                        if(property_exists($atributos, 'inputclass')){
                            $field_attr_inputclass = $atributos->{'inputclass'};
                        }

                        if(property_exists($atributos, 'required')){
                            $field_attr_required = $atributos->{'required'};
                            if($field_attr_required == 'true'){
                                $field_attr_required = 'required';
                            }elseif($field_attr_required == 'false'){
                                $field_attr_required = '';
                            }
                        }

                        if(property_exists($atributos, 'local_alias')){
                            $field_attr_local_alias = $atributos->{'local_alias'};
                        }

                        if(property_exists($atributos, 'max')){
                            $field_attr_max = $atributos->{'max'};
                            if( $field_attr_max == "today()" ){
                                $today = new DateTime('now');
                                $field_attr_max = $today->format('Y-m-d');
                            }
                        }

                        if(property_exists($atributos, 'min')){
                            $field_attr_min = $atributos->{'min'};
                            if( $field_attr_min == "today()" ){
                                $today = new DateTime('now');
                                $field_attr_max = $today->format('Y-m-d');
                            }
                        }

                        if($campo == 'TEXTFIELD'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="'.$field_attr_type.'" placeholder="'.$field_attr_placeholder.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="" maxlength="'.$field_attr_maxlength.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'TEXTAREA'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <textarea id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" name="'. $row->{'mod_id_formulario_pregunta'} .'" maxlength="'.$field_attr_maxlength.'" '.$enabled.' '.$field_attr_required.'></textarea><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'DATE'){
                            $html = $html . '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html . ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="date" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html . '</div>';
                        }
                        
                        if($campo == 'DATETIME'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="datetime-local" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'TIME'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="time" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'RADIOBUTTON'){
                            $opciones = json_decode($row->{'opciones_campo'});
                            $array_opciones = (array)$opciones;
                            $number_opciones = count($array_opciones);

                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >';
                            $html = $html .  '<input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="-#$%-">';
                            if($enunciado){
                                $html = $html . '<label>'.$enunciado.'</label>';
                            }

                            $field_attr_radioclass = '';
                            if(property_exists($atributos, 'radioclass')){
                                $field_attr_radioclass = $atributos->{'radioclass'};
                            }

                            /*
                                Se utiliza para controlar el registro de una sola
                                condición de required para el primer radio.
                            */
                            $required_temporal = $field_attr_required;

                            $field_attr_group_radio_class = '';
                            if(property_exists($atributos, 'groupradioclass')){
                                $field_attr_group_radio_class = $atributos->{'groupradioclass'};
                            }
                                              
                            $html = $html .  '<div class="opcionesRadio ' .  $field_attr_group_radio_class . '" style="margin-bottom:0.4em">';
                            for($x = 0; $x < $number_opciones; $x++){
                                $opcion = (array) $array_opciones[$x];

                                $html = $html .  '
                                    <div id="'.$row->{'mod_id_formulario_pregunta'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" class="radio ' . $field_attr_radioclass . '">
                                        <label><input type="radio" class=" ' . $field_attr_inputclass . '" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" name="optradio" '.$enabled.'   ' . $required_temporal . '>'.$opcion['enunciado'].'</label>
                                    </div>
                                ' . "\n";
                                /*
                                    Si el grupo de radios es requerido y ya se ha puesto esa condición en el 
                                    primer radio, a pesar de que se concatene la variable al input, se limpia después
                                    de pintar el primer radio.
                                */
                                if(  $required_temporal != ''  ){
                                    $required_temporal = '';
                                }
                            }
                            $html = $html .  '</div><a href="javascript:void(0);" class="limpiar btn btn-xs btn-default" >Limpiar</a>
                             </div>
                            ' . "\n";
                        }

                        if($campo == 'CHECKBOX'){
                            $opciones = json_decode($row->{'opciones_campo'});
                            $array_opciones = (array)$opciones;
                            $number_opciones = count($array_opciones);

                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >';
                            if($enunciado){
                                $html = $html . '<label>'.$enunciado.'</label>';
                            }

                            $field_attr_checkclass = '';
                            if(property_exists($atributos, 'checkclass')){
                                $field_attr_checkclass = $atributos->{'checkclass'};
                            }

                            $name_checkbox = $row->{'mod_id_formulario_pregunta'};
                            if($number_opciones > 1){
                                $name_checkbox = $row->{'mod_id_formulario_pregunta'} . '[]';
                            }

                            for($x = 0; $x < $number_opciones; $x++){
                                $opcion = (array) $array_opciones[$x];
                                $html = $html . '<div class="checkbox ' . $field_attr_checkclass . '">' . "\n";

                                $option_attr_checkclass = '';
                                if(array_key_exists('class', $opcion)){
                                    $option_attr_checkclass = $opcion['class'];
                                }

                                if($number_opciones == 1){
                                    $html = $html . '   <input type="hidden" name="'. $name_checkbox .'" value="-1">' . "\n";
                                }

                                $html = $html . '   <label class="' . $option_attr_checkclass . '" ><input type="checkbox" class="' . $field_attr_inputclass . '" name="'. $name_checkbox .'" value="'.$opcion['valor'].'" '.$enabled.'>'.$opcion['enunciado'].'</label>' . "\n";
                                $html = $html . '</div>';
                                $html = $html . '' . "\n";
                            }
                            $html = $html . '</div>';

                        }

                    }

                    break;

                }
            }

        }
        $html = $html .  ' <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;"> <hr style="border-color:red"></div><button type="submit" class="btn btn-sm btn-danger btn-dphpforms-univalle btn-dphpforms-sendform">Registrar</button> <a href="javascript:void(0);" class="btn btn-sm btn-danger btn-dphpforms-univalle btn-dphpforms-close">Cerrar</a>' . "\n";
        $html = $html .  ' </form>' . "\n";

        return $html;

    }
   
?>