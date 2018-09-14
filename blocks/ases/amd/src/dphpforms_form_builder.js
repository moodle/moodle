// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_form_builder
  */

  define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

            $(document).ready(function(){
                var JSONCondiciones = [
                                        {
                                            "disparador":"cam_xxx",
                                            "condiciones":[
                                                {
                                                    "condicion":"marcado",
                                                    "comportamiento_condicion_cumplida":[
                                                        {
                                                            "campo":"cam_xxx",
                                                            "permisos": [{"rol":0, "permisos":["lectura", "escritura"]}]
                                                        },
                                                        {
                                                            "campo":"cam_xxx+1",
                                                            "permisos": [
                                                                {"rol":0, "permisos":["lectura"]},
                                                                {"rol":1, "permisos":["lectura"]},
                                                                {"rol":2, "permisos":["lectura", "escritura"]}
                                                            ]
                                                        }
                                                    ],
                                                    "comportamiento_condicion_no_cumplida":[
                                                        {
                                                            "campo":"cam_xxx+1",
                                                            "permisos": [
                                                                {"rol":0, "permisos":["lectura", "escritura"]},
                                                                {"rol":1, "permisos":["lectura", "escritura"]},
                                                                {"rol":2, "permisos":["lectura"]}
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ];

                $("#JSON-condiciones").text(JSON.stringify(JSONCondiciones, undefined, 2)) ;
                
            });
            
            $('#btn-registro-regla').click(function(){
                var campoA = $("#campoA option:selected").val();
                var campoB = $("#campoB option:selected").val();
                var regla = $("#selector-reglas option:selected").val();
                registrarRegla(campoA, campoB, regla);
            });
            
            $('#verOpcionesAvanzadas').click(function(){
                $('#opcionesAvanzadas').fadeIn(400);
                $(this).hide();
            });
            
            $('#generarFormulario').click(function(){
                generadorFormJSON();
            });

            $('#json-store-form').click(function(){
                var json = JSON.parse($('#opt1-textarea-json').val());
                store_form(json);
            });
            
            $('#titulo-formulario').on('keyup',function(){
                $('#form-preview > h2').text($('#titulo-formulario').val());
            });
            
            $(document).on('click', '.btn-eliminar-campo' , function() {
                $(this).parent().remove();
                actualizarCamposReglas();
             });
            
             $(document).on('click', '.eliminar-regla' , function() {
                $(this).parent().remove();
                actualizarCamposReglas();
             });
            
             $(document).on('keyup', '.enunciado' , function() {
                $(this).parent().find('.label-text').text($(this).val());
             });
            
            function adicionarAlFormulario(html, id_){
                console.log('Adicionando: ' + html);
                console.log('Identificador temporal cam_' + id_);
                $('#form-preview').append(html);
                actualizarCamposReglas();
            };
            
            function actualizarCamposReglas(){
                $('#campoA').children('option').remove();
                $('#campoB').children('option').remove();
                $('#form-preview').children('div').each(function(index){
                    $('#campoA').append('<option value="'+ $(this).attr('id') +'">'+ $(this).attr('id') +'</option>');
                    $('#campoB').append('<option value="'+ $(this).attr('id') +'">'+ $(this).attr('id') +'</option>');
                });
            };
            
            function obtenerIdTemporal(){
                return Math.floor((Math.random() * 100) + 900);
            }
            
            function registrarRegla(campoA, campoB, regla){
                if( !(campoA) || !(campoB) ){
                    alert('Seleccione primero un par de campos.');
                    return;
                }
                var regla_entendible = null;
                switch (regla) {
                    case '>':
                        regla_entendible = 'Mayor que';
                        break;
                    case '<':
                        regla_entendible = 'Menor que';
                        break;
                    case 'EQUAL':
                        regla_entendible = 'Igual que';
                        break;
                    case 'DIFFERENT':
                        regla_entendible = 'Diferente que';
                        break;
                    case 'BOUND':
                        regla_entendible = 'Depende de';
                    break;
                }
                var html = '<div class="regla" data-campo-a="'+campoA+'" data-campo-b="'+campoB+'" data-regla="'+regla+'">\
                                '+campoA+' - <i>'+regla_entendible+'</i> - '+campoB+' \
                                <div class="btn btn-danger btn-xs eliminar-regla">\
                                    eliminar\
                                </div>\
                            </div>';
            
                $('#contenedor-reglas').append(html);
            }
            
            $('li').click(function(){
                
               var seleccion = $(this);
               console.log(seleccion.attr('id'));
               if(seleccion.attr('id') == 'caja-texto'){
                   var id_ = obtenerIdTemporal();
                   var html = '<div id="cam_' + id_ + '" class="form-group div-box-preview" data-tipo-Campo="TEXTFIELD">\
                                    <div  class="identificador-temporal">Identificador temporal: cam_' + id_ + '</div>\
                                    <label for="" class="label-enunciado">Enunciado</label>\
                                    <input type="text" class="form-control enunciado" name="textfield" placeholder="¿......?">\
                                    <br>\
                                    <textarea name="atributosCampo" class="form-control atributosCampo"  placeholder="Atributos: { &#34;atributo&#34;:&#34;valor&#34;, &#34;atributo&#34;:&#34;valor&#34; },\nejemplo: {&#34;type&#34;:&#34;text&#34;, &#34;placeholder&#34;:&#34;Name&#34;}"></textarea>\
                                    <br>\
                                    <strong>Permisos sobre el campo [RF]:</strong>\
                                    <textarea name="permisosCampo" class="form-control permisosCampo" >[{ "rol":0, "permisos":["lectura", "escritura"] }]</textarea>\
                                    <hr>\
                                    <label for="" class="label-text">Previsualización del enunciado</label>\
                                    <input type="text" class="form-control" name="TEXTFIELD">\
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs btn-eliminar-campo">Eliminar</a>\
                                </div>';
                   adicionarAlFormulario(html, id_);
               }
            
               if(seleccion.attr('id') == 'caja-parrafo'){
                    var id_ = obtenerIdTemporal();
                    var html = '<div id="cam_' + id_ + '" class="form-group div-box-preview" data-tipo-campo="TEXTAREA">\
                                    <div  class="identificador-temporal">Identificador temporal: cam_' + id_ + '</div>\
                                    <label for="" class="label-enunciado">Enunciado</label>\
                                    <input type="text" class="form-control enunciado" name="textfield" placeholder="¿......?">\
                                    <br>\
                                    <textarea name="atributosCampo" class="form-control atributosCampo"  placeholder="Atributos: { &#34;atributo&#34;:&#34;valor&#34;, &#34;atributo&#34;:&#34;valor&#34; },\nejemplo: {&#34;type&#34;:&#34;text&#34;, &#34;placeholder&#34;:&#34;Name&#34;}"></textarea>\
                                    <br>\
                                    <strong>Permisos sobre el campo [RF]:</strong>\
                                    <textarea name="permisosCampo" class="form-control permisosCampo" >[{ "rol":0, "permisos":["lectura", "escritura"] }]</textarea>\
                                    <hr>\
                                    <label for="" class="label-text">Previsualización del enunciado</label>\
                                    <textarea class="form-control" name="TEXTAREA"></textarea>\
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs btn-eliminar-campo">Eliminar</a>\
                                </div>';
                   adicionarAlFormulario(html, id_);
               }
            
               if(seleccion.attr('id') == 'caja-fecha'){
                    var id_ = obtenerIdTemporal();
                    var html = '<div id="cam_' + id_ + '" class="form-group div-box-preview" data-tipo-campo="DATE">\
                                    <div  class="identificador-temporal">Identificador temporal: cam_' + id_ + '</div>\
                                    <label for="" class="label-enunciado">Enunciado</label>\
                                    <input type="text" class="form-control enunciado" name="textfield" placeholder="¿......?">\
                                    <br>\
                                    <textarea name="atributosCampo" class="form-control atributosCampo"  placeholder="Atributos: { &#34;atributo&#34;:&#34;valor&#34;, &#34;atributo&#34;:&#34;valor&#34; },\nejemplo: {&#34;type&#34;:&#34;text&#34;, &#34;placeholder&#34;:&#34;Name&#34;}"></textarea>\
                                    <br>\
                                    <strong>Permisos sobre el campo [RF]:</strong>\
                                    <textarea name="permisosCampo" class="form-control permisosCampo" >[{ "rol":0, "permisos":["lectura", "escritura"] }]</textarea>\
                                    <hr>\
                                    <label for="" class="label-text">Previsualización del enunciado</label>\
                                    <input type="date" class="form-control" name="DATE">\
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs btn-eliminar-campo">Eliminar</a>\
                                </div>';
                   adicionarAlFormulario(html, id_);
               }
            
               if(seleccion.attr('id') == 'caja-hora'){
                    var id_ = obtenerIdTemporal();
                    var html = '<div id="cam_' + id_ + '" class="form-group div-box-preview" data-tipo-campo="TIME">\
                                    <div  class="identificador-temporal">Identificador temporal: cam_' + id_ + '</div>\
                                    <label for="" class="label-enunciado">Enunciado</label>\
                                    <input type="text" class="form-control enunciado" name="textfield" placeholder="¿......?">\
                                    <br>\
                                    <textarea name="atributosCampo" class="form-control atributosCampo"  placeholder="Atributos: { &#34;atributo&#34;:&#34;valor&#34;, &#34;atributo&#34;:&#34;valor&#34; },\nejemplo: {&#34;type&#34;:&#34;text&#34;, &#34;placeholder&#34;:&#34;Name&#34;}"></textarea>\
                                    <br>\
                                    <strong>Permisos sobre el campo [RF]:</strong>\
                                    <textarea name="permisosCampo" class="form-control permisosCampo" >[{ "rol":0, "permisos":["lectura", "escritura"] }]</textarea>\
                                    <hr>\
                                    <label for="" class="label-text">Previsualización del enunciado</label>\
                                    <input type="time" class="form-control" name="TIME">\
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs btn-eliminar-campo">Eliminar</a>\
                                </div>';
                   adicionarAlFormulario(html, id_);
               }
            
               if(seleccion.attr('id') == 'caja-radio'){
                    var id_ = obtenerIdTemporal();
                    var html = '<div id="cam_' + id_ + '" class="form-group div-box-preview" data-tipo-campo="RADIOBUTTON">\
                                    <div  class="identificador-temporal">Identificador temporal: cam_' + id_ + '</div>\
                                    <label for="" class="label-enunciado">Enunciado</label>\
                                    <input type="text" class="form-control enunciado" name="textfield" placeholder="¿......?">\
                                    <br>\
                                    <textarea rows="5" class="form-control opcionesCampo" placeholder="Elementos: [{ &#34;enunciado&#34;:&#34;texto&#34;, &#34;valor&#34;:&#34;valor&#34;, &#34;posicion&#34;: &#34;número&#34;}] \n\nejemplo: [{ &#34;enunciado&#34;:&#34;Sí&#34;, &#34;valor&#34;:&#34;1&#34;, &#34;posicion&#34;: &#34;0&#34;}, { &#34;enunciado&#34;:&#34;No&#34;, &#34;valor&#34;:&#34;0&#34;, &#34;posicion&#34;: &#34;1&#34;}]"></textarea>\
                                    <br>\
                                    <textarea name="atributosCampo" class="form-control atributosCampo"  placeholder="Atributos: { &#34;atributo&#34;:&#34;valor&#34;, &#34;atributo&#34;:&#34;valor&#34; },\nejemplo: {&#34;type&#34;:&#34;text&#34;, &#34;placeholder&#34;:&#34;Name&#34;}"></textarea>\
                                    <br>\
                                    <strong>Permisos sobre el campo [RF]:</strong>\
                                    <textarea name="permisosCampo" class="form-control permisosCampo" >[{ "rol":0, "permisos":["lectura", "escritura"] }]</textarea>\
                                    <hr>\
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs btn-eliminar-campo">Eliminar</a>\
                                </div>';
                   adicionarAlFormulario(html, id_);
               }
            
               if(seleccion.attr('id') == 'caja-checkbox'){
                    var id_ = obtenerIdTemporal();
                    var html = '<div id="cam_' + id_ + '" class="form-group div-box-preview" data-tipo-campo="CHECKBOX">\
                                    <div  class="identificador-temporal">Identificador temporal: cam_' + id_ + '</div>\
                                    <label for="" class="label-enunciado">Enunciado</label>\
                                    <input type="text" class="form-control enunciado" name="textfield" placeholder="¿......?">\
                                    <br>\
                                    <textarea rows="5" class="form-control opcionesCampo" placeholder="Elementos: [{ &#34;enunciado&#34;:&#34;texto&#34;, &#34;valor&#34;:&#34;valor&#34;, &#34;checked&#34;:&#34;true&#34;, &#34;posicion&#34;: &#34;número&#34;}] \n\nejemplo: [{ &#34;enunciado&#34;:&#34;Sí&#34;, &#34;valor&#34;:&#34;1&#34;, &#34;posicion&#34;: &#34;0&#34;}, { &#34;enunciado&#34;:&#34;No&#34;, &#34;valor&#34;:&#34;0&#34;, &#34;posicion&#34;: &#34;1&#34;}]"></textarea>\
                                    <br>\
                                    <textarea name="atributosCampo" class="form-control atributosCampo"  placeholder="Atributos: { &#34;atributo&#34;:&#34;valor&#34;, &#34;atributo&#34;:&#34;valor&#34; },\nejemplo: {&#34;type&#34;:&#34;text&#34;, &#34;placeholder&#34;:&#34;Name&#34;}"></textarea>\
                                    <br>\
                                    <strong>Permisos sobre el campo [RF]:</strong>\
                                    <textarea name="permisosCampo" class="form-control permisosCampo" >[{ "rol":0, "permisos":["lectura", "escritura"] }]</textarea>\
                                    <hr>\
                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs btn-eliminar-campo">Eliminar</a>\
                                </div>';
                   adicionarAlFormulario(html, id_);
               }
            });
            
            
            function generadorFormJSON(){
                
                var contador = 0;
                var preguntas = JSON.parse('[]');
                var identificadores = JSON.parse('[]');
                $('#form-preview').children('div').each(function(index){
            
                    var pregunta = '';
                    
                    identificadores.push(JSON.parse('{ "id_temporal":"' + $(this).attr('id') + '", "id_nuevo":"cam_' + contador + '" }'));
                    var nuevo_id = 'cam_' + contador;
            
                    var opciones = "null";
                    if($(this).find('.opcionesCampo').val()){
                        opciones = $(this).find('.opcionesCampo').val();
                    };
                    var atributos = "null";
                    if($(this).find('.atributosCampo').val()){
                        atributos = $(this).find('.atributosCampo').val();
                    };
                    var permisos = "null";
                    if($(this).find('.permisosCampo').val()){
                        permisos = $(this).find('.permisosCampo').val();
                    };
            
                    pregunta =  '\
                    {\
                            "id_temporal":"'+ nuevo_id +'",\
                            "enunciado":"'+$(this).find('.enunciado').val()+'",\
                            "tipo_campo":"'+$(this).attr('data-tipo-campo')+'",\
                            "opciones_campo":'+ opciones +',\
                            "atributos_campo":'+ atributos +',\
                            "permisos_campo":'+ permisos +'\
                        }\
                    ';
            
                    preguntas.push(JSON.parse(pregunta));
                    pregunta = "";
                    contador = contador + 1;                
                });
            
                var reglas = JSON.parse('[]');
                $('#contenedor-reglas').children('.regla').each(function(index){
                    var campoA = $(this).attr('data-campo-a');
                    var campoB = $(this).attr('data-campo-b');
                    var regla = $(this).attr('data-regla');
            
                    for(var k in identificadores) {
                        if(identificadores[k].id_temporal == campoA){
                            campoA = identificadores[k].id_nuevo;
                        }
                        if(identificadores[k].id_temporal == campoB){
                            campoB = identificadores[k].id_nuevo;
                        }
                    };
            
                    reglas.push(JSON.parse('{\
                            "id_temporal_campo_a":"' + campoA + '",\
                            "id_temporal_campo_b":"' + campoB + '",\
                            "regla":"' + regla + '"\
                        }'));
                });
            
                var disparadores = JSON.parse('[]');;
                if($('#disparadores').val()){
                    disparadores = $('#disparadores').val();
                    for(var x in identificadores) {
                        disparadores = disparadores.replaceAll(identificadores[x].id_temporal, identificadores[x].id_nuevo);
                    };
                    
                    disparadores = JSON.parse(disparadores);
                };
            
                var formulario = {
                    "datos_formulario":{
                        "nombre": $('#titulo-formulario').val(),
                        "descripcion": $('#desc-formulario').val(),
                        "method": $('#metodo-formulario').val(),
                        "action": $('#procesador-formulario').val(),
                        "enctype": $('#enctype-formulario').val()
                    },
                    "preguntas":preguntas,
                    "disparadores": disparadores,
                    "reglas":reglas
                };
                store_form(formulario);
            };

            function store_form(form){
                console.log(form);
                $.ajax({
                    method: "POST",
                    url: "../managers/dphpforms/dphpforms_forms_processor.php",
                    contentType: "application/json",
                    dataType: "text",
                    data: JSON.stringify(form) ,
                    success: function( msg ){
                            alert( msg );
                            console.log(msg);
                    },
                    error: function( XMLHttpRequest, textStatus, errorThrown ) {
                        alert( "some error " + textStatus + " " + errorThrown );
                        console.log( "some error " + textStatus + " " + errorThrown );
                        console.log( XMLHttpRequest );
                    }
                });
            }

            $(".limpiar").click(function(){
                $(this).parent().find("div").each(function(){
                    $(this).find("label").find("input").prop("checked", false);
                });
            });
            
            String.prototype.replaceAll = function(search, replace) {
                if (replace === undefined) {
                    return this.toString();
                }
                return this.split(search).join(replace);
            };
                
            }

  }
      
});