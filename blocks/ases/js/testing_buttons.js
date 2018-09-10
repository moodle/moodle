$(document).ready(function() {
   
    var $div = $('<div>');

    $("div.singlebutton").append("<span type='button' formmethod='post' class='btn btn-info btn-lg' data-toggle='modal' data-target='#myModal'>Open Modal</span>");

    $div.load('/blocks/ases/js/archivo_testing_buttons.html');

    $("#region-main").append($div);


});