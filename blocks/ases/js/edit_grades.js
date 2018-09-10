$(document).ready(function() {
  
    var notas_viejas= new Array();
    var porcentajes = new Array();
    var notas = new Array();
  
    
//Boton de editar notas
  $(this).on("click", "span.editar", function() {
   

    notas = [];
    notas_viejas = [];
    porcentajes = [];
    var boton = $(this).parent();
   
    var inputs = boton.parent().next();
    
    inputs.find("input.item").removeAttr("readonly");
    boton.html("<span class = 'aceptar' >Guardar </span>  &nbsp;&nbsp;<span class = 'cancelar' >Cancelar </span>");

    var id_curso = inputs.find('table').attr("id");
    var ident = '#' + id_curso+ ' input';

    $(ident).each(function(){
        var inp = $(this);
        // PENDIENTE COMPROBAR QUE SEAN PROM, o PondPROM 
        
        notas_viejas.push(inp.val());
        // console.log(inp.parent().prev().html());
        }); 
        
        
  });

//Boton de GUARDAR
  $(this).on("click", "span.aceptar", function() {
    
    var boton = $(this).parent();
    var inputs = boton.parent().next();
    
    var ids = inputs.find('table').attr("id");
    var id_curso = ids.split("-")[0];
    var ident = '#' + ids + ' input';
    
    var id_estudiante = ids.split("-")[1];
    var aggregation = inputs.find("input").parent().attr("id");//NO LO ESTOY USANDO AUN....
    var items = new Array();
    var categorias = new Array();
    $(ident).each(function(){
        var inp = $(this);
        
        notas.push(inp.val()); 
        items.push(inp.attr("id").split("-")[0]);
        porcentajes.push(inp.attr("id").split("-")[1])
        categorias.push(inp.parent().attr("id").split("-")[1]);
        }); 
        
        

        var bool;
        
        if(are_changes(notas_viejas, notas)){
            bool = update_notas(id_estudiante, items, notas_viejas, notas, porcentajes, categorias);
        }else{
            swal({title: "No se han realizado cambios.", html:true, type: "success",  text: "Exito", confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) { } });
            inputs.find("input").attr("readonly", true);
            boton.html("<span class = 'editar' >Editar Notas </span>");
        }
       
        if(bool){
            inputs.find("input").attr("readonly", true);
            boton.html("<span class = 'editar' >Editar Notas </span>");
            create_semesters_panel(id_estudiante);
          
        }
        
       
        notas = [];
        notas_viejas = [];
        porcentajes = [];
     
  });
  
//Boton de Cancelar         
    
  $(this).on("click", "span.cancelar", function() {
      
      var boton = $(this).parent();
      var inputs = boton.parent().next();
        notas = [];
        notas_viejas = [];
        porcentajes = [];
      
        swal({  title: "Estas seguro/a de cancelar?",   
                text: "Los cambios realizados no serán tomados en cuenta y se perderán",   
                type: "warning",  
                showCancelButton: true,   
                confirmButtonColor: "#d51b23",   
                confirmButtonText: "Si!",
                cancelButtonText: "No", 
                closeOnConfirm : true
                }, 
                function(isConfirm){ 
                    if(isConfirm) {
                        var id_curso = inputs.find('table').attr("id");
                        var ident = '#' + id_curso+ ' input[class = item]';
                        inputs.find("input").attr("readonly", true);
                        boton.html("<span class = 'editar' >Editar Notas </span>");
                        //Llenar con notas viejas! 
                        var i = 0;
                        $(ident).each(function(){
                          $(this).val(notas_viejas[i]);
                          i++;
                        }); 
                  }});
      
    });

});

function are_changes(old_n, new_n){
    
    var bool = false;
    for (var i=0; i<old_n.length; i++) {
        if(old_n[i] != new_n[i]){
            bool = true;
        }
        
    }
    return bool;
}

function update_notas(userid, items, old_n, new_n, porcentajes, categorias){
        
        var bool =false;
        var data ={user: userid,ar_items: items,notas_v: old_n, notas_n: new_n, porcent: porcentajes, categ: categorias};
        console.log(data);
        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/edit_grades_processing.php",
            async: false,
            success: function(msg)
            {
                
                var error = msg.error;
                
                if(!error){
                    
                    bool = true;
                    swal({title: "Actualizado con exito", html:true, type: "success",  text: msg.msg, confirmButtonColor: "#d51b23"}, function(isConfirm){   if (isConfirm) { } });
                
                     
                }else{
                    bool = false;
                    swal({title: error, html:true, type: "error",  text: msg.msg, confirmButtonColor: "#D3D3D3"}, function(isConfirm){   if (isConfirm) {} });
                    
                }

            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
            return bool;

}

