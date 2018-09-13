// //user_rol, rol, usuario prof es el profesional de socioeducativo, 
// //permisos rol es para saber que permisos por rol, relacion ausuario-fuuncionalidad
// //y permiso, la variable $USER, monitor tiene sus estudiantes talentospilos_monitor_estud, todos los roles tienen un jefe

// var globalArregloPares=[];
// var globalArregloGrupal=[];
// var arregloMonitorYEstudiantes=[];
// var arregloPracticanteYMonitor=[];
// var arregloImprimirPares=[];
// var arregloImprimirGrupos=[];
// var rol=0;
// var id=0;
// var name="";
// var htmltexto="";
// var instance="";
// var email="";

// $(document).ready(function() {

//     //se extrae el id de la instancia en la cual se realizara el reporte
//     var informacionUrl = window.location.search.split("&");
//         for(var i=0;i<informacionUrl.length;i++)
//         {
//             var elemento = informacionUrl[i].split("=");
//             if(elemento[0]=="?instanceid"||elemento[0]=="instanceid")
//             {
//                 var instance=elemento[1];
//             }
//         }

//     //se obtiene el nombre para la verificacion de sistemas y administrador
//     $.ajax({
//         type: "POST",
//         data: {type: "getName"},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             name=msg;
//         },
//         dataType: "text",
//         cache: "false",
//         error: function(msg){swal({title: "error getName" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//     //se obtiene el id de la persona logeada
//     $.ajax({
//         type: "POST",
//         data: {type: "getid"},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             id=msg;
//         },
//         dataType: "text",
//         cache: "false",
//         error: function(msg){swal({title: "error getid" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//     //se obtiene el rol de la persona segun la instancia
//     $.ajax({
//         type: "POST",
//         data: {type: "getRol",id:id,instance:instance},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             rol=msg;
//         },
//         dataType: "text",
//         cache: "false",
//         error: function(msg){swal({title: "error getrol" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//     //se obtiene el email de la persona
//     $.ajax({
//         type: "POST",
//         data: {type: "getEmail"},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             email=msg;
//         },
//         dataType: "text",
//         cache: "false",
//         error: function(msg){swal({title: "error getrol" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//         name="";
//         rol=7;

//         //se muestra la interfaz correspondiente al usuario
//         if(rol==4)
//         {
//             $('#titulo').text("Informacion Estudiantes");
//             //htmltexto=monitorUser(id,0,instance);
//             htmltexto=monitorUser(1055,0,534,0);
//         }else if(rol==7)
//         {
//             $('#titulo').text("Informacion Monitores");
//             // htmltexto=practicanteUser(id,instance);
//             htmltexto=practicanteUser(1113,534);
//         }else if(rol==3)
//         {
//             $('#titulo').text("Informacion Practicantes");
//             // htmltexto=profesionalUser(id,instance);
//             htmltexto=profesionalUser(1123,534);
//         }else if(name=="administrador"||name=="sistemas1008"||name=="Administrador")
//         {
//             anadirEvento();
//         }

//         //se reemplaza el texto retornado, este texto corresponde a un conjunto de toogles
//         $('#reemplazarToogle').html(htmltexto);



//         //si el usuario cumple con las condiciones, se añade el evento a los botones de observaciones
//         //para enviar mensajes a los correspondientes monitores
//          if(rol==7||(name=="administrador"||name=="sistemas1008"||name=="Administrador"))
//          {
//             //se inicia la adicion del evento
//             $(this).on('click','.btn.btn-info.btn-lg.botonCorreo', function()
//             {

//             if($(this).parent().children('textarea').val()=="")
//             {
//                 swal({title: "Para enviar una observación debe llenar el campo correspondiente" , html:true, type: "error",  confirmButtonColor: "#d51b23"})   
//             }else
//             {

//             //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
//             var particionar_informacion=$(this).parent().children('textarea').attr("id").split("_");
//             var tipo = particionar_informacion[0];
//             var codigoN1 = particionar_informacion[1];
//             var fecha = particionar_informacion[3];
//             var nombre = particionar_informacion[4];
//             var mensaje_enviar=$(this).parent().children('textarea').val();
//             var codigoN2=0;

//             $.ajax({
//             type: "POST",
//             data: {type: "getProfesional",id:id,instance:instance},
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async:false,
//             success: function(msg)
//             {
//                 codigoN2=msg;
//             },
//             dataType: "text",
//             cache: "false",
//             error: function(msg){swal({title: "error getrol" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//             });

//             //se limpia el textarea
//             $(this).parent().children('textarea').val("");
//             var respuesta="";

//             //se llama el ajax para enviar el mensaje
//                 $.ajax({
//                 type: "POST",
//                 data: {type: "send_email_to_user",tipoSeg:tipo,codigoEnviarN1:codigoN1,codigoEnviarN2:codigoN2,fecha:fecha,nombre:nombre,message:mensaje_enviar},
//                 url: "../../../blocks/ases/managers/get_info_report.php",
//                 async:false,
//                 success: function(msg)
//                 {
//                     console.log("mensaje");
//                     console.log(msg);
//                 //si el envio del mensaje fue exitoso
//                     if(msg==1)
//                     {
//                         swal({title: "Correo enviado" , html:true, type: "success",  confirmButtonColor: "#d51b23"});
//                     }else
//                     {
//                         swal({title: "error al enviar el correo al monitor" , html:true, type: "error",  confirmButtonColor: "#d51b23"});
//                     }
//                 },
//                 dataType: "text",
//                 cache: "false",
//                 error: function(msg){swal({title: "error al enviar el correo" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//                 });

