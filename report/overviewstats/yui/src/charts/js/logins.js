M.report_overviewstats = M.report_overviewstats || {};
M.report_overviewstats.charts = M.report_overviewstats.charts || {};
M.report_overviewstats.charts.logins = {

    /**
     * @method init
     * @param data
     */
    init: function(data) {
        var perday = new Y.Chart({
            type: "combo",
            dataProvider: data.perday,
            categoryKey: "date",
            horizontalGridlines: true,
            verticalGridlines: true,
            axes: {
                values: {
                    labelFormat: {
                        decimalPlaces: 1
                    }
                }
            },
            styles: {
                axes: {
                    date: {
                        label: {
                            rotation: -90
                        }
                    }
                }
            }

        });

        Y.one("#chart_logins_perday").setStyle("backgroundImage", "none");
        perday.render("#chart_logins_perday");

    }
};
