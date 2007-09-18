<script src="<?php echo $CFG->themewww .'/'. current_theme() ?>/js/jquery-latest.pack.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function() {
        script.init();
    });

    var script = {
        corrections: function () {
            if (top.user) {
                top.document.getElementsByTagName('frameset')[0].rows = "117,30%,0,200";
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
    }
</script>