//             }

//             });


//             $(this).on('click','.btn.btn-info.btn-lg.botonEditarSeguimiento', function()
//             {
//                 console.log("encontro editar");
//                 var $tbody = $(this).parent().parent().parent();
//                 $tbody.find('.editable').removeAttr('readonly');
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');
//             });

//             $(this).on('click','.btn.btn-info.btn-lg.botonCancelarSeguimiento', function()
//             {
//                 console.log("encontro cancelar");
//                 var $tbody = $(this).parent().parent().parent();
//                 $tbody.find('.editable').attr('readonly',true);
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');

//             });

//             $(this).on('click','.btn.btn-info.btn-lg.botonModificarSeguimiento', function()
//             {
//                 console.log("encontro modificar");
//                 var $tbody = $(this).parent().parent().parent();

//                 var idSeguimientoActualizar=$tbody.attr("id");
//                 var lugar = $tbody.find("#lugar").val();
//                 var tema = $tbody.find("#tema").val();
//                 var objetivos = $tbody.find("#objetivos").val();
//                 var obindividual = $tbody.find("#obindividual").val();
//                 var riesgoIndividual = $tbody.find('input[name=riesgo_individual]').val();
//                 var obfamiliar = $tbody.find("#obfamiliar").val();
//                 var riesgoFamiliar = $tbody.find('input[name=riesgo_familiar]').val();
//                 var obacademico = $tbody.find("#obacademico").val();
//                 var riesgoAcademico = $tbody.find('input[name=riesgo_academico]').val();
//                 var obeconomico = $tbody.find("#obeconomico").val();
//                 var riesgoEconomico = $tbody.find('input[name=riesgo_economico]').val();
//                 var obuniversitario = $tbody.find("#obuniversitario").val();
//                 var riesgoUniversitario = $tbody.find('input[name=riesgo_universitario]').val();
//                 var observacionesGeneral = $tbody.find("#observacionesGeneral").val();

//                 console.log(idSeguimientoActualizar)
//                 console.log(lugar);
//                 console.log(tema);
//                 console.log(objetivos);
//                 console.log("individual")
//                 console.log(obindividual);
//                 console.log(riesgoIndividual);
//                 console.log("familiar")
//                 console.log(obfamiliar);
//                 console.log(riesgoFamiliar);
//                 console.log("academico")
//                 console.log(obacademico);
//                 console.log(riesgoAcademico);
//                 console.log("economico")
//                 console.log(obeconomico);
//                 console.log(riesgoEconomico);
//                 console.log("univers")
//                 console.log(obuniversitario);
//                 console.log(riesgoUniversitario);
//                 console.log(observacionesGeneral);


//             });

//          }else if(rol==3||(name=="administrador"||name=="sistemas1008"||name=="Administrador"))
//          {
//             //se inicia la adicion del evento
//             $(this).on('click','.btn.btn-info.btn-lg.botonCorreo', function()
//             {

//             if($(this).parent().children('textarea').val()=="")
//             {
//                 swal({title: "Para enviar una observación debe llenar el campo correspondiente" , html:true, type: "error",  confirmButtonColor: "#d51b23"})   
//             }else
//             { 
//              //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
//             var particionar_informacion=$(this).parent().children('textarea').attr("id").split("_");
//             var tipo = particionar_informacion[0];
//             var codigoN1 = particionar_informacion[1];
//             var codigoN2 = particionar_informacion[2];
//             var fecha = particionar_informacion[3];
//             var nombre = particionar_informacion[4];
//             var mensaje_enviar=$(this).parent().children('textarea').val();

//             //se limpia el textarea
//             $(this).parent().children('textarea').val("");
//             var respuesta="";

//             //se llama el ajax para enviar el mensaje
//                 $.ajax({
//                 type: "POST",
//                 data: {type: "send_email_to_user",tipoSeg:tipo,codigoEnviarN1:codigoN1,codigoEnviarN2:codigoN2,fecha:fecha,nombre:nombre,message:mensaje_enviar},
//                 url: "../../../blocks/ases/managers/get_info_report.php",
//                 async:false,
//                 success: function(msg)
//                 {
//                 //si el envio del mensaje fue exitoso
//                     if(msg==1)
//                     {
//                         swal({title: "Correo enviado" , html:true, type: "success",  confirmButtonColor: "#d51b23"});
//                     }else
//                     {
//                         swal({title: "error al enviar el correo al monitor" , html:true, type: "error",  confirmButtonColor: "#d51b23"});
//                     }
//                 },
//                 dataType: "text",
//                 cache: "false",
//                 error: function(msg){swal({title: "error al enviar el correo" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//                 });
//             }   
//             });

//             /*
//             $(this).on('click','.btn.btn-info.btn-lg.botonEditar', function()
//             {
//                 console.log("encontro");
//                 var $tbody = $(this).parent().parent().parent().find('.editable');
//                 $tbody.removeAttr('readonly');
//                 $tbody.find('.botonesSeguimiento').toogleClass('.ocultar');

//             });
//             */
//          }
// });

// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************
// //METODOS INICIALES PARA EL PROFESIONAL
// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************

