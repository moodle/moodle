$(document).ready(function(){
    executeAttendance();
});

function executeAttendance(){
    var code = $('#codigo').val();
    createAttendanceCourse(code);
    createAttendanceLast(code);
}

function createAttendanceCourse(code){
       $.ajax({
        type: "POST",
        data: {dat: code},
        url: "../managers/att_course_processing.php",
        success: function(msg)
        {
            $("#attendance_course").empty();
            $("#attendance_course").append('<table id="tableResultCourse" class="display" cellspacing="0" width="100%"><thead><thead></table>');
            var table = $("#tableResultCourse").DataTable(msg);
            table.column( 0 ).visible( false );
            // sumColumns(3);
        },
        
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
        })
}

function createAttendanceLast(code){
    var table = $.ajax({
        type:"POST",
        data: {dat: code},
        url: "../managers/att_last_processing.php",
        success: function(msg)
        {
            $("#attendance_last").empty();
            $("#attendance_last").append('<table id="tableResultLast" class="display" cellspacing="0" width="100%"><thead><thead></table>');
            var table = $("#tableResultLast").DataTable(msg);
            table.column( 0 ).visible( false );
        },
        dataType: "json",
        cache: "false",
        error: function(msg){console.log(msg)},
    });    
}

