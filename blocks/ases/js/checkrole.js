//url[array]  contiene el valor de los parametros de la url se accede a cada uno de la forma url["nombreParametro"]  
var url = getUrlParams();

$(document).ready(function() {
  checkpermision();
});

var checkpermision = function(options) {
  // Define default options.
  var defaults = {
    url: document.location, // % is filled with the field's ID, allowing for multiple Smart Suggests per page
  };
  
  // Merge defaults with user-defined options.
  var options = $.extend(defaults, options);
  
  var url = options.url;
  
  //se obtiene los parametros de la url
  var parameters = getUrlParams(url.search);

	var indentifierl = url.pathname;
	
	if (indentifierl.includes("talentos_profile.php")){
    verificarPermisosFicha(parameters);
  }else if(indentifierl.includes("index.php")){
    verificarPermisosIndex(parameters);
  }else if(indentifierl.includes("user_management.php")){
    verificarPermisosRoles(parameters);
  }else if(indentifierl.includes("upload_files_form.php")){
    verificarPermisosArchivos(parameters);
  }else if(indentifierl.includes("psicosocial_users")){
    verificarPermisosUsuariosPsicosocial(parameters);
   }else if(indentifierl.includes("seguimiento_pilos"))
   {
     verificarPermisosSeguimientoPilos(parameters);
   }//else if(indentifierl.includes("general_reports"))
  // {
  //   verificarPermisosGeneralReports(parameters);
  // }
  
		
};

function getUrlParams (page) {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = [];
  var query = document.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    query_string[pair[0]] = pair[1];
  }
  
  return query_string;
}


function verificarPermisosFicha(urlParameters){
  $.ajax({
            type: "POST",
            data:{page:"ficha",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              var error = msg.error;
              if(!error){
                //console.log(msg);
                  for (x in msg) {
                     var fun = msg[x].funid;
                     var permiso = msg[x].permiso;
                    switch (parseInt(fun)) {
                        case 3://General
                          gestionarPermisosGeneral(permiso,urlParameters);
                          break;
                          
                        case 4: //academica
                          gestionarPermisosAcademica(permiso);
                          break;
                          
                        case 5: //asistenacia
                          gestionarPermisosAsistencia(permiso);  
                          break;
                         
                        case 6: // psicosocial
                          gestionarPermisospsicosocial(permiso);
                          break;
                        
                        case 7: // psicosocial monitor 
                          gestionarPermisospsicosocial_monitor(permiso,urlParameters);
                          //console.log("entro");
                          break;
                          
                        case 10: // f_geografia
                          gestionarPermisosGeografia(permiso, urlParameters);
                          console.log("entro");
                          break;
                          
                        default :
                          console.log("Error al consultar permiso "+ fun);
                    }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: 'AREA RESTRINGIDA', html:true, type: "error",  text: error, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} else {     swal("Cancelled", "Your imaginary file is safe :)", "error");   } });
                //alert(error); window.history.back()
                
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){alert("Error " + msg)},
            });
}

