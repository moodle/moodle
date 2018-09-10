$(document).ready(function() {
     $("#ficha_estudiante").on('click','#search',function(){
          //checkpermision(); // se revisan los permisos - metodo definido en checkrole.js

	
	    searchStudent();

	  //setTimeout(function(){ location.reload(true); }, 2000);  
     });
    
});

