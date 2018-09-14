$(document).ready(function (){
    load_students();
    loadAll_seg();
    
	$('#socioedu_add_grupal').click(function() {
        $('#save_seg').removeClass("hide");
        $('#div_created').addClass('hide');
        $('#upd_seg').addClass('hide');
        $('#myModalLabel').attr('name','GRUPAL');
    
        initFormSeg();
        load_attendance_list();
	});
	
	$('#go_back').on('click',function(){
	    window.history.back();
	});
	
	
	
	$('#save_seg').click(function () {
	    var students_id =  new Array();
	    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
	    
        $('#seguimiento input[id=asistencia]:checked').each(
                function() {
                    var id = $(this).val();
                    students_id.push(id);
                }
        );  
	   
	   
	   var tipo =  $('#myModalLabel').attr('name');
	   var data = $('#seguimiento').serializeArray();
	   data.push({name:"function",value:"new"});
	   data.push({name:"tipo",value:tipo});
	   data.push({name:"idtalentos",value:students_id});
	   data.push({name:"idinstancia", value: parameters.instanceid});
	   console.log(data);
	   
	   var validation = validateModal(data);
        if (validation.isvalid){
         
                
         	$.ajax({
                type: "POST",
                data: data,
                url: "../managers/seguimiento.php",
                success: function(msg)
                {
                    var error = msg.error;
                    if(!error){
                        swal({title: "Actualizado con exito!!", html:true, type: "success",  text: msg.msg, confirmButtonColor: "#d51b23"});
                    	$('#myModal').modal('toggle');
                    	$('#myModal').modal('toggle');
                    	$('#save_seg').addClass('hide');
    	    			$('.modal-backdrop').remove();
    	    			loadAll_seg();
                    }else{
                        swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3"});
                    }
                },
                dataType: "json",
                cache: "false",
                error: function(msg){console.log(msg)},
            });
        	
        }else{
        	swal({title: "Error", html:true, type: "warning",  text: "Detalles del error:<br>"+validation.detalle, confirmButtonColor: "#D3D3D3"});
        }
        
        
        
         
	});
	
	$('#upd_seg').click(function(){
	    var id_seg = $(this).parent().attr('id');
	    update_seg_grupal(id_seg);
	});
	
});