function verificarPermisosIndex(urlParameters){

    $.ajax({
            type: "POST",
            data:{page:"index",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              var error = msg.error;
              if(!error){
                  for (x in msg) {
                    var fun = msg[x].funid;
                    var permiso = msg[x].permiso;
                      switch (permiso) {
                        case 'C':
                          // CODE
                          break;
                        
                        case 'R':
                          $('#btn-send-indexform').prop("disabled",false);
                          break;
                        
                        case 'U':
                          // code
                          break;
                          
                        case 'D':
                          // code
                          break;
                      }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: 'AREA RESTRINGIDA', html:true, type: "error",  text: error, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} else {     swal("Cancelled", "Your imaginary file is safe :)", "error");   } });
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
}

function verificarPermisosSeguimientoPilos(urlParameters){

    $.ajax({
            type: "POST",
            data:{page:"v_seguimiento_pilos",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              var error = msg.error;
              if(!error){
                  for (x in msg) {
                    var fun = msg[x].funid;
                    var permiso = msg[x].permiso;
                      switch (permiso) {
                        case 'C':
                          break;
                        
                        case 'R':
                          break;
                        
                        case 'U':
                          break;
                          
                        case 'D':
                          break;
                      }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: 'AREA RESTRINGIDA', html:true, type: "error",  text: error, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} else {     swal("Cancelled", "Your imaginary file is safe :)", "error");   } });
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
}

function verificarPermisosGeneralReports(urlParameters){

    $.ajax({
            type: "POST",
            data:{page:"v_general_reports",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              var error = msg.error;
              if(!error){
                  for (x in msg) {
                    var fun = msg[x].funid;
                    var permiso = msg[x].permiso;
                      switch (permiso) {
                        case 'C':
                          break;
                        
                        case 'R':
                          break;
                        
                        case 'U':
                          break;
                          
                        case 'D':
                          break;
                      }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: 'AREA RESTRINGIDA', html:true, type: "error",  text: error, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} else {     swal("Cancelled", "Your imaginary file is safe :)", "error");   } });
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
}

function verificarPermisosArchivos(urlParameters){
      $.ajax({
            type: "POST",
            data:{page:"archivos",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              var error = msg.error;
              if(!error){
                  for (x in msg) {
                    var fun = msg[x].funid;
                    var permiso = msg[x].permiso;
                    // alert("Su permiso es "+permiso);
                      switch (permiso) {
                        case 'C':
                          // CODE
                          break;
                        
                        case 'R':
                         
                          break;
                        
                        case 'U':
                          $('#archivo').prop("disabled",false);
                          $('#boton_subir').prop("disabled",false);
                          break;
                          
                        case 'D':
                          $('#archivos_subidos').removeClass("hide");
                          break;
                        
                        default:
                          alert("el permiso es "+permiso);
                          break;
                      }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: "AREA RESTRINGIDA", html:true, type: "error",  text: error, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} else {     swal("Cancelled", "Your imaginary file is safe :)", "error");   } });
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){alert("Error " + msg)},
            })
}

function verificarPermisosRoles(urlParameters){
      $.ajax({
            type: "POST",
            data:{page:"gestion_roles",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              //console.log(msg);
              var error = msg.error;
              if(!error){
                  for (x in msg) {
                    var fun = msg[x].funid;
                    var permiso = msg[x].permiso;
                      switch (permiso) {
                        case 'C':
                          // CODE
                          break;
                        
                        case 'R':
                         
                          break;
                        
                        case 'U':
                          $('#search_button').prop("disabled",false);
                          break;
                          
                        case 'D':
                          // code
                          break;
                      }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: "ÁREA RESTRINGIDA", html:true, type: "warning",  text: "Esta sección está restringida solo para el personal del Área de Sistemas del Plan Talentos Pilos", confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} });
                
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){alert("Error " + msg)},
            })  
}

function gestionarPermisosGeneral(permiso, urlParameters){
  switch (permiso) {

    case 'R':
      $('#1a').removeClass("hide");
      break;
    
    case 'U':
      $('#editar_ficha').removeClass("hide");
      if(!urlParameters.talento_id){
        $('#editar_ficha').addClass("hide");
      }
      break;

  }
}

function gestionarPermisosAcademica(permiso){
  switch (permiso) {
    case 'C':
      // CODE
      break;
    
    case 'R':
      $('#2a').removeClass("hide");
      break;
    
    case 'U':
      // code
      break;
      
    case 'D':
      // code
      break;
  }
}

function gestionarPermisosAsistencia(permiso){
    switch (permiso) {
    case 'C':
      // CODE
      break;
    
    case 'R':
      $('#3a').removeClass("hide");
      break;
    
    case 'U':
      // code
      break;
      
    case 'D':
      // code
      break;
  }
}

function gestionarPermisospsicosocial(permiso){
      switch (permiso) {
    case 'C':
      // CODE
      break;
    
    case 'R':
      $('#4a').removeClass("hide");
      $('#panelPares').removeClass("hide");
      $('#panelGrupal').removeClass("hide");
      $('#panelAcompSocio').removeClass("hide");
      $('#panelSegSocio').removeClass("hide");
      $('#panelPrimerAcerca').removeClass("hide");
      break;
    
    case 'U':
      // code
      break;
      
    case 'D':
      // code
      break;
  }
}

function gestionarPermisosGeografia(permiso){
   switch (permiso) {
    case 'C':
      $('#5a').removeClass("hide");
      break;
    case 'R':
      $('#5a').removeClass("hide");
      break;
  }
}

function gestionarPermisospsicosocial_monitor(permiso, urlParameters){
  var idtalentos = $('#ficha_estudiante #idtalentos').val();
  //console.log(urlParameters);
  canSeeStudent(
    idtalentos,
    urlParameters,
    function(canSee){
      
      //console.log(canSee);
      if (canSee.result){
        switch (permiso) {
          case 'C':
            // CODE
            break;
          
          case 'R':
            $('#4a').removeClass("hide");
            $('#panelPares').removeClass("hide");
            $('#panelGrupal').removeClass("hide");
            break;
          
          case 'U':
            // code
            break;
            
          case 'D':
            // code
            break;
        }
        
      }else{
        swal({title: "ÁREA RESTRINGIDA", html:true, type: "warning",  text: "No tienes permisos para ver la información de este estudiante.<br> Dirigete a la oficina de Sistemas del plan talentos pilos para gestionar tu situación", confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) { 
          window.history.back();
        } });
      }
    }
  );
  
}

function writejson1 (urlParameters){
  $.ajax({
            type: "POST",
            data:{page:"archivos",block:urlParameters.instanceid},
            url: "../managers/writeJsonFile.php",
            success: function(msg)
            {
              console.log(msg);
            },
            dataType: "text",
            cache: "false",
            error: function(msg){console.log("Error "); console.log(msg);},
            });
}

function canSeeStudent(idtalentos, urlParameters, callback){ // se consulta mediane un funcion callbak la cual recibe como parametro resultado del ajax
  var pagina = location.href;
  var data = new Array();
  //console.log(urlParameters);
  if(idtalentos){
    data.push({name:"estudiante_monitor",value:idtalentos});
    data.push({name:"idinstancia",value:urlParameters.instanceid});
    
    //console.log(data);
    $.ajax({
          type: "POST",
          data:data,
          url: "../managers/checkrole.php",
          success: function(msg)
          {
            //console.log({result:result,pagina:pagina});
            msg.pagina = pagina;
            callback(msg);
          },
          dataType: "json",
          cache: "false",
          error: function(msg){
            console.log("Error ");
            console.log(msg);
            //return {result:false,pagina:pagina};
          }
          
    });
  }
}

function verificarPermisosUsuariosPsicosocial(urlParameters){
      $.ajax({
            type: "POST",
            data:{page:"gest_monit_pract",block:urlParameters.instanceid},
            url: "../managers/checkrole.php",
            success: function(msg)
            {
              //console.log(msg);
              var error = msg.error;
              if(!error){
                  for (x in msg) {
                    var fun = msg[x].funid;
                    var permiso = msg[x].permiso;
                      switch (permiso) {
                        case 'C':
                          // CODE
                          break;
                        
                        case 'R':
                         
                          break;
                        
                        case 'U':
                          $('#search_button').prop("disabled",false);
                          break;
                          
                        case 'D':
                          // code
                          break;
                      }
                  }
              }else{
                var men = msg.msg;
                swal({allowEscapeKey:false, title: "ÁREA RESTRINGIDA", html:true, type: "warning",  text: "Esta sección está restringida solo para el personal del Área de Psicosocial.", confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) {  window.history.back();} });
                
              }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){alert("Error " + msg)},
            })  
}