// //funcion que recupera los practicantes de un profesional en la instancia
// function profesionalUser(id_prof,instanceid)
// {
//     $.ajax({
//         type: "POST",
//         data: {type: "info_profesional",id:id_prof},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             //se llama el metodo que crea el arreglo global de practicantes de un profesional en la instancia
//             transformarConsultaProfesionalArray(msg,instanceid);
//         },
//         dataType:"json",
//         cache: "false",
//         error: function(msg){swal({title: "error info practicante" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//         //se retorna la informacion del toogle creado desde el punto del profesional
//         return crearTablaYToggleProfesional(arregloPracticanteYMonitor);

// }

// //funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
// //se usara para la creacion del toogle
// function transformarConsultaProfesionalArray(arregloPracticantes,instance)
// {

//     for(var practicante in arregloPracticantes)
//     {
//       var arregloAuxiliar=[];
//       //arreglo[codigo-nombre-html de practicante]
//       arregloAuxiliar.push(arregloPracticantes[practicante][0])
//       arregloAuxiliar.push(arregloPracticantes[practicante][1]);
//       //se asigna a esta posicion un texto html correspondiente a la informacion del practicante
//       arregloAuxiliar.push(practicanteUser(arregloPracticantes[practicante][0],instance));
//       arregloPracticanteYMonitor.push(arregloAuxiliar);
//     }

// }

// //se crea el toogle del profesional el cual tiene cada uno de los practicantesr asignados al profesional
// function crearTablaYToggleProfesional()
// {
//     var stringRetornar="";
//     for(var practicante in arregloPracticanteYMonitor)
//   {
//      stringRetornar+='<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading" style="background-color: #938B8B;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse'+arregloPracticanteYMonitor[practicante][0]+'">'+arregloPracticanteYMonitor[practicante][1]+'</a></h4></div>';
//      stringRetornar+='<div id="collapse'+arregloPracticanteYMonitor[practicante][0]+'" class="panel-collapse collapse"><div class="panel-body">';
//      //en la tercer posicion del arreglo se encuentra un texto html con un formato especifico
//      stringRetornar+=arregloPracticanteYMonitor[practicante][2];
//      stringRetornar+='</div></div></div></div>'
//   }

//   return stringRetornar;
// }

// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************
// //METODOS INICIALES PARA EL PRACTICANTE
// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************

// //funcion que recupera los monitores de un practicante en la instancia
// function practicanteUser(id_pract,instanceid)
// {
//     arregloMonitorYEstudiantes=[];

//         $.ajax({
//         type: "POST",
//         data: {type: "info_practicante",id:id_pract},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             //se llama el metodo que crea el arreglo global de monitores de un practicante en la instancia
//             transformarConsultaPracticanteArray(msg,instanceid,id_pract);
//         },
//         dataType:"json",
//         cache: "false",
//         error: function(msg){swal({title: "error info practicante" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//         //se retorna la informacion del toogle creado desde el punto del practicante
//         return crearTablaYTogglePracticante(arregloMonitorYEstudiantes);
// }

// //funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
// //se usara para la creacion del toogle
// function transformarConsultaPracticanteArray(arregloMonitores,instance,id_pract)
// {

//     for(var monitor in arregloMonitores)
//     {
//       var arregloAuxiliar=[];
//       var cantidad=0;
//       //arreglo[codigo-nombre-html de los monitores]
//       arregloAuxiliar.push(arregloMonitores[monitor][0]);
//       arregloAuxiliar.push(arregloMonitores[monitor][1]);
//       arregloAuxiliar.push(monitorUser(arregloMonitores[monitor][0],monitor,instance,id_pract));

//       $.ajax({
//         type: "POST",
//         data: {type:"number_seg_monitor",id:arregloMonitores[monitor][0],instance:instance},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             //se llama el metodo que crea el arreglo global de estudiantes de un monitor en la instancia
//             cantidad=msg;
//         },
//         dataType:"text",
//         cache: "false",
//         error: function(msg){swal({title: "error info monitor" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//       arregloAuxiliar.push(cantidad);
//       arregloMonitorYEstudiantes.push(arregloAuxiliar);
//     }

// }

// //se crea el toogle del practicante el cual tiene cada uno de los monitores asignados al practicante
// function crearTablaYTogglePracticante()
// {
//     var stringRetornar="";
//     for(var monitor in arregloMonitorYEstudiantes)
//   {
//      stringRetornar+='<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading" style="background-color: #AEA3A3;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse'+arregloMonitorYEstudiantes[monitor][0]+'">'+arregloMonitorYEstudiantes[monitor][1]+'<span> Cantidad Seguimientos: '+arregloMonitorYEstudiantes[monitor][3]+'</span></a></h4></div>';
//      stringRetornar+='<div id="collapse'+arregloMonitorYEstudiantes[monitor][0]+'" class="panel-collapse collapse"><div class="panel-body">';
//      //en la tercer posicion del arreglo se encuentra un texto html con un formato especifico
//      stringRetornar+=arregloMonitorYEstudiantes[monitor][2];
//     stringRetornar+='</div></div></div></div>'
//   }
//   return stringRetornar;
// }

// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************
// //METODOS INICIALES PARA EL MONITOR
// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************

// //funcion que recupera los estudiantes de un monitor en la instancia
// function monitorUser(codigoMonitor,noMonitor,instanceid,codigoPracticante)
// {
//     var informacion;

