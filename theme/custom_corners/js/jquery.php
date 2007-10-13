<script src="<?php echo $CFG->themewww .'/'. current_theme() ?>/js/jquery-latest.pack.js" type="text/javascript"></script>

<script type="text/javascript">
/* <![CDATA[ */
    var script = {
        corrections: function () {
            if (top.user) {
                top.document.getElementsByTagName('frameset')[0].rows = "117,30%,0,200";
            }
            
            // correct sideblock width for MSIE for columns with a calender 
            if (window.browserIE6 !== undefined || window.browserIE7 !== undefined) {
                var calendar = $('#right-column .block_calendar_month');
                if (calendar.length) {
                    var w = $('#right-column').width();
                    $('#right-column .sideblock').width(w);
                }
                calendar = $('#left-column .block_calendar_month');
                if (calendar.length) {
                    w = $('#left-column').width();
                    $('#left-column .sideblock').width(w);
                }
            }
            if (window.browserIE6 !== undefined) {
                if ($('header-home')) {
                    w = $('#header-home').width();
                    $('#header-home .i2').width(w - 24);
                }
                if ($('header')) {
                    w = $('#header').width();
                    $('#header .i2').width(w - 24);
                }
            }
            if (window.browserIE7 !== undefined) {
                if ($('header-home')) {
                    w = $('#header-home').width();
                    $('#header-home .i2').width(w - 28);
                }
                if ($('header')) {
                    w = $('#header').width();
                    $('#header .i2').width(w - 24);
                }
            }
        },
        
        info: function() {
            window.setTimeout(function(){$('#infowrapper').click();}, 4000);
            $('#infowrapper').toggle(function() {
                $('#infooverlay').animate({height: 'toggle'}, "fast");
                $(this).animate({opacity: 0.3}, "fast");
            }, function() {
                $('#infooverlay').animate({height: 'toggle'}, "fast");
                $(this).animate({opacity: 0.9}, "fast");
            });
        },
        
        init: function() {
            script.corrections();
            // script.info();
        }
    };
/* ]]> */
</script>