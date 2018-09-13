define(['jquery'], function($) {
 
    return {
        init: function(id) {
            
            $('.checkoption').change(function(){
                var certificateid = $(this).attr('certificateid');
                var userid = $(this).attr('userid');

                var option =""
                if ($(this).is(":checked"))
                {
                    
                    option = "insert";
                    
                }
                else{
                    option = "delete";
                    
                }

                $.post( "assign_permission.php",{"userid": userid, "certificateid":certificateid, "option":option}, function( data ) {
                  $( ".result" ).html( data );
                });
                
            });        

        }

    }


});
