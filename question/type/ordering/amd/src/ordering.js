define(['jquery', 'jqueryui', 'qtype_ordering/jquery.ui.touch-punch-improved'], function($) {

    return {
        init: function (sortableid, responseid, ablockid, axis) {
            $('#' + sortableid).sortable({
                axis       : axis,
                containment: $('#'+sortableid),
                opacity    : 0.6,
                update     : function () {
                    var items = $(this).sortable('toArray').toString();
                    $('#' + responseid).attr('value', items);
                }
            });
        }
    };

});