//     $.ajax({
//         type: "POST",
//         data: {type: "info_monitor",id:codigoMonitor,instance:instanceid},
//         url: "../../../blocks/ases/managers/get_info_report.php",
//         async:false,
//         success: function(msg)
//         {
//             //se llama el metodo que crea el arreglo global de estudiantes de un monitor en la instancia
//             transformarConsultaMonitorArray(msg);
//         },
//         dataType:"json",
//         cache: "false",
//         error: function(msg){swal({title: "error info monitor" , html:true, type: "error",  confirmButtonColor: "#d51b23"})},
//         });

//     //metodo que agrupa informacion de los seguimientos de pares por el codigo
//     arregloImprimirPares=agrupar_informacion(globalArregloPares,20); 

//     //metodo que agrupa informacion de los seguimientos grupales por los codigos
//     arregloImprimirGrupos=agrupar_informacion(globalArregloGrupal,12); 

//     //metodo que deja un solo registro grupal con el mismo codigo y concatena nombres y codigos de los estudiantes
//     arregloImprimirGrupos=agrupar_Seguimientos_grupales(arregloImprimirGrupos);

//     //se ordena los seguimientos de cada estudiante segun la fecha
//     for(var grupo in arregloImprimirPares)
//     {
//         ordenaPorColumna(arregloImprimirPares[grupo],19);
//     }

//     //se retorna la informacion del toogle creado desde el punto del monitor
//     return crearTablaYToggle(arregloImprimirPares,noMonitor,arregloImprimirGrupos,codigoMonitor,codigoPracticante);
// }

// //funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
// //se usara para la creacion del toogle
// function transformarConsultaMonitorArray(consulta)
// {
//     for(var registro in consulta)
//     {
//         //se extrae informacion dependiendo de si el seguimiento es de pares o grupal
//         if(consulta[registro]["tipo"]=="PARES")
//         {
//             var array_auxiliar=[];

//             var fecha=transformarFecha(consulta[registro]["fecha"]);
//             var nombre=consulta[registro]["nombre_estudiante"];
//             var apellido=consulta[registro]["apellido_estudiante"];
//             var nombre_enviar="";
//             if(apellido==""||apellido.length==0)
//             {
//               nombre_enviar=nombre;
//             }else
//             {
//               nombre_enviar=nombre+" "+apellido;  
//             }

//             var nombrem=consulta[registro]["nombre_monitor_creo"];
//             var apellidom=consulta[registro]["apellido_monitor_creo"];
//             var nombremon_enviar="";

//             if(apellidom==""||apellidom.length==0)
//             {
//               nombremon_enviar=nombrem;
//             }else
//             {
//               nombremon_enviar=nombrem+" "+apellidom;  
//             }

//             array_auxiliar.push(nombre_enviar);//0
//             array_auxiliar.push(fecha);//1
//             array_auxiliar.push(consulta[registro]["hora_ini"]);//2
//             array_auxiliar.push(consulta[registro]["hora_fin"]);//3
//             array_auxiliar.push(consulta[registro]["lugar"]);//4
//             array_auxiliar.push(consulta[registro]["tema"]);//5
//             array_auxiliar.push(consulta[registro]["actividades"]);//6
//             array_auxiliar.push(consulta[registro]["individual"]);//7
//             array_auxiliar.push(consulta[registro]["individual_riesgo"]);//8
//             array_auxiliar.push(consulta[registro]["familiar_desc"]);//9
//             array_auxiliar.push(consulta[registro]["familiar_riesgo"]);//10
//             array_auxiliar.push(consulta[registro]["academico"]);//11
//             array_auxiliar.push(consulta[registro]["academico_riesgo"]);//12
//             array_auxiliar.push(consulta[registro]["economico"]);//13
//             array_auxiliar.push(consulta[registro]["economico_riesgo"]);//14
//             array_auxiliar.push(consulta[registro]["vida_uni"]);//15
//             array_auxiliar.push(consulta[registro]["vida_uni_riesgo"]);//16
//             array_auxiliar.push(consulta[registro]["observaciones"]);//17
//             array_auxiliar.push("saltar");//18 borra
//             array_auxiliar.push(consulta[registro]["fecha"]);//19
//             array_auxiliar.push(consulta[registro]["id_estudiante"]);//20 id talentos
//             array_auxiliar.push(nombremon_enviar);//21
//             array_auxiliar.push(consulta[registro]["objetivos"]);//22
//             array_auxiliar.push(consulta[registro]["id_seguimiento"]);//23

//             globalArregloPares.push(array_auxiliar)
//         }else if(consulta[registro]["tipo"]=="GRUPAL")
//         {
//             var array_auxiliar=[];

//             var fecha=transformarFecha(consulta[registro]["fecha"]);
//             var nombre=consulta[registro]["nombre_estudiante"];
//             var apellido=consulta[registro]["apellido_estudiante"];
//             var nombre_enviar="";
//             if(apellido==""||apellido.length==0)
//             {
//               nombre_enviar=nombre;
//             }else
//             {
//               nombre_enviar=nombre+" "+apellido;  
//             }

//             var nombrem=consulta[registro]["nombre_monitor_creo"];
//             var apellidom=consulta[registro]["apellido_monitor_creo"];
//             var nombremon_enviar="";

//             if(apellidom==""||apellidom.length==0)
//             {
//               nombremon_enviar=nombrem;
//             }else
//             {
//               nombremon_enviar=nombrem+" "+apellidom;  
//             }

