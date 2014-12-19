<script type="text/javascript">
Y.on('change', submit_form, '#licenseidselector');
 function submit_form() {
     var nValue = Y.one('#licenseidselector').get('value');
    $.ajax({
        type: "GET",
        url: "company_user_create_form.ajax.php?licenseid="+nValue,
        datatype: "HTML",
        success: function(response){
            $("#licensecourseselector").html(response);
        }
    });
 }
</script>

