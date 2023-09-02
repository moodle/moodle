jQuery(document).ready(function($){


    $("#id_qbassignsubmission_onlinetex_enabled").change(function() {
        if(this.checked) {
           if( $("#id_qbassignsubmission_codeblock_enabled").is(":checked"))
            $("#id_qbassignsubmission_codeblock_enabled").trigger('click');

            
            if( $("#id_qbassignsubmission_file_enabled").is(":checked"))
            $("#id_qbassignsubmission_file_enabled").trigger('click');
        }
    });

    $("#id_qbassignsubmission_codeblock_enabled").change(function() {
        if(this.checked) {
            if( $("#id_qbassignsubmission_onlinetex_enabled").is(":checked"))
            $("#id_qbassignsubmission_onlinetex_enabled").trigger('click');

            if( $("#id_qbassignsubmission_file_enabled").is(":checked"))
            $("#id_qbassignsubmission_file_enabled").trigger('click');
        }
    });

    $("#id_qbassignsubmission_file_enabled").change(function() {
        if(this.checked) {

            if( $("#id_qbassignsubmission_codeblock_enabled").is(":checked"))
            $("#id_qbassignsubmission_codeblock_enabled").trigger('click');

            if( $("#id_qbassignsubmission_onlinetex_enabled").is(":checked"))
            $("#id_qbassignsubmission_onlinetex_enabled").trigger('click');
        }
    });

});