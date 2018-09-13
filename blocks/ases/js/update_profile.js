$(document).ready(function() {

    $('#ficha_estudiante').on('click', '#save', function() {
        // checkpermision(); // se revisan los permisos - metodo definido en checkrole.js
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es obligatorio.",
            remote: "Por favor, rellena este campo.",
            email: "Por favor, escribe una dirección de correo válida",
            url: "Por favor, escribe una URL válida.",
            date: "Por favor, escribe una fecha válida.",
            dateISO: "Por favor, escribe una fecha (ISO) válida.",
            number: "Por favor, escribe un número entero válido.",
            digits: "Por favor, escribe sólo dígitos.",
            creditcard: "Por favor, escribe un número de tarjeta válido.",
            equalTo: "Por favor, escribe el mismo valor de nuevo.",
            accept: "Por favor, escribe un valor con una extensión aceptada.",
            maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
            minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
            rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
            range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
            max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
            min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
        });

        $('#ficha_estudiante').validate({
            debug: false,
            rules: {
                cedula: {
                    required: true,
                    number: true
                },
                dir1: {
                    required: true
                },
                tel1: {
                    required: true,
                    number: true
                },
                tel2: {
                    number: true
                },
                tel3: {
                    number: true
                },
                email2: {
                    required: true,
                    email: true
                },
                nombre_acudiente: {
                    required: true,
                    lettersonly: true
                },
                tel4: {
                    required: true,
                    number: true
                },
                codigo: {
                    required: false
                }
            },
            highlight: function(element) {
                $(element).closest('.input-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.input-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            submitHandler: function(form) {
                //esta función se ejecuta cuando el formulario esta validado
                update();
            }
        });
    });


});

jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z,á,é,í,ó,ú,ñ ]+$/i.test(value);
}, "El nombre sólo debe tener letras");


function update() {
    var data = $('#ficha_estudiante').serializeArray();
    $.ajax({
        type: "POST",
        data: data,
        url: "../managers/update_profile.php",
        success: function(msg) {
            var error = msg.error;
            if (!error) {
                swal({
                    title: "Actualizado con exito",
                    html: true,
                    type: "success",
                    text: msg.msg,
                    confirmButtonColor: "#d51b23"
                }, function(isConfirm) {
                    if (isConfirm) {
                        blockall(this);
                    }
                });
            }
            else {
                swal({
                    title: error,
                    html: true,
                    type: "error",
                    text: msg.msg,
                    confirmButtonColor: "#D3D3D3"
                }, function(isConfirm) {
                    if (isConfirm) {
                        blockall(this);
                    }
                });
            }
        },
        dataType: "json",
        cache: "false",
        error: function(msg) {
            console.log(msg)
        },
    })
}

function blockall($this) {
    $("#ficha_estudiante").find("input, textarea").prop("readonly", true);
    $("#ficha_estudiante").find("select").prop("disabled", true);
    $("#cancel").hide();
    $("#save").fadeOut();
    $("#editar_ficha").fadeIn();
    $('#search').fadeIn();
    $('#codigo').attr('readonly', false);
}
