$( document ).ready(function(){
    console.log('load elegance js');
    var offset = 220;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } else {
            jQuery('.back-to-top').fadeOut(duration);
        }
    });

    jQuery('.back-to-top').click(function(event) {
        event.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, duration);
        return false;
    })

    $('body').show();
    $('.version').text(NProgress.version);
    NProgress.start();
    setTimeout(function() { NProgress.done(); $('.fade').removeClass('out'); }, 1000);

    $("#b-0").click(function() { NProgress.start(); });
    $("#b-40").click(function() { NProgress.set(0.4); });
    $("#b-inc").click(function() { NProgress.inc(); });
    $("#b-100").click(function() { NProgress.done(); });

    if ($('#page-login-index').length > 0 ) {
        var ajaxurl = M.cfg.wwwroot+'/theme/elegance/ajax/themesettings.php';
        $.ajax({
          url: ajaxurl,
          cache: false,
          data: { setting: "loginbackgrounds", sesskey: M.cfg.sesskey}
        }).done(function( msg) {
            if (msg.result == 'success') {
                $.backstretch(msg.loginimages, {'duration': 10000, 'fade': 750});
            }
        });
    }
    
});