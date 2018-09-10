$(document).ready(function() {
    //document.location.href = document.location.href;
    //*************************************************
    
    //Se declara una variable con una etiqueta donde posteriormente se cargara el codigo html del modal 
    var $div = $('<div>');
    
    //se añade el boton Crear categorias e items bajo el boton Activar edicion que viene en el calificador de moodle
    $("div.singlebutton").append("<span type='button' formmethod='post' class='btn btn-info btn-lg' data-toggle='modal' data-target='#myModal'>Crear categorias e items</span>");
    
    //se carga el modal en la variable
    $div.load('/blocks/ases/js/archivo_testing_buttons.html');
    
    //se añade el modal despues de la region main
    $("#region-main").append($div);
    
    
    //se toma la url actual y se toma el id de la asignatura
        var informacionUrl = window.location.search.split("&");
        for(var i=0;i<informacionUrl.length;i++)
        {
            var elemento = informacionUrl[i].split("=");
            if(elemento[0]=="?id"||elemento[0]=="id")
            {
                var curso=elemento[1];
                
            }
        }
        
        //se crea un arreglo de categorias que posteriormente sera utilizado
        var arregloCategorias = new Array();
        
        $.ajax({
        type: "POST",
        data: {course: curso,tipo:"cargarCat"},
        url: "../../../blocks/ases/managers/crear_categoriasItems_processing.php",
        success: function(msg)
        {
            //el arreglo que viene es asignado a la variable arregloCategorias que posteriormente sera usado
            arregloCategorias = msg;
            
            //se crea un select con las categorias que tenga la asignatura
            var newSelect = '<select id="divCategoriaPadre" name="divCategoriaPadre" class="selectPadre">';
            newSelect=newSelect.concat('<option value="inicio">Seleccione categoria padre...</option>');
            for(var i= 0 ; i < msg.length;i++)
            {
                var opcion='<option>'+msg[i][1]+'</option>';
                newSelect=newSelect.concat(opcion);
                
            }
            
            newSelect=newSelect.concat('</select>');
            
            //se reemplaza el div de categorias que tiene el modal
            $('#divCategoriaPadre').html(newSelect);
 
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
        });
        //se termina de llenar el select de categorias
        //*************************************************

    //se añade un evento cuando se pierde el foco del campo de valor  
	$(this).on('blur','#div1 #inputValor', function()
        {
        //se revisa que el elemento digitado cumpla con las restricciones
        var numero = document.getElementById('inputValor').value;  
        
            //si no cumple con la restriccion de estar entre 0 y 100 entonces se realiza el aviso y se pone el valor en 0
            if(numero<0 || numero> 100)
            {
                swal({title: "El valor debe estar entre 0 y 100\n\rusted ingreso: " + numero , html:true, type: "warning",  confirmButtonColor: "#d51b23"});
                document.getElementById('inputValor').value= 0;
            }
        });
	
	//Se añade un evento cuando se cambia la categoria padre seleccionada
	$(this).on('change','#divCategoriaPadre',function() 
    {
        //se toma el indice de la nueva categoria seleccionada, se le resta 1 debido a que al momento de crear el 
        //select se añadio una opcion de Seleccione categoria padre...
        var indice=(document.getElementById('divCategoriaPadre').selectedIndex)-1;
        
        //tener en cuenta que el arreglo de categorias viene con el nombre de la categoria y un numero que
        //representa si es de valor promedio ponderado , promedio promedio etc.
        if (indice <0)
        {
            //desactivar y reiniciar los elementos
            document.getElementById("divCategoriaPadre").selectedIndex=0; 
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";
        }
        else if(arregloCategorias[indice][2]==0)
        {
            //si la categoria es promedio simple se desactivan los campos correspondientes
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";
        }else if(arregloCategorias[indice][2]==10)
        {
            //si la categoria es promedio ponderado se desactivan los campos correspondientes
            document.getElementById('inputValor').disabled=false;
            document.getElementById('inputValor').value="";
        }else
        {
            //ya que el asistente solo funciona para categorias promedio ponderado y simple entonces se da el aviso
            swal({title: "El asistente solo funciona con categorias de tipo promedio ponderado y promedio simple" , html:true, type: "warning",  confirmButtonColor: "#d51b23"});
            document.getElementById("divCategoriaPadre").selectedIndex=0; 
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";
        }
    });
	
	
	//si se oprime el boton cerrar se recarga la pagina
	$(this).on('click','.modal-footer #close',function() {
	    
	     document.location.href = document.location.href;
	    
	});
    
    //si se oprime el boton aceptar y salir se crea el item/categoria y se recarga la pagina para reigstrar los cambios
	$(this).on('click','.modal-footer #acepsalir',function() {
	    
	    crearCategoriaOItem();
	    
	    document.location.href = document.location.href;
	});
	
	//si se orpime el boton aceptar se crea el item/categoria, los cambios no son reflejados en el calificador hasta que se recarge la pagina
	$(this).on('click','.modal-footer #acept',function() {
	    crearCategoriaOItem();
	});

	
	//se añade el metodo que solo permite aceptar numeros
	$(this).on('keypress','#div1 #inputValor',function (e){
        tecla = (document.all) ? e.keyCode : e.which;
    
        //Tecla de retroceso para borrar y el punto(.) siempre la permite
        if (tecla==8||tecla==46){
            return true;
        }
            
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
        
    });
    
    //se añade el metodo que verifica si se selecciona categoria o item en el combo correspondiente, asi activa cierta parte de la interfaz
    //segun corresponda
    $(this).on('change','#div1 #tipoItem',function (){
        var indice = document.getElementById('tipoItem').selectedIndex;
        if(indice==2)
        {
            document.getElementById('tipoCalificacion').disabled=true;
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";
        }else if(indice==1)
        {
            document.getElementById('tipoCalificacion').disabled=false;
        }else if(indice==0){
            document.getElementById('tipoCalificacion').disabled=true;
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";
        }
    });
	
	
	//funcion utilizada por los botones guardar y crear nuevo y guardar y salir
    function crearCategoriaOItem() 
    {
    // //se instancia el tipo de item para definir que metodo se usara
    var tipoItem = $("#tipoItem").val();
 
    //si el tipo de item que se añadira es una categoria
    if((tipoItem=="Categoria"))
    {
        //se verifica que elementos generales como el nombre y el padre esten definidos
        var nombre = $("#inputNombre").val();
        var indice=(document.getElementById('divCategoriaPadre').selectedIndex)-1;
        if(!(nombre=="")&&!(indice<0))
        {
            //si la categoria es de tipo promedio simple entonces el campo valor estara vacio, por esta razon
            //se revisa previamente si es simple y en caso de ser asi se asigna 0 al valor, caso contrario se toma la informacion
            if(arregloCategorias[indice][2]==0)
            {
                valor=0;
            }else
            {
                var valor = $("#inputValor").val();
            }
        
            //se verifica que se este enviando un valor
            if(valor.length!=0)
            {
            
                var tipoCalificacion = $("#tipoCalificacion").val();
            
                //si el tipo de calificacion de la categoria sera ponderada se asigna el valor de 10 que se enviara
                if(tipoCalificacion=="Ponderado")
                {
                    var ponderado=10;
                }else
                {
                    var ponderado=0;
                }
    
                //se toma el id del padre segun el elemento elegido en el combo de categoria padre
                var padre=arregloCategorias[indice][0];
                
                //ajax que es utilizado para la creacion de la categoria
                $.ajax({
                type: "POST",
                data: {course: curso,parent:padre,fullname:nombre,agregation:ponderado,tipo:tipoItem,peso:valor},
                url: "../../../blocks/ases/managers/crear_categoriasItems_processing.php",
                async:false,
                success: function(msg)
                {
                    //se recibe el mensaje, si el ingreso fue exitoso entonces se recarga el combo de categorias padre
                    if(msg=="ok")
                    {
                    swal({title: "Categoria añadida con exito", html:true, type: "success",  confirmButtonColor: "#d51b23"});
                    $.ajax({
                    type: "POST",
                    data: {course: curso},
                    url: "../../../blocks/ases/managers/cargar_categorias_processing.php",
                    async:false,
                    success: function(msg)
                    {
                        
                    arregloCategorias = msg;
                    var newSelect = '<select id="divCategoriaPadre" name="divCategoriaPadre" class="selectPadre">';
                    newSelect=newSelect.concat('<option value="inicio">Seleccione categoria padre...</option>');
                    for(var i= 0 ; i < msg.length;i++)
                    {
                        var opcion='<option>'+msg[i][1]+'</option>';
                        newSelect=newSelect.concat(opcion);
                    }
                    newSelect=newSelect.concat('</select>');
                    $('#divCategoriaPadre').html(newSelect);
 
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg){console.log(msg)},
                    });
            
                    }else
                    {
                        //si no fue exitosa la creacion se envia el mensaje de alerta
                        swal({title: "Error al añadir la categoria", html:true, type: "error",  confirmButtonColor: "#d51b23"});
                    }
                
                },
                dataType: "text",
                cache: "false",
                error: function(msg){swal({title: "Error al intentar añadir la categoria", html:true, type: "error",  confirmButtonColor: "#d51b23"})},
                });
        
            //Se reinician los campos del modal
            document.getElementById("tipoItem").selectedIndex=0;
            $("#inputNombre").val("");
            document.getElementById("divCategoriaPadre").selectedIndex=0;
            document.getElementById("tipoCalificacion").selectedIndex=0;
            document.getElementById("tipoCalificacion").disabled=false;
            $("#inputValor").val("");
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";
            
            }else
            {
                //en caso que no haya instanciado todos los elementos que se enviaran
                swal({title: "Porfavor llene todos los campos habilitados", html:true, type: "warning",  confirmButtonColor: "#d51b23"});
            }
        

            
        }else{
            swal({title: "Porfavor llene todos los campos habilitados", html:true, type: "warning",  confirmButtonColor: "#d51b23"});
        }
        
    //en caso de que se vaya a crear un Item
    }else if((tipoItem=="Item"))
    {
        //se verifica que elementos generales como el nombre y el padre esten definidos
        var nombre = $("#inputNombre").val();
        var indice=(document.getElementById('divCategoriaPadre').selectedIndex)-1;
        if(!(nombre=="")&&!(indice<0))
        {
            //si la categoria es de tipo promedio simple entonces el campo valor estara vacio, por esta razon
            //se revisa previamente si es simple y en caso de ser asi se asigna 0 al valor, caso contrario se toma la informacion
            if(arregloCategorias[indice][2]==0)
            {
                valor=0;
            }else
            {
                var valor = $("#inputValor").val();
            }
        
            //se verifica que se este enviando un valor
            if(valor.length!=0)
            {
                //se toma el id del padre segun el elemento elegido en el combo de categoria padre
                var padre=arregloCategorias[indice][0];
                
                //ajax que es utilizado para la creacion de la categoria
                $.ajax({
                type: "POST",
                data: {course: curso,parent:padre,fullname:nombre,tipo:tipoItem,peso:valor},
                url: "../../../blocks/ases/managers/crear_categoriasItems_processing.php",
                async:false,
                success: function(msg)
                {   
                    //se recibe el mensaje, si el ingreso fue exitoso entonces se recarga el combo de categorias padre
                    if(msg=="ok")
                    {
                        swal({title: "item añadido con exito", html:true, type: "success",  confirmButtonColor: "#d51b23"});
                    }else
                    {
                        swal({title: "Error al añadir el item", html:true, type: "error",  confirmButtonColor: "#d51b23"});
                    }
            
                },
                dataType: "text",
                cache: "false",
                error: function(msg){swal({title: "Error al intentar añadir el item", html:true, type: "error",  confirmButtonColor: "#d51b23"})},
                });
    
                }else
                {
                   swal({title: "Porfavor llene todos los campos habilitados", html:true, type: "warning",  confirmButtonColor: "#d51b23"});
                }

            //Se reinician los campos del modal
            document.getElementById("tipoItem").selectedIndex=0;
            $("#inputNombre").val("");
            document.getElementById("divCategoriaPadre").selectedIndex=0;
            document.getElementById("tipoCalificacion").selectedIndex=0;
            document.getElementById("tipoCalificacion").disabled=false;
            $("#inputValor").val("");
            document.getElementById('inputValor').disabled=true;
            document.getElementById('inputValor').value="";

        }else{
            //en caso que no haya instanciado todos los elementos que se enviaran
            swal({title: "Porfavor llene todos los campos habilitados", html:true, type: "warning",  confirmButtonColor: "#d51b23"});
        }
    }else
    {
        swal({title: "Seleccione el tipo de elemento que desea crear", html:true, type: "warning",  confirmButtonColor: "#d51b23"});
    }
	}
});




