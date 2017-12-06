M.report_overviewstats = M.report_overviewstats || {};
M.report_overviewstats.charts = M.report_overviewstats.charts || {};
M.report_overviewstats.charts.langs = {

    /**
     * @method init
     * @param data
     */
    init: function(data) {
        var chart = new Y.Chart({
            type: "bar",
            categoryKey: "language",
            verticalGridlines: true,
            axes: {
                values: {
                    labelFormat: {
                        decimalPlaces: 1
                    }
                }
            },
            dataProvider: data
        });

        Y.one("#chart_langs").setStyle("backgroundImage", "none");
        chart.render("#chart_langs");
    }
};
