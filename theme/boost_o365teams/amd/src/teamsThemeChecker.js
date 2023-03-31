define(['jquery'], function($) {
    return {
        init: function() {
            microsoftTeams.initialize();

            microsoftTeams.getContext(function(context) {
                theme = context.theme;
                $("body").addClass("theme_" + theme);
            });

            microsoftTeams.registerOnThemeChangeHandler(function(theme) {
                $("body").removeClass("theme_default");
                $("body").removeClass("theme_dark");
                $("body").removeClass("theme_contrast");
                $("body").addClass("theme_" + theme);
            });
        }
    };
});
