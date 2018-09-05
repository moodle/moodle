define(['jquery'], function($) {
 
    return {
        init: function() { 
            $(".items > li:first-child > a").addClass("expanded");
            $(".items > li:first-child .sub-items").css("display","block");

            $(".items > li > a").click(function(e) {
                e.preventDefault();
                var $this = $(this);
                if ($this.hasClass("expanded")) {
                    $this.removeClass("expanded");
                } else {
                    $(".items a.expanded").removeClass("expanded");
                    $this.addClass("expanded");
                    $(".sub-items").filter(":visible").slideUp("normal");
                }
                $this.parent().children("ul").stop(true, true).slideToggle("normal");
            });

            $(".sub-items a").click(function() {
                $(".sub-items a").removeClass("current");
                $(this).addClass("current");
            });
        }
    }
});
