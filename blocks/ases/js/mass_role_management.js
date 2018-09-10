$(document).ready(function (argument) {
    var val = $('#selector').val();
    addHelpMessage(val);
     $('#selector').on('change', function () {
        var val = $('#selector').val();
        addHelpMessage(val);
     });
     
    $('#boton_subir').on('click', function(){
        $('#informacion').empty();
        uploadFile();
    });
});

function uploadFile() {
    
    var urlParameters =  getUrlParams(document.location.search); //metodo definido en checrole
    
    var formData = new FormData();
    
    formData.append('idinstancia',urlParameters.instanceid);
    formData.append('file', $('#archivo')[0].files[0]);
    
    var controler = '';
    
    switch ($('#selector').val()) {
        case 'seguimiento':
            controler = 'mrm_seguimiento.php';
            break;
        case 'seguimiento_estudiante':
            controler = 'mrm_seg_estud.php'; //msm_monitor_estud
            break;
        case 'monitor_estud':
            controler = 'mrm_monitor_estud.php'; //
            break;
        case 'roles_usuario':
            controler = 'mrm_roles.php'; //
            break;
        default:
            return 0;
    }

    
    $.ajax({
        url: '../managers/'+controler,
        data: formData,
        type: 'POST',
        dataType: 'json',
        cache: false,
        // parametros necesarios para la carga de archivos
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#response').html("<img src='../icon/facebook.gif' />");
        },
        success : function (msj) {
             $('#response').empty();
            
            $('#informacion').empty();
            
            if(msj.success){
                $('#informacion').append('<div class="alert alert-success"><h4 align="center">Información</h4><strong>Exito!</strong> <br><p>'+msj.success+'</p></div>');
            }else if(msj.warning){
                $('#informacion').append('<div class="alert alert-warning"><h4 align="center">Información</h4><strong>Cargado con inconsitencias!</strong> <br>'+msj.warning+'</div>');
            }else if(msj.error){
                $('#informacion').append('<div class="alert alert-danger"><h4 align="center">Información</h4><strong>Error!</strong> <br>'+msj.error+'</div>');
            }
            
            $('#informacion').append(msj.urlzip);
            //console.log(msj);
        },
        error: function (msj) {
            console.log(msj);
        }
        // ... Other options like success and etc
    });
     
}

function addHelpMessage(selector){
    $('#informacion').empty();
    switch (selector) {
        case 'monitor_estud':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username_monitor</li><li>username_estudiante</li> </ul> </p></div>');
            break;
        case 'roles_usuario':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username</li><li>rol(administrativo, reportes,profesional_ps, monitor_ps,  estudiante_t ó practicante_psp)</li> </ul> </p><p>Columnas aceptadas: <ul> <li>jefe</li>  </ul> </p></div>');
            break;
        case 'status':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username</li> <li>Estado Ases</li> <li>Estado Icetex</li> <li>Estado de Programa</li><li>Tracking Status</li> </ul> </p></div>');
            break;
        default:
            // code
    }
}