//             array_auxiliar.push(nombre_enviar);
//             array_auxiliar.push(fecha);
//             array_auxiliar.push(consulta[registro]["hora_ini"]);
//             array_auxiliar.push(consulta[registro]["hora_fin"]);
//             array_auxiliar.push(consulta[registro]["lugar"]);
//             array_auxiliar.push(consulta[registro]["tema"]);
//             array_auxiliar.push(consulta[registro]["actividades"]);
//             array_auxiliar.push(consulta[registro]["objetivos"]);
//             array_auxiliar.push(consulta[registro]["observaciones"]);
//             array_auxiliar.push("saltar");//9 borrar
//             array_auxiliar.push(consulta[registro]["fecha"]);//10
//             array_auxiliar.push(consulta[registro]["id_estudiante"]);   //11
//             array_auxiliar.push(consulta[registro]["id_seguimiento"]);//12
//             array_auxiliar.push(nombremon_enviar);//13
//             globalArregloGrupal.push(array_auxiliar)

//         }
//     }
// }

// //se crea el toogle del practicante el cual tiene cada uno de los estudiantes asignados al monitor
// function crearTablaYToggle(arregloImprimirPares,monitorNo,arregloImprimirGrupos,codigoEnviarN1,codigoEnviarN2)
// {
//      var stringRetornar="";

//      //se recorre cada estudiante
//      for(var student in arregloImprimirPares)
//      {

//      stringRetornar+='<div class="panel-group"><div class="panel panel-default"><div class="panel-heading" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse'+monitorNo+arregloImprimirPares[student][0][20]+'">'+arregloImprimirPares[student][0][0]+'<span>Cantidad registros estudiante: '+arregloImprimirPares[student].length+' </span></a></h4></div>';
//      stringRetornar+='<div id="collapse'+monitorNo+arregloImprimirPares[student][0][20]+'" class="panel-collapse collapse"><div class="panel-body">';

//      //se crea un toogle para cada seguimiento que presente dicho estudiante
//      for(var tupla in arregloImprimirPares[student])
//      {
//       stringRetornar+='<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse'+monitorNo+arregloImprimirPares[student][0][20]+tupla+'">'+"Registro "+arregloImprimirPares[student][tupla][1]+'</a></h4></div>';
//       stringRetornar+='<div id="collapse'+monitorNo+arregloImprimirPares[student][0][20]+tupla+'" class="panel-collapse collapse"><div class="panel-body hacer-scroll"><table class="table table-hover students_table" id="students_table'+arregloImprimirPares[student][0][20]+arregloImprimirPares[student][0][19]+'">';
//       stringRetornar+='<thead><tr><th></th><th></th><th></th></tr></thead>';
//       stringRetornar+='<tbody id='+tupla+'_'+arregloImprimirPares[student][tupla][23]+'>';

//                 stringRetornar+='<tr><td>'+arregloImprimirPares[student][tupla][1]+'</td>';
//                 stringRetornar+='<td><b>LUGAR:</b> <input id="lugar" class="no-borde-fondo editable lugar" readonly value="'+arregloImprimirPares[student][tupla][4]+'"></td>';
//                 stringRetornar+='<td><b>HORA:</b> '+arregloImprimirPares[student][tupla][2]+'-'+arregloImprimirPares[student][tupla][3]+'</td><tr>';

//                 stringRetornar+='<tr><td colspan="3"><b>TEMA:</b><br><input id="tema" class="no-borde-fondo editable tema" readonly value="'+arregloImprimirPares[student][tupla][5]+'"></td></tr>';

//                 stringRetornar+='<tr><td colspan="3"><b>OBJETIVOS:</b><br><textarea id="objetivos" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][22]+'</textarea></td></tr>';

//                 var riesgo="";
//                 var valor=-1;
//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if(arregloImprimirPares[student][tupla][8]==1)
//                 {
//                     riesgo="bajo";
//                     valor = 1;
//                 }else if(arregloImprimirPares[student][tupla][8]==2)
//                 {
//                     riesgo="medio";
//                     valor = 2;
//                 }else if(arregloImprimirPares[student][tupla][8]==3)
//                 {
//                     riesgo="alto";
//                     valor = 3;
//                 }else
//                 {
//                     riesgo = "no";
//                 }

//                 if(riesgo!="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+'"><b>INDIVIDUAL:</b><br><textarea id="obindividual" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][7]+'</textarea><br>RIESGO: '+riesgo;    
//                     stringRetornar+='<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_individual"  value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_individual" value="2" checked>Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_individual" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';


//                 }else if(riesgo=="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+' quitar-ocultar ocultar individual"><b>INDIVIDUAL:</b><br><textarea id="obindividual" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra'
//                     stringRetornar+='<div class="col-md-12" id="radio_individual_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_individual"  value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_individual" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_individual" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';

//                 }


//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if(arregloImprimirPares[student][tupla][10]==1)
//                 {
//                     riesgo="bajo";
//                     valor=1;
//                 }else if(arregloImprimirPares[student][tupla][10]==2)
//                 {
//                     riesgo="medio";
//                     valor=2;
//                 }else if(arregloImprimirPares[student][tupla][10]==3)
//                 {
//                     riesgo="alto";
//                     valor=3;
//                 }else
//                 {
//                     riesgo = "no";
//                 }

//                 if(riesgo!="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+'"><b>FAMILIAR:</b><br><textarea id="obfamiliar" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][9]+'</textarea><br>RIESGO: '+riesgo;    
//                     stringRetornar+='<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_familiar" value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_familiar" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_familiar" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';

