define(['jquery', 'core/str'], function ($, str) {
    return {
        init: function () {
            $('.choicegroup-memberdisplay').click( function (e) {
                e.preventDefault();
                $('.choicegroups-membersnames').toggleClass('hidden');
                var showusersstring = str.get_string('showgroupmembers', 'mod_choicegroup');
                var hideusersstring = str.get_string('hidegroupmembers', 'mod_choicegroup');
                if ($('.choicegroups-membersnames').is(":visible")) {
                    $.when(hideusersstring).done(function (hidestring) {
                        $(".choicegroup-memberdisplay").html(hidestring);
                    });
                }
                else {
                    $.when(showusersstring).done(function (showstring) {
                        $(".choicegroup-memberdisplay").html(showstring);
                    });
                }

            });

            $('.choicegroup-descriptiondisplay').click( function (e) {
                e.preventDefault();
                $('.choicegroups-descriptions').toggleClass('hidden');
                var hidedescriptionstring = str.get_string('hidedescription', 'mod_choicegroup');
                var showdescriptionstring = str.get_string('showdescription', 'mod_choicegroup');
                if ($('.choicegroups-descriptions').is(":visible")) {
                    $.when(hidedescriptionstring).done(function (hidestring) {
                        $(".choicegroup-descriptiondisplay").html(hidestring);
                    });
                }
                else {
                    $.when(showdescriptionstring).done(function (showstring) {
                        $(".choicegroup-descriptiondisplay").html(showstring);

                    });
                }
            });
        }
    };

});