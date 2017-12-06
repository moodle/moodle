M.report_overviewstats = M.report_overviewstats || {};
M.report_overviewstats.charts = M.report_overviewstats.charts || {};
M.report_overviewstats.charts.enrolments = {

    /**
     * @method init
     * @param data
     */
    init: function(data) {
        var lastmonth = new Y.Chart({
            type: "combo",
            dataProvider: data.lastmonth,
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

        Y.one("#chart_enrolments_lastmonth").setStyle("backgroundImage", "none");
        lastmonth.render("#chart_enrolments_lastmonth");

        var lastyear = new Y.Chart({
            type: "combo",
            dataProvider: data.lastyear,
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

        Y.one("#chart_enrolments_lastyear").setStyle("backgroundImage", "none");
        lastyear.render("#chart_enrolments_lastyear");
    }
};