//                 }else if(riesgo=="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+' quitar-ocultar ocultar"><b>FAMILIAR:</b><br><textarea id="obfamiliar" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar+='<div class="col-md-12" id="radio_familiar_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_familiar"  value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_familiar" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_familiar" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';
//                 }

//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if(arregloImprimirPares[student][tupla][12]==1)
//                 {
//                     riesgo="bajo";
//                     valor=1;
//                 }else if(arregloImprimirPares[student][tupla][12]==2)
//                 {
//                     riesgo="medio";
//                     valor=2;
//                 }else if(arregloImprimirPares[student][tupla][12]==3)
//                 {
//                     riesgo="alto";
//                     valor=3;
//                 }else
//                 {
//                     riesgo = "no";
//                 }

//                 if(riesgo!="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+'"><b>ACADEMICO:</b><br><textarea id="obacademico" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][11]+'</textarea><br>RIESGO: '+riesgo;
//                     stringRetornar+='<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_academico" value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_academico" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_academico" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';


//                 }else if(riesgo=="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+' quitar-ocultar ocultar"><b>ACADEMICO:</b><br><textarea id="obacademico" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar+='<div class="col-md-12" id="radio_academico_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_academico"  value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_academico" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_academico" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';
//                 }

//             //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if(arregloImprimirPares[student][tupla][14]==1)
//                 {
//                     riesgo="bajo";
//                     valor=1;
//                 }else if(arregloImprimirPares[student][tupla][14]==2)
//                 {
//                     riesgo="medio";
//                     valor=2;
//                 }else if(arregloImprimirPares[student][tupla][14]==3)
//                 {
//                     riesgo="alto";
//                     valor=3;
//                 }else
//                 {
//                     riesgo = "no";
//                 }

//                 if(riesgo!="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+'"><b>ECONOMICO:</b><br><textarea id="obeconomico" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][13]+'</textarea><br>RIESGO: '+riesgo;    
//                     stringRetornar+='<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_economico" value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_economico" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_economico" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';


//                 }else if(riesgo=="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+' quitar-ocultar ocultar"><b>ECONOMICO:</b><br><textarea id="obeconomico" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar+='<div class="col-md-12" id="radio_economico_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_economico"  value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_economico" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_economico" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';
//                 }

//             //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if(arregloImprimirPares[student][tupla][16]==1)
//                 {
//                     riesgo="bajo";
//                     valor=1;
//                 }else if(arregloImprimirPares[student][tupla][16]==2)
//                 {
//                     riesgo="medio";
//                     valor=2;
//                 }else if(arregloImprimirPares[student][tupla][16]==3)
//                 {
//                     riesgo="alto";
//                     valor=3;
//                 }else
//                 {
//                     riesgo = "no";
//                 }

//                 if(riesgo!="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+'"><b>UNIVERSITARIO:</b><br><textarea id="obuniversitario" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][15]+'</textarea><br>RIESGO: '+riesgo;
//                     stringRetornar+='<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_universitario" value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_universitario" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_universitario" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';


//                 }else if(riesgo=="no")
//                 {
//                     stringRetornar+='<tr><td colspan="3" class="riesgo_'+riesgo+' quitar-ocultar ocultar"><b>UNIVERSITARIO:</b><br><textarea id="obuniversitario" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar+='<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div'+arregloImprimirPares[student][tupla][23]+'">';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_universitario"  value="1">Bajo';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_universitario" value="2">Medio';
//                     stringRetornar+='</label>';
//                     stringRetornar+='<label class="radio-inline" >';
//                     stringRetornar+='<input type="radio" name="riesgo_universitario" value="3">Alto';
//                     stringRetornar+='</label>';
//                     stringRetornar+='</div>';
//                     stringRetornar+='</td></tr>';
//                 }

//             stringRetornar+='<tr><td colspan="3"><b>OBSERVACIONES:</b><br><textarea id="observacionesGeneral" class ="no-borde-fondo editable" readonly>'+arregloImprimirPares[student][tupla][17]+'</textarea></td></tr>';    

//             stringRetornar+='<tr><td colspan="3"><b>CREADO POR:</b><br>'+arregloImprimirPares[student][tupla][21]+'</td></tr>'; 

//             //en caso que tenga el rol correspondiente se añade un campo y un boton para
//             //enviar un mensaje con observaciones tanto al monitor que hizo el seguimiento como al profesional que lo envia
//             if(rol==3||rol==7||name=="administrador"||name=="sistemas1008"||name=="Administrador")
//             {
//                 stringRetornar+='<tr><td colspan="3"><b>REPORTAR OBSERVACIÓN</b><br><textarea id="individual_'+codigoEnviarN1+'_'+codigoEnviarN2+'_'+arregloImprimirPares[student][tupla][1]+'_'+arregloImprimirPares[student][tupla][0]+'" rows="4" cols="150"></textarea><br><br><span class="btn btn-info btn-lg botonCorreo" type="button">Enviar observaciones</span>';
//                 stringRetornar+='<span class="btn btn-info btn-lg botonesSeguimiento botonEditarSeguimiento" type="button">Editar Seguimiento</span><span class="btn btn-info btn-lg botonesSeguimiento botonModificarSeguimiento ocultar" type="button">Aceptar</span><span class="btn btn-info btn-lg botonesSeguimiento botonCancelarSeguimiento ocultar" type="button">Cancelar</span>';
//                 stringRetornar+='<td></tr>'; 
//             }


