
<?php
    /**
    * Accion generada por el generador de codigo de moodle para el 
    * programa de talentos pilos de la universidad del valle
    * @author Edgar Mauricio Ceron Florez
    * @author ESCRIBA AQUI SU NOMBRE */
    require_once(dirname(__FILE__). '/../../../config.php');
    require_once('permissions_management/permissions_lib.php');
    require('validate_profile_action.php');

    function get_permission(){

      global $USER;
      $message = '';
      $continue = true;
      $accion = '41';

      $id_instancia =required_param('instanceid', PARAM_INT);
      $moodle_id = $USER->id; 
      $userrole = get_id_rol($USER->id,$id_instancia);


      // Se obtiene la URL actual.

      $url = $_SERVER['REQUEST_URI'];
      $aux_function_name=explode('/', $url);


      // obtiene nombre de la vista actual.

      $function_name=explode('.php',$aux_function_name[5])[0];


      // Obtiene obj de la acción.

      $action =get_action_by_id($accion);

      /*(nombre de la vista es igual al nombre de la funcionalidad).*/

      $functionality= get_functions_by_name($function_name);

      if($functionality){

        $exist=is_action_in_functionality($accion,$functionality->id);

        if(!$exist){
          $message = 'No existe relación entre la acción y la funcionalidad especificada.
          acción :  '.$action->nombre_accion.' and funcionalidad : '.$function_name;
          return $message;

        }else{

          // Verifica que el rol del usuario pueda realizar dicha acción.


        try{
           $is_able = role_is_able($userrole,$accion);

        }catch(Exception $ex){
           $message = 'Debe conectarse para visualizar la página';
           return $message;
        }

        if(!$is_able){
           $message = 'el usuario conectado no puede realizar dicha acción';
           return $message;
        }else{

          // Obtiene todas las acciones a las cuales el rol puede acceder de dicha funcionalidad y las guarda en un arreglo.


          $actions_per_func=get_actions_by_role($functionality->id,$userrole);
          return $actions_per_func;
        }
      }
    }
  }

