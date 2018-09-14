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
require_once('dphpforms_form_updater.php');

function dphpforms_store_form($form_JSON){

    $json_obj_form = $form_JSON;
    
    $form_db_id = null; 
    $form_details = array(
        'nombre' => $json_obj_form->{'datos_formulario'}->{'nombre'},
        'descripcion' => $json_obj_form->{'datos_formulario'}->{'descripcion'},
        'method' => $json_obj_form->{'datos_formulario'}->{'method'},
        'action' => $json_obj_form->{'datos_formulario'}->{'action'},
        'enctype' => $json_obj_form->{'datos_formulario'}->{'enctype'},
        'alias' => $json_obj_form->{'datos_formulario'}->{'alias'}
    );

    $form_db_id = dphpforms_store_form_details($form_details);

    $identifiers_preguntas = array();
    foreach ($json_obj_form->{'preguntas'} as &$pregunta) {
        $pregunta_details = array(
            'tipo_campo' => $pregunta->{'tipo_campo'},
            'opciones_campo' => $pregunta->{'opciones_campo'},
            'atributos_campo' => $pregunta->{'atributos_campo'},
            'enunciado' => $pregunta->{'enunciado'},
            'permisos_campo' => $pregunta->{'permisos_campo'} 
        );
        array_push(
            $identifiers_preguntas, 
            array( 
                'idPreguntaDB' => dphpforms_store_pregunta($pregunta_details),
                'idPreguntaTemporal' => $pregunta->{'id_temporal'},
                'permisosCampo' => json_encode($pregunta->{'permisos_campo'})
            )
        );
    }

    $identifiers_form_preguntas = array();
    foreach ($identifiers_preguntas as $key => $identifiers_pregunta) {
        array_push($identifiers_form_preguntas,
            array(
                'idRelacionFormPreg' => dphpforms_store_form_pregunta($form_db_id, $identifiers_pregunta['idPreguntaDB'], $key),
                'idPreguntaDB' => $identifiers_pregunta['idPreguntaDB'],
                'idPreguntaTemporal' => $identifiers_pregunta['idPreguntaTemporal']
            )
        );
    }

    if(property_exists($json_obj_form, 'campos_busqueda')){
        dphpforms_store_preg_alias($identifiers_form_preguntas, $json_obj_form->{'campos_busqueda'});
    }

    if(property_exists($json_obj_form, 'actualizador_orden')){
        dphpforms_update_pregunta_position_new_form($identifiers_form_preguntas, $json_obj_form->{'actualizador_orden'});
    }
    

    $identifiers_reglas = array();
    if(property_exists($json_obj_form, 'reglas')){
        foreach ($json_obj_form->{'reglas'} as &$regla) {
            $identifier_pregunta_A = null;
            $identifier_pregunta_B = null;
           
            for($i = 0; $i < count($identifiers_form_preguntas); $i++){
                
                if($identifiers_form_preguntas[$i]['idPreguntaTemporal'] == $regla->{'id_temporal_campo_a'}){
                    $identifier_pregunta_A = $identifiers_form_preguntas[$i]['idRelacionFormPreg'];
                }
    
                if($identifiers_form_preguntas[$i]['idPreguntaTemporal'] == $regla->{'id_temporal_campo_b'} ){
                    $identifier_pregunta_B = $identifiers_form_preguntas[$i]['idRelacionFormPreg'];
                }
    
                if(($identifier_pregunta_A != null)&&($identifier_pregunta_B != null)){
                    break;
                }
            }
            
            array_push($identifiers_reglas, dphpforms_store_form_regla($form_db_id, $regla->{'regla'}, $identifier_pregunta_A, $identifier_pregunta_B));
        }
    }

    $identifiers_disparadores = dphpforms_store_form_disparadores($form_db_id, $json_obj_form->{'disparadores'}, $identifiers_form_preguntas);
    if(!$identifiers_disparadores){
        echo json_encode(
            array(
                'id_formulario' => '-1',
                'mensaje_error' => 'ERROR REGISTRANDO DISPARADORES'
            )
        );
        die();
    }

    echo json_encode(
        array(
            'id_formulario' => $form_db_id,
            'mensaje_error' => ''
        )
    );
    
}

function dphpforms_store_form_details($form_details){

    global $DB;
     
    $obj_form_details = new stdClass();
    $obj_form_details->nombre = $form_details['nombre'];
    $obj_form_details->descripcion = $form_details['descripcion'];
    $obj_form_details->method = $form_details['method'];
    $obj_form_details->action = $form_details['action'];
    $obj_form_details->enctype = $form_details['enctype'];
    $obj_form_details->alias = $form_details['alias'];

    $form_id = $DB->insert_record('talentospilos_df_formularios', $obj_form_details, $returnid=true, $bulk=false) ;
    return $form_id;
}

