$(document).ready(function () {
    // Render tabs
    $("#tabdiv").tabs();
    $('ul.tabset_tabs li').click(
        function () {
            $("html, body").animate({ scrollTop: 0 }, "slow");
        }
    );

    // Add listeners to dropdowns
    $('select').change(
         function() {
            $(this).parents("form").submit();
        }
    );
});
