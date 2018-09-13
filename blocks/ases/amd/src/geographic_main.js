 /**
 * Load and save geographic information
 * @module amd/src/geographic_main
 * @author Iader E. García Gómez
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */ 

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function(){

            var id_ases = $('#id_ases').val()

            $('#button_edit_geographic').on('click', function(){
                $('#button_edit_geographic').attr('hidden', true);
                $('#div_save_buttons').removeAttr('hidden');
                $('#select_neighborhood').removeAttr('disabled');
                $('#select_geographic_risk').removeAttr('disabled');
                $('#latitude').removeAttr('disabled');
                $('#longitude').removeAttr('disabled');
            });

            $('#button_cancel_geographic').on('click', function(){
                $('#button_edit_geographic').removeAttr('hidden');
                $('#div_save_buttons').attr('hidden', true);
                $('#select_neighborhood').attr('disabled', true);
                $('#select_geographic_risk').attr('disabled', true);
                $('#latitude').attr('disabled', true);
                $('#longitude').attr('disabled', true);
            });

            //load_geographic_info(id_ases);

            $('#button_save_geographic').on('click', function(){
                var latitude = $('#latitude').val();
                var longitude = $('#longitude').val();
                var neighborhood = $('#select_neighborhood').val();
                var geographic_risk = $('#select_geographic_risk').val();

                save_geographic_info(id_ases, latitude, longitude, neighborhood, geographic_risk);

            });

            /**
     * @method load_geographic_info
     * @desc Loads all geographic info of a student given his id. Current processing on geographic_serverproc.php
     * @param {id} id_ases ASES student id
     * @return {void}
     */
    function load_geographic_info(id_ases){
        $.ajax({
            type: "POST",
            data: {
                func: 'load_geographic_info',
                id_ases: id_ases
            },
            url: "../managers/student_profile/geographic_serverproc.php",
            success: function(msg) {

                console.log(msg);
            },
            dataType: "json", //Json format
            cache: "false",
            error: function(msg) {
                console.log(msg);
            },
        });
    }

    /**
     * @method save_geographic_info
     * @desc Saves a student geographic information. Current processing on geographic_serverproc.php
     * @param {integer} id_ases ASES student id
     * @param {float} latitude latitude coordenate
     * @param {float} longitude longitude coordenate
     * @param {id} neighborhood neighborhood name
     * @param {id} geographic_risk geographic risk according to the neighborhood
     */
    function save_geographic_info(id_ases, latitude, longitude, neighborhood, geographic_risk){

        $.ajax({
            type: "POST",
            data: {
                func: 'save_geographic_info',
                id_ases: id_ases,
                latitude: latitude,
                longitude: longitude,
                neighborhood: neighborhood,
                geographic_risk: geographic_risk
            },
            url: "../managers/student_profile/geographic_serverproc.php",
            success: function(msg) {

                swal(
                    msg.title,
                    msg.text,
                    msg.type);
                
                $('#button_edit_geographic').removeAttr('hidden');
                $('#div_save_buttons').attr('hidden', true);
                $('#select_neighborhood').attr('disabled', true);
                $('#select_geographic_risk').attr('disabled', true);
                $('#latitude').attr('disabled', true);
                $('#longitude').attr('disabled', true);
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                
                swal(
                    msg.title,
                    msg.text,
                    msg.type);
            },
        });

    }

            }
        }


    

})