//         //cerre el colapsable correspondientes
//         stringRetornar+='</tbody></table></div></div></div></div>';

//      }
//       stringRetornar+='</div></div></div></div>'
//             console.log(   $("#radio_individual_div18597 .radio-inline input:radio").html() );
//      }

//     //si existen seguimiento grupales
//     if(arregloImprimirGrupos.length!=0)
//     {
//       stringRetornar+='<div class="panel-group"><div class="panel panel-default"><div class="panel-heading" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup'+monitorNo+arregloImprimirGrupos[0][11]+'">SEGUIMIENTOS GRUPALES</a></h4></div>';
//       stringRetornar+='<div id="collapsegroup'+monitorNo+arregloImprimirGrupos[0][11]+'" class="panel-collapse collapse"><div class="panel-body">';

//       for(var grupo in arregloImprimirGrupos)
//     {
//      stringRetornar+='<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup'+monitorNo+grupo+arregloImprimirGrupos[grupo][11]+'">'+arregloImprimirGrupos[grupo][1]+'</a></h4></div>';
//      stringRetornar+='<div id="collapsegroup'+monitorNo+grupo+arregloImprimirGrupos[grupo][11]+'" class="panel-collapse collapse"><div class="panel-body hacer-scroll"><table class="table table-hover" id="grouptable">';
//      stringRetornar+='<thead><tr><th></th><th></th><th></th></tr></thead>';
//      stringRetornar+='<tbody id='+grupo+'_'+arregloImprimirGrupos[grupo][12]+'>';
//           stringRetornar+='<tr><td>'+arregloImprimirGrupos[grupo][1]+'</td>';
//           stringRetornar+='<td>LUGAR: '+arregloImprimirGrupos[grupo][4]+'</td>';
//           stringRetornar+='<td>HORA: '+arregloImprimirGrupos[grupo][2]+'-'+arregloImprimirGrupos[grupo][3]+'</td></tr>';

//           stringRetornar+='<tr><td colspan="3"><b>ESTUDIANTES:</b><br> '+arregloImprimirGrupos[grupo][14]+'</td></tr>';

//           stringRetornar+='<tr><td colspan="3"><b>TEMA:</b><br> '+arregloImprimirGrupos[grupo][5]+'</td></tr>';

//           stringRetornar+='<tr><td colspan="3"><b>ACTIVIDADES GRUPALES:</b><br> '+arregloImprimirGrupos[grupo][6]+'</td></tr>';

//           stringRetornar+='<tr><td colspan="3"><b>OBSERVACIONES:</b><br>'+arregloImprimirGrupos[grupo][7]+'</td></tr>';

//           stringRetornar+='<tr><td colspan="3"><b>CREADO POR:</b><br>'+arregloImprimirPares[student][tupla][21]+'</td></tr>'; 


//           if(rol==3||rol==7||(name=="administrador"||name=="sistemas1008"||name=="Administrador"))
//           {
//               stringRetornar+='<tr><td colspan="3"><b>REPORTAR OBSERVACIÓN</b><br><textarea id="grupal_'+codigoEnviarN1+'_'+codigoEnviarN2+'_'+arregloImprimirGrupos[grupo][1]+'_'+arregloImprimirGrupos[grupo][14]+'" rows="4" cols="150"></textarea><br><br><span class="btn btn-info btn-lg botonCorreo" type="button">Enviar observaciones</span><td></tr>'; 
//           }

//         //en caso que tenga el rol correspondiente se añade un campo y un boton para
//         //enviar un mensaje con observaciones tanto al monitor que hizo el seguimiento como al profesional que lo envia
//         stringRetornar+='</tbody></table></div></div></div></div>';
//     }
//     stringRetornar+='</div></div></div></div>';

//     }

//     globalArregloPares=[];
//     globalArregloGrupal=[];

//     return stringRetornar;
// }


// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************

// //funcion para agrupar los seguimientos segun el monitor
// function agrupar_informacion(infoMonitor,campoComparar)
// {
//     var nuevoArreglo = [];
//   //se recorren todos los estudiantes
//   for(var unico in infoMonitor)
//   {
//       //se inician variables
//       var confirmarAnanir="si";
//       var posicion=0;
//       //si es el primer elemento del arreglo siempre se añadira
//       if(nuevoArreglo.length!=0)
//       {
//       //si ya hay elementos en el arreglo
//         for(var actuales in nuevoArreglo)
//         {
//         //se verifica que no exista otra persona con el mismo nombre
//           if(infoMonitor[unico][campoComparar]==nuevoArreglo[actuales][0][campoComparar])
//           {
//           //si existe entonces no se añadira un nuevo al arreglo sino uno nuevo a la posicion
//              confirmarAnanir="no"
//              posicion=actuales;
//           }
//         }
//       }
//       //si se retorna si es decir que no existen registros del estudiante
//       if(confirmarAnanir=="si")
//       {
//         var arregloEstudiante=[];
//         //se agrega al arreglo
//         var tamano=nuevoArreglo.length;
//         //alert(tamano);
//         arregloEstudiante.push(infoMonitor[unico]);
//         nuevoArreglo[tamano]=arregloEstudiante;
//       }else
//       {
//         var arregloEstudiante=[];
//         arregloEstudiante=nuevoArreglo[posicion];
//         arregloEstudiante.push(infoMonitor[unico]);
//         //si no es prque ya tiene registro asi que se agrega registro al estudiante
//         nuevoArreglo[posicion]=[];
//         nuevoArreglo[posicion]=arregloEstudiante;
//       }
//   }
//   return nuevoArreglo;
// }

