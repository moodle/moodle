 /**
 * Academic report management
 * @module amd/src/academic_reports
 * @author Camilo José Cruz rivera
 * @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    return {

        /**
         *
         */
        init: function() {

            $(document).ready(function(){
                if($(".bajo").length != 0){
                    $(".bajo").parent().parent().parent().parent().prev().toggleClass('bajo');
                }
                if($(".estimulo").length != 0){
                    $(".estimulo").parent().parent().parent().parent().prev().toggleClass('estimulo');
                }
                if($(".cancelacion").length != 0){
                    $(".cancelacion").parent().parent().parent().parent().prev().toggleClass('cancelacion');
                }

                if(parseInt($('.est').text()) > 0){
                    $('.est').parent().toggleClass('estimulo');
                }

                if(parseInt($('.baj').text()) > 0){
                    $('.baj').parent().toggleClass('bajo');
                }

                if(parseFloat($('.prom').text()) < 3){
                    $('.prom').parent().toggleClass('bajo');
                }
            });
        }

    };
});