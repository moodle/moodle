
<?php
    /**
    * Accion generada por el generador de codigo de moodle para la 
    * Estrategia Ases de la Universidad del Valle
    * @author Edgar Mauricio Ceron Florez
    */
    require_once(dirname(__FILE__). '/../../../config.php');
    require('validate_profile_action.php');
    $accion = '6';
    global $USER;
    $id_instancia = $_POST['instance'];
    $moodle_id = $USER->id; 
    $user_id = get_talentos_id($moodle_id);
    $perfil = get_perfil_usuario($user_id, $id_instancia);
    
    if(validar_permisos($perfil, $accion)){
        if(isset($_POST['nombre'])){
            $nombre = $_POST['nombre'];
            $nombre_archivo = $nombre.".php";
            
            $nombre_view = $nombre.".php";
            $nombre_mustache = $nombre.".mustache";
            $nombre_output = $nombre."_page.php";
            
            $ruta_view = dirname(__FILE__)."/../../view/";
            $ruta_mustache = dirname(__FILE__)."/../../templates/";
            $ruta_output = dirname(__FILE__)."/../../classes/output/";
            
            $flag_archivos = (
                !existeArchivo($ruta_view, $nombre_view) && 
                !existeArchivo($ruta_mustache, $nombre_mustache)&&
                !existeArchivo($ruta_output, $nombre_output)    
            );
            
            //////////////////////////////////
            
            $record = new stdClass;
            $record->nombre_accion = $nombre;
            $record->descripcion = "Accion para entrar a la vista ".$nombre;
            $record->estado = true;
            
            $sql_query = "SELECT * FROM {talentospilos_accion} WHERE nombre_accion = '".$record->nombre_accion."'";
            $accion = $DB->get_record_sql($sql_query);
            $repetido = false;
            
            if($accion->nombre_accion){
                $repetido = true;
            }
            //////////////////////////////////
            
            if($flag_archivos && !$repetido){
                
                $id_nueva_accion = $DB->insert_record('talentospilos_accion', $record, true); 
                
                $contenido_view = 
                "
                <?php
                    /**
                    * Ases block
                    * @author     Edgar Mauricio Ceron Florez
                    * @author     ESCRIBA AQUI SU NOMBRE
                    * @package    block_ases
                    * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                    */
                    
                    require_once(__DIR__ . '/../../../../../config.php');
                    require_once($"."CFG->libdir.'/adminlib.php');
                    require('/../../managers/view_management/validate_profile_action.php');
                    include('/../../lib.php');
                    global $"."PAGE;
                    
                    include('../../classes/output/".$nombre."_page.php');
                    include('../../classes/output/no_tiene_permisos_page.php');
                    include('../../classes/output/renderer.php');
                    $"."title = 'INGRESE AQUI EL TITULO DE LA PAGINA';
                    $"."pagetitle = $"."title;
                    $"."courseid = required_param('courseid', PARAM_INT);
                    $"."blockid = required_param('instanceid', PARAM_INT);
                    
                    require_login($"."courseid, false);
                    $"."contextcourse = context_course::instance($"."courseid);
                    $"."contextblock =  context_block::instance($"."blockid);
                    $"."url = new moodle_url('/blocks/ases/view/".$nombre.".php',array('courseid' => $"."courseid, 'instanceid' => $"."blockid));
                    
                    //Configuracion de la navegacion
                    $"."coursenode = $"."PAGE->navigation->find($"."courseid, navigation_node::TYPE_COURSE);
                    $"."blocknode = navigation_node::create('INGRESE AQUI TITULO DE NAVEGACION',$"."url, null, 'block', $"."blockid);
                    $"."coursenode->add_node($"."blocknode);
                    $"."blocknode->make_active();
                    
                    $"."PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
                    $"."PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
                    $"."PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
                    $"."PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
                    $"."PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
                    $"."PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
                    $"."PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
                    $"."PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
                    
                    $"."PAGE->requires->js('/blocks/ases/js/jquery.validate.min.js', true);
                    $"."PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
                    $"."PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
                    $"."PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);
                    $"."PAGE->requires->js('/blocks/ases/js/npm.js', true);
                    $"."PAGE->set_url($"."url);
                    $"."PAGE->set_title($"."title);
                    
                    $"."PAGE->set_heading($"."title);
                    $"."output = $"."PAGE->get_renderer('block_ases');
                    echo $"."output->header();
                    
                    //VALIDACIÓN DE PERMISOS
                    $"."accion = '".$id_nueva_accion."';
                    global $"."USER;
                    $"."id_instancia = $"."_GET['instanceid'];
                    $"."moodle_id = $"."USER->id; 
                    $"."user_id = get_talentos_id($"."moodle_id);
                    $"."perfil = get_perfil_usuario($"."user_id, $"."id_instancia);
                    if(validar_permisos($"."perfil, $"."accion)){
                        $".$nombre."_page = new \block_ases\output"."\\".$nombre."_page('Some text');
                        echo $"."output->render($".$nombre."_page);
                    }
                    else{
                        $".$nombre.'_page = new \block_ases\output\no_tiene_permisos_page("Some text");'."
                        echo $"."output->render($".$nombre."_page);
                    }
                    echo $"."output->footer();
                ";
                
                $contenido_mustache = 
                "
                <body name=\"$nombre\">
                    <div class=\"container\">
                    
                    </div>
                </div>
                ";
                
                $contenido_output =
                "<?php
                /**
                * Talentos Pilos
                *
                * @author     Edgar Mauricio Ceron Florez
                * @author     ESCRIBA AQUI SU NOMBRE
                * @package    block_ases
                * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                */
                
                
                // Standard GPL and phpdocs
                namespace block_ases\output;                                                                                                       
                 
                use renderable;                                                                                                                
                use renderer_base;                                                                                                                  
                use templatable;                                                                                                                    
                use stdClass;       
                
                class ".$nombre."_page implements renderable, templatable {                                                                               
                    /** @var string $"."sometext Some text to show how to pass data to a template. */                                                  
                    var $"."sometext = null;                                                                                                           
                 
                    public function __construct($"."sometext) {                                                                                        
                        $"."this->sometext = $"."sometext;                                                                                                
                    }
                 
                    /**                                                                                                                             
                     * Export this data so it can be used as the context for a mustache template.                                                   
                     *                                                                                                                              
                     * @return stdClass                                                                                                             
                     */                                                                                                                             
                    public function export_for_template(renderer_base $"."output) {                                                                    
                        $"."data = new stdClass();                                                                                                     
                        $"."data->sometext = $"."this->sometext;                                                                                          
                        return $"."data;                                                                                                               
                    }
                }
                ";
                
                $content_renderer = "
                public function render_".$nombre."_page($"."page){
                    $"."data = $"."page->export_for_template($"."this);
                    return parent::render_from_template('block_ases/".$nombre."', $"."data);
                }
                ";
                
                crearArchivo($ruta_view, $nombre_view, $contenido_view);
                crearArchivo($ruta_mustache, $nombre_mustache, $contenido_mustache);
                crearArchivo($ruta_output, $nombre_output, $contenido_output);
                echo "Generados los archivo: ".$nombre_view.", ".$nombre_mustache.", ".$nombre_output;
                echo "\nPegue en el archivo /clases/output/renderer.php el siguiente codigo:\n".$content_renderer;
            }
            else{
                echo "Ya existe uno o varios archivos con este nombre";
            }
        }
        else{
            echo "Nombre de archivo no fue pasado como parametro de POST";
        }
    }
    else{
        echo "Usted no tiene permisos para realizar esta accion";
    }
    
    
    
    /**
     * Verifica si existe un archivo con el nombre ingresado en el directorio
     * especificado
     * @param $ruta String con la ruta a comprobar
     * @param $nombre String con el nombre del archivo 
     * @return boolean true si ya existe un archivo con el nombre ingresado, de
     * lo contrario devuelve false
     */ 
    
    function existeArchivo($ruta, $nombre){
        $archivos_view = scandir($ruta);
        foreach($archivos_view as $archivo){
            if($archivo == $nombre){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Crea un archivo en la ruta dada, con el nombre ingresado y el contenido
     * especificado.
     * @param $ruta String con la ruta del archivo
     * @param $nombre Nombre que se le asignara al archivo
     * @param $contenido String con el contenido del archivo
     */
     
    function crearArchivo($ruta, $nombre, $contenido){
        $archivo = fopen($ruta.$nombre, "w") or die("Unable to open file!");
        fwrite($archivo, $contenido);
        fclose($archivo);
    }