// //funcion para agrupar los seguimientos grupales segun el id
// function agrupar_Seguimientos_grupales(arreglo)
// {
//     var NuevoArregloGrupal=[];
//     for(var elementoRevisar in arreglo)
//     {
//         var arregloAuxiliar=arreglo[elementoRevisar][0].slice();
//         var nombres="";
//         var nombresImpirmir="";
//         var codigos="";
//         var contador=1;

//         //funcion que captura tanto los nombres como los codigos y crea un texto 
//         //para cada uno los cuales seran usado para ponerse en la tabla
//         for(var tuplaGrupo=0;tuplaGrupo<arreglo[elementoRevisar].length;tuplaGrupo++)
//         {
//             if(tuplaGrupo==(arreglo[elementoRevisar].length)-1)
//             {
//             nombres+=arreglo[elementoRevisar][tuplaGrupo][0];
//             nombresImpirmir+=arreglo[elementoRevisar][tuplaGrupo][0];
//             codigos+=arreglo[elementoRevisar][tuplaGrupo][11];
//             }else
//             {
//             nombres+=arreglo[elementoRevisar][tuplaGrupo][0];
//             nombresImpirmir+=arreglo[elementoRevisar][tuplaGrupo][0]+",";
//             codigos+=arreglo[elementoRevisar][tuplaGrupo][11];
//             }
//         }

//         //se al arreglo los nombres y los codigos concatenados al final del arreglo
//         arregloAuxiliar[0]=nombres;
//         arregloAuxiliar[11]=codigos;
//         arregloAuxiliar.push(nombresImpirmir);
//         NuevoArregloGrupal.push(arregloAuxiliar)
//     }

//     return NuevoArregloGrupal;
// }

// //******************************************************************************************************
// //******************************************************************************************************
// //******************************************************************************************************

// //funcion que añade un comboBox para los roles especificos y consulta codigos
// //predefinidos
// function anadirEvento()
// {

//             var selectanadir='<select id="selectProfesional" name="divCategoriaPadre" class="selectPadre col-md-offset-2">';
//                 selectanadir+='<option value="inicio">Seleccione Practicante a consultar</option>';
//                 selectanadir+='<option value=4>monitor prueba</option>';
//                 selectanadir+='<option value=7>practicante prueba</option>';
//                 selectanadir+='<option value=3>profesional prueba</option>';
//                 selectanadir+='</select>';
//                 selectanadir+='<hr><span class="btn btn-primary col-md-offset-2" id="consultarMonitores" class="submit">Consultar Monitores </span>';

//     $('#anadir').append(selectanadir);

//     $('#consultarMonitores').on('click',function()
//     {
//     var v=$('#selectProfesional').val();
//     if(v=="inicio")
//       {
//         alert("Seleccione una opcion");
//       }else
//         {
//                 if(v==4)
//             {   $('#titulo').text("Informacion Estudiantes");
//                 // htmltexto=monitorUser(54671,0,450299,0);
//                 console.log("entro m act")
//                 htmltexto=monitorUser(1055,0,534,0);
//             }else if(v==7)
//             {   $('#titulo').text("Informacion Practicante");
//                 // htmltexto=practicanteUser(103132,450299);
//                 console.log("entro pract")
//                 htmltexto=practicanteUser(1113,534);
//             }else if(v==3)
//             {
//                 $('#titulo').text("Informacion Profesional");
//                 // htmltexto=profesionalUser(110953,450299);
//                 console.log("entro prof")
//                 htmltexto=profesionalUser(1123,534);
//             }

//             $('#reemplazarToogle').html(htmltexto);
//         }

//   }
//   )
// }


// //funcion que ordena un arreglo segun la columna definida de menos valor a mayor
// function  ordenaPorColumna(arreglo,col) {

//         var aux;

//         // Recorro la columna selecciona
//         for (var i = 0; i < arreglo.length; i++) {
//             for (var j = i + 1; j < arreglo.length; j++) {
//                 // Verifico si el elemento en la posición [i][col] es mayor que el de la posición [j][col]
//                 if (arreglo[i][col] < arreglo[j][col]) {
//                     // Recorro las filas seleccionadas (i, j) e intercambio los elementos
//                     // Declaro la variable k para controlar la posición (columnas) en la fila
//                     for (var k = 0; k < arreglo[i].length; k++) {
//                         // Intercambio los elementos de las filas seleccionadas columna por columna
//                         aux = arreglo[i][k];
//                         arreglo[i][k] = arreglo[j][k];
//                         arreglo[j][k] = aux;
//                     }
//                 }
//             }
//         }
//     }

// //funcion que transforma la fecha guardada en el campus en formato epoch a un formato
// //identificable para las personas
// function transformarFecha(fecha)
// {
//             var a = new Date( fecha * 1000);
//             var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
//             var year = a.getFullYear();
//             var month = months[a.getMonth()];
//             var date = a.getDate();
//             var time = date + ' ' + month + ' ' + year;
//             return time;
// }

// function cantidadSeguimientosMonitor(arreglo)
// {

//     var cantidad=0;
//     for(var estudiante in arreglo)
//     {
//         for(seguimiento in estudiante)
//         {
//             cantidad++;
//         }
//     }

//     return cantidad;

// }
