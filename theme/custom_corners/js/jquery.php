<script src="<?php echo $CFG->themewww .'/'. current_theme() ?>/js/jquery-latest.pack.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function() {
        init();
    });

    var init = function() {
        window.setTimeout(function(){$('#infowrapper').click();}, 4000);
        $('#infowrapper').toggle(function() {
            $('#infooverlay').animate({height: 'toggle'}, "fast");
            $(this).animate({opacity: 0.3}, "fast");
        }, function() {
            $('#infooverlay').animate({height: 'toggle'}, "fast");
            $(this).animate({opacity: 0.9}, "fast");
        });
    };
</script>