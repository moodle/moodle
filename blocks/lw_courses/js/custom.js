require(["jquery"],function($) {
    var gridsize = parseInt($(".block_lw_courses .startgrid").attr("grid-size"), 10);
    var listview = ["col-md-12", "span12", "list"];
    var gridview = ["col-md-" + gridsize, "span" + gridsize, "grid"];

    $(".lw_courses_list .coursebox").click(function() {
        window.location = $(this).find("h2.title").find("a").attr("href");
    });
    $("#box-or-lines").click(function(e) {
        e.preventDefault();
        $(this).toggleClass("grid");
        $(listview).each(function(i, v) {
            $(".lw_courses_list .coursebox").toggleClass(v);
        });
        $(gridview).each(function(i, v) {
            $(".lw_courses_list .coursebox").toggleClass(v);
        });
    });
});