function dphpforms_store_pregunta($pregunta_details){

    global $DB;

    $result = null;
    $sql = "SELECT * FROM {talentospilos_df_tipo_campo}";
    $result = $DB->get_records_sql($sql);
    $result = (array) $result;
    
    $fields = array();
    $result = array_values($result);
    if(count($result) > 0){
        for($i = 0; $i < count($result); $i++){
            $row = $result[$i];
            array_push($fields, array('id' => $row->id, 'campo' => $row->campo));
        }
    }

    foreach($fields as &$field){
        if(in_array($pregunta_details['tipo_campo'], $field)){
            $pregunta_details['tipo_campo'] = (int) $field['id'];
        };
    };    

    $obj_pregunta = new stdClass();
    $obj_pregunta->tipo_campo = $pregunta_details['tipo_campo'];
    $obj_pregunta->opciones_campo = json_encode($pregunta_details['opciones_campo']);
    $obj_pregunta->atributos_campo = json_encode($pregunta_details['atributos_campo']);
    $obj_pregunta->enunciado = $pregunta_details['enunciado'];

    $pregunta_identifier = $DB->insert_record('talentospilos_df_preguntas', $obj_pregunta, $returnid=true, $bulk=false);

    $obj_permisos_formulario_pregunta = new stdClass();
    $obj_permisos_formulario_pregunta->id_formulario_pregunta = $pregunta_identifier;
    $obj_permisos_formulario_pregunta->permisos = json_encode($pregunta_details['permisos_campo']);

    $permission_identifier = $DB->insert_record('talentospilos_df_per_form_pr', $obj_permisos_formulario_pregunta, $returnid=true, $bulk=false);

    /*if($permission_identifier){
        echo " PERMISO REGISTRADO. ";
    }*/
    
    return $pregunta_identifier;
}

function dphpforms_store_form_pregunta($form_id, $identifier_pregunta, $position){
    
    global $DB;

    $obj_form_preguntas = new stdClass();
    $obj_form_preguntas->id_formulario = $form_id;
    $obj_form_preguntas->id_pregunta = $identifier_pregunta;
    $obj_form_preguntas->posicion = $position;

    $idRelacion = $DB->insert_record('talentospilos_df_form_preg', $obj_form_preguntas, $returnid=true, $bulk=false);

    //$identifier_permission = dphpforms_store_form_pregunta_permits($idRelacion, $permits);
    if(!$idRelacion){
        echo json_encode(
            array(
                'id_formulario' => '-1',
                'mensaje_error' => 'ERROR REGISTRANDO PREGUNTA'
            )
        );
        die();
    }

    return $idRelacion;

}

function dphpforms_store_form_regla($form_id, $text_rule, $identifier_pregunta_A, $identifier_pregunta_B){
    
    global $DB;

    $identifier_regla = null;

    $text_rule_ = $text_rule;

    if($text_rule_ == 'LESS_THAN'){
        $text_rule_ = "<";
    }

    if($text_rule_ == 'GREATER_THAN'){
        $text_rule_ = ">";
    }

    $sql = "SELECT * from {talentospilos_df_reglas} WHERE regla = '".$text_rule_."'";
    $result = $DB->get_record_sql($sql);
    $identifier_regla = $result->id;

    $obj_regla_form_pregunta = new stdClass();
    $obj_regla_form_pregunta->id_formulario         = $form_id;
    $obj_regla_form_pregunta->id_regla              = $identifier_regla;
    $obj_regla_form_pregunta->id_form_pregunta_a    = $identifier_pregunta_A;
    $obj_regla_form_pregunta->id_form_pregunta_b    = $identifier_pregunta_B;

    $regla_identifier = $DB->insert_record('talentospilos_df_reg_form_pr', $obj_regla_form_pregunta, $returnid=true, $bulk=false);

    return $regla_identifier;

}

function dphpforms_store_form_pregunta_permits($form_id_pregunta, $permits){
    
    global $DB;

    $obj_permisos_formulario_pregunta = new stdClass();
    $obj_permisos_formulario_pregunta->id_formulario_pregunta = $form_id_pregunta;
    $obj_permisos_formulario_pregunta->permisos = $permits;

    $identifier_permission = $DB->insert_record('talentospilos_df_per_form_pr', $obj_permisos_formulario_pregunta, $returnid=true, $bulk=false);
 
    return $identifier_permission;
}

function dphpforms_store_form_disparadores($form_id, $disparadores, $identifiers_form_preguntas){

    global $DB;
    $disparadores_string = json_encode($disparadores);
    foreach ($identifiers_form_preguntas as &$value) {
        $disparadores_string = str_replace('"'.$value['idPreguntaTemporal'].'"', '"'.$value['idRelacionFormPreg'].'"', $disparadores_string);
    }


    $obj_disparadores_permisos_formulario_diligenciado = new stdClass();
    $obj_disparadores_permisos_formulario_diligenciado->id_formulario = $form_id;
    $obj_disparadores_permisos_formulario_diligenciado->disparadores = $disparadores_string;

    $identifier_disparador = $DB->insert_record('talentospilos_df_disp_fordil', $obj_disparadores_permisos_formulario_diligenciado, $returnid=true, $bulk=false);
    return $identifier_disparador;
}

function dphpforms_store_preg_alias($identifiers_form_preguntas, $alias){

    global $DB;

    $alias_string = json_encode($alias);

    foreach ($identifiers_form_preguntas as &$value) {
        $alias_string = str_replace('"'.$value['idPreguntaTemporal'].'"', '"'.$value['idRelacionFormPreg'].'"', $alias_string);
    }

    $json_alias = json_decode($alias_string);

    foreach ($json_alias as &$value) {
        
        $obj_alias = new stdClass();
        $obj_alias->id_pregunta = $value->{'id_campo'};
        $obj_alias->alias = $value->{'alias'};

        $DB->insert_record('talentospilos_df_alias', $obj_alias, $returnid=true, $bulk=false);
    }

}

function dphpforms_update_pregunta_position_new_form($identifiers_form_preguntas, $update_json){

    $updated_info = json_encode($update_json);

    foreach ($identifiers_form_preguntas as &$value) {
        $updated_info = str_replace('"'.$value['idPreguntaTemporal'].'"', '"'.$value['idRelacionFormPreg'].'"', $updated_info);
    }

    $updated_info = json_decode($updated_info);

    foreach ($updated_info as &$value) {
        update_pregunta_position($value->{'id_temporal'}, (int) $value->{'nueva_posicion'});
    }
}

?>