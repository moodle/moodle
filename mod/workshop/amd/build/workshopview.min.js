define ("mod_workshop/workshopview",["jquery"],function(a){function b(b){var c=0;b.height("auto");b.each(function(){var b=a(this).height();if(b>c){c=b}});b.height(c)}return{init:function init(){var c=a(".path-mod-workshop .userplan dt"),d=a(".path-mod-workshop .userplan dd");b(c);b(d);a(window).on("resize",function(){b(c);b(d)})}}});
//# sourceMappingURL=workshopview.min.js.map
