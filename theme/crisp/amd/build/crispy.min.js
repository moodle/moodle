define(['jquery'], function($) {
 
    return {
        init: function() {
            $('ul.menulist li> a.main').each(function () {
                $(this).hover(function (e) {
                e.preventDefault();
                $(this).toggleClass("newmainlist");
                });
            });
            $('div.adminset').css('display','none');
            $('a.admin-sets').click(function(e){
                e.preventDefault();
                console.log(1);
                if($(this).hasClass('shown')) {
                $(this).removeClass('shown');
                $(this).next().fadeOut("slow");
                } else {
                $(this).addClass('shown');
                $(this).next().fadeIn("slow");
                }
            });
            $('#main-navigation li').each(function(){
                $(this).hover(function(e){
                e.preventDefault();
                if($(this).hasClass('shown')) {
                $(this).removeClass('shown');
                $(this).css('background','none');
                $(this).find('ul').css('display','none');
                } else {
                $(this).addClass('shown');
                $(this).css('background','#D51B23');
                $(this).find('ul').css('display','block');
                }
                });
            });

            $('#main-navigation2 li').each(function(){
                $(this).hover(function(e){
                e.preventDefault();
                if($(this).hasClass('shown')) {
                $(this).removeClass('shown');
                $(this).find('ul').css('display','none');
                } else {
                $(this).addClass('shown');
                $(this).find('ul').css('display','block');
                }
                });
            });
        }
    };
});
