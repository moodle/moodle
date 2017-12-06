M.report_overviewstats = M.report_overviewstats || {};
M.report_overviewstats.charts = M.report_overviewstats.charts || {};
M.report_overviewstats.charts.courses = {

    /**
     * @method init
     * @param data
     */
    init: function(data) {
        var sizes = new Y.Chart({
            type: "column",
            categoryKey: "course_size",
            horizontalGridlines: true,
            verticalGridlines: true,
            axes: {
                values: {
                    labelFormat: {
                        decimalPlaces: 1
                    }
                }
            },
            dataProvider: data.sizes
        });

        Y.one("#chart_courses_sizes").setStyle("backgroundImage", "none");
        sizes.render("#chart_courses_sizes");
    }
};
