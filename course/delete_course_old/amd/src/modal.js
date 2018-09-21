define(['jquery'], function($) {
 
    return{
        init:function(){

        	$('.eliminar_modal').click(function(){
									id = $(this).attr('idcourse');

									$.get( 'count_teacher.php', { id: id }, function( data ) {
										if (data.total==1) {
											contenido='Usted está a punto de eliminar el curso con todos sus contenidos. Esta acción no puede deshacerse </br>¿Esta Seguro de borrar el curso?';
										$('#info-teacher-course').html(contenido);
										$('#myModal').modal('show');
										}
										else{
											contenido='<div class=alert alert-error>Cuidado este curso tiene otros profesores</div>';
											$('#info-teacher-course').html(contenido);
											for (var i =0; i <=data.profesores.length-1; i++) {
												$('#info-teacher-course').append('<strong>'+data.profesores[i]+'</strong><br>');

											}
											$('#info-teacher-course').append('<br>¿Esta Seguro de continuar con la operación de borrado del curso?. Recuerde que esta acción no puede deshacerse.');
											$('#myModal').modal('show');

										}


								}, 'json');

									$('#eliminar').click(function () {
											window.location.assign('log-and-delete-files.php?id='+id);
									})
							});
      

    }
}
    

});