function load_students(){
    var data =  new Array();
    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    data.push({name:"function",value:"load_grupal"});
    data.push({name:"idinstancia", value:parameters.instanceid});
   
    $.ajax({
            type: "POST",
            data: data,
            url: "../managers/seguimiento.php",
            success: function(msg)
            {
                $('#mytable tbody').html('');
                if(msg.rows != 0){
                    
                    var content =  msg.content;
                    for (x in content){
                        
                        $('#mytable tbody').append("<tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td class=\"hide\">"+ content[x].idtalentos +"</td> </tr>");
                    }
                
                }else{
                    $('#list_grupal_seg').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
}


function load_attendance_list(list = null, editable = null){
    var data =  new Array();
    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    
    data.push({name:"function",value:"load_grupal"});
    data.push({name:"idinstancia",value: parameters.instanceid});
   
    $.ajax({
            type: "POST",
            data: data,
            url: "../managers/seguimiento.php",
            success: function(msg)
            {
                $('#seguimiento #mytable tbody').html('');
                
                if(msg.rows != 0){
                //   var content =  msg.content;
                //     for (x in content){
                //         $('#seguimiento #mytable tbody').append("<tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\""+ content[x].idtalentos +"\"/></td>  </tr>"); 
                //     }

                    var content =  msg.content;
                    if(!list){
                         for (x in content){
                            $('#seguimiento #mytable tbody').append("<tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\""+ content[x].idtalentos +"\"/></td>  </tr>"); 
                         }
                    }else{
                        
                        var arrayid = new Array();
                        for ( x in list){
                            arrayid.push(list[x].id_estudiante);
                        }
                      
                 
                        for (x in content){
                           
                            var id = content[x].idtalentos;
                            if($.inArray(id,arrayid) != -1){
                                if(editable){
                                    $('#seguimiento #mytable tbody').append(" <tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td><input type=\"checkbox\" checked=\"checked\" id=\"asistencia\" name=\"asistencia\"  value=\""+ content[x].idtalentos +"\"/></td>  </tr>");
                                }else{
                                    $('#seguimiento #mytable tbody').append(" <tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td><input type=\"checkbox\" checked=\"checked\" id=\"asistencia\" name=\"asistencia\"  value=\""+ content[x].idtalentos +"\"disabled /></td>  </tr>");
                                }
                            }else{
                           
                                
                                if(editable){
                                    $('#seguimiento #mytable tbody').append("<tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\""+ content[x].idtalentos +"\"/></td>  </tr>");
                                }else{
                                    $('#seguimiento #mytable tbody').append("<tr> <td>"+ content[x].username+"</td> <td>"+ content[x].firstname +"</td> <td>"+ content[x].lastname +"</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\""+ content[x].idtalentos +"\" disabled/></td>  </tr>");
                                }
                                    
                            }
                        }
                        //console.log(Object.keys(content).length);
                        //console.log(n);
                        
                    }
                    
                }else{
                    $('#seguimiento #list_grupal_seg').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                }
            },
            dataType: "json",
            cache: "false",
            error: function(msg){alert("Error 2" + msg)},
            });
}

function update_seg_grupal(id_seg){
    
    var students_id =  new Array();
	   
    $('#seguimiento input[id=asistencia]:checked').each(
            function() {
                var id = $(this).val();
                students_id.push(id);
            }
    );  
    
	var data = $('#myModal #seguimiento').serializeArray();
	
    data.push({name:"id_seg",value:id_seg});
    data.push({name:"function",value:"update"});
    data.push({name:"tipo",value:"GRUPAL"});
    data.push({name:"idtalentos",value:students_id});
   
  
    $.each(data, function(i, item) {
                if(item.name=="optradio"){ 
                    item.value = $('#seguimiento input[name=optradio]:checked').parent().attr('id');
                }
            });
    
    // var  result = "";     
    // $.each(data, function(i, item) {
    //     result += item.name+" = "+item.value+"\n";
    // });
    // alert(result);
    
   
	$.ajax({
        type: "POST",
        data: data,
        url: "../managers/seguimiento.php",
        success: function(msg)
        {
            var error = msg.error;
            if(!error){
                 swal({title: "Actualizado con exito!!", html:true, type: "success",  text: msg.msg, confirmButtonColor: "#d51b23"});
                    $('#myModal').modal('toggle');
                    $('#myModal').modal('toggle');
    	    		$('#upd_seg').addClass('hide');
    	    		$('.modal-backdrop').remove();
    	    		loadAll_seg();
            }else{
                swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3"});
            }
            
            
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
    });
}

function initFormSeg(){

    var date = new Date();
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    var minutes = date.getMinutes();
    var hour = date.getHours();
    
//   // inicializar fecha
 
    //incializar hora
    var hora="";
    for (var i = 0; i<24;i++) {
        if(i==hour){
            if(hour<10) hour= "0"+hour;
            hora += "<option value=\""+hour+"\" selected>"+hour+"</option>";
        }else if (i<10){
            hora += "<option value=\"0"+i+"\">0"+i+"</option>";
        }else{
        hora += "<option value=\""+i+"\">"+i+"</option>";
        }
    }
    
    var min = "";
    for (var i = 0; i<60;i++) {
        
        if(i== minutes){
            if (minutes < 10 ) minutes = "0"+minutes;
            min += "<option value=\""+minutes+"\" selected>"+minutes+"</option>";
        }else if (i<10){
            min += "<option value=\"0"+i+"\">0"+i+"</option>";
        }else{
        min += "<option value=\""+i+"\">"+i+"</option>";
        }
    }
    
    
    $('#seguimiento #h_ini').append(hora);
    $('#seguimiento #m_ini').append(min);
    
    $('#seguimiento #h_fin').append(hora);
    $('#seguimiento #m_fin').append(min);
    
    $("#seguimiento").find("input:text, textarea").val('');
    $('#seguimiento #infomonitor').addClass('hide');
    $('#upd_seg').attr('disabled',false);
    $('#upd_seg').attr('title', '');
    $('#seguimiento').find('select, textarea, input').attr('disabled',false);
  
}

function loadAll_seg(){
    $('#list_grupal').html('');
    var data = new Array();
    var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
    
    data.push({name:"function",value:"loadSegMonitor"});
    data.push({name:"tipo",value:"GRUPAL"});
    data.push({name:"idinstancia", value: parameters.instanceid});
	$.ajax({
        type: "POST",
        data: data,
        url: "../managers/seguimiento.php",
        success: function(msg)
        {
            var error = msg.error;
            if(!error){
                
                var result = msg.result;
                var rows =  msg.rows;
                if(rows > 0){
                    for (x in result) {
                        
                        $('#list_grupal').append("<div class=\"container well col-md-12\"> <div class=\"container-fluid col-md-10\" name=\"info\"><div class=\"row\"><label class=\"col-md-3\" for=\"fecha_des\">Fecha</label><label class=\"col-md-9\" for=\"tema_des\">Tema</label> </div> <div class=\"row\"> <input type=\"text\" class=\"col-md-3\" value=\""+result[x].fecha+"\" id=\"fecha_seg\" name=\"fecha_seg\" disabled> <input type=\"text\" class=\"col-md-9\" value=\""+result[x].tema+"\" id=\"tema_seg\" name=\"tema_seg\" disabled> </div></div> <div id=\""+result[x].id_seg+"\" class=\"col-md-2\" name=\"div_button_seg\"> <button type=\"submit\" id=\"consult_grupal\" name=\"consult_grupal\" class=\"submit\" data-toggle=\"modal\" data-target=\"#myModal\">Detalle</button> </div></div>");
                    }
                    $('#list_grupal').on('click', '#consult_grupal', function(){
                        var id_seg = $(this).parent().attr('id');
                        $('#update_seg').removeClass('hide');
                        loadJustOneSeg(id_seg,'GRUPAL');
                    }); 
                       
                    
                }else{
                    $('#list_grupal').append("<label>No registra</label><br>");
                }
                
            }else{
                swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3"});
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg);},
    });
}

function loadJustOneSeg(id_seg,tipo){
    var data = new Array();
    var idtalentos = $('#ficha_estudiante #idtalentos').val(); 
    data.push({name:"id_seg",value:id_seg});
    data.push({name:"function",value:"loadJustOne"});
    data.push({name:"idtalentos",value:idtalentos});
    data.push({name:"tipo",value:tipo});
	$.ajax({
        type: "POST",
        data: data,
        url: "../managers/seguimiento.php",
        success: function(msg)
        {
            initFormSeg();
            
            var error = msg.error;
            if(!error){
             
                var result = msg.result;
                var rows =  msg.rows;
                if(rows > 0){
                    for (x in result) {
                        $('#seguimiento #date').val(result[x].fecha);
                        $('#seguimiento #place').val(result[x].lugar);
                        $('#seguimiento #h_ini').val(result[x].h_ini);
                        $('#seguimiento #m_ini').val(result[x].m_ini);
                        $('#seguimiento #h_fin').val(result[x].h_fin);
                        $('#seguimiento #m_fin').val(result[x].m_fin);
                        $('#seguimiento #tema').val(result[x].tema);
                        $('#seguimiento #objetivos').val(result[x].objetivos);
                        $('#seguimiento #actividades').val(result[x].actividades);
                        $('#seguimiento #observaciones').val(result[x].observaciones);
                        $('#seguimiento #monitor').text(result[x].infoMonitor);
                        $('#seguimiento #infomonitor').removeClass('hide');
                        
                        $('#seguimiento #'+result[x].act_status).children().prop('checked',true);
                        
                        load_attendance_list(result[x].attendande_listid,result[x].editable);
                        // var listid = result[x].attendande_listid;
                        // var arrayid = new Array();
                        // for ( x in listid){
                        //     arrayid.push(listid[x].id_estudiante);
                        // }
                        // $('#mytable tbody').on('change','#asistencia',function() {
                        //     alert("sdfaf0");
                        // });
                        
                        // $('#mytable tbody').find('tr').each(function() {
                        //     // var id = $(this).find('td').eq(3).html();
                        //     // if($.inArray(id,arrayid) != -1){
                        //     //     alert($(this).find('td').eq(3).find('#asistencia').val());
                        //     // }
                        //   alert('wqerq')
                        // });
                        
                        
                   
                        
                        
                        $('#upd_seg').removeClass('hide');
                        $('#upd_seg').parent().attr('id',id_seg);
                        $('#save_seg').addClass('hide');
                        
                        if (result[x].editable == false){
                            $('#upd_seg').attr('disabled',true);
                            $('#upd_seg').attr('title', 'Han trasncurrido más de 24 horas desde su creación por lo tanto no se puede actualizar');
                            $('#seguimiento').find('select, textarea, input').attr('disabled',true);
                            
                        }else{
                            $('#upd_seg').attr('disabled',false);
                            $('#upd_seg').attr('title', '');
                            $('#seguimiento').find('select, textarea, input').attr('disabled',false);
                        }
                        
                        
                        
                        
                        //se muestra los datos de creacion
                        $('#created_date').text("Creado el "+result[x].createdate);
                        $('#div_created').removeClass('hide');
                    }
 
                }else{
                    swal("No se ecnotraron resultados","warning");
                }
            }else{
                swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3"});
            }
            
            
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
    });
}


function validateModal(data){
    var isvalid = true;
    var detalle = "";
    
    
	var date,h_ini, m_ini, h_fin, m_fin, tema, objetivos, idtalentos;

	$.each(data, function(i, field){
          
        switch (field.name) {
    	case 'date':
    		date = field.value;
    		break;
    		
    	case 'h_ini':
    		h_ini = field.value;
    		break;
    		
    	case 'm_ini':
    		m_ini = field.value;
    		break;
    	
    	case 'h_fin':
    		h_fin = field.value;
    		break;
    	case 'm_fin':
    		m_fin = field.value;
    		break;
    	case 'tema':
    		tema = field.value;
    		break;
    	case 'objetivos':
    		objetivos = field.value;
    		break;
    	case 'idtalentos':
    		idtalentos = field.value;
    		break;
        }
    });
    if (!date){ 
        isvalid = false;
        detalle +="* Selecciona una Fecha de seguimiento valida: date<br>";
    }
    
    if(h_ini > h_fin){
    	isvalid = false;
        detalle +="* La hora final debe ser mayor a la inicial<br>";
    }else if(h_ini == h_fin){
    	if(m_ini > m_fin){
    		isvalid = false;
            detalle +="* La hora final debe ser mayor a la inicial<br>";
    	}
    }
    
    if(idtalentos.length === 0){
        isvalid = false;
        detalle +="* Selecciona los estudiantes que asistieron al seguimiento: "+idtalentos.length +"<br>";
    }
    
    
    if(tema == ""){
        isvalid = false;
        detalle +="* La informacion de \"observaciones\" es obligatoria :"+ tema+"<br>";
    }
    
    if(objetivos == ""){
        isvalid = false;
        detalle +="* La informacion de \"Objetivos\" es obligatoria:"+ objetivos+"<br>";
    }
    
    var result= {isvalid:isvalid, detalle:detalle};
    
    
    return result;
}