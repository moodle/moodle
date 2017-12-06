YUI.add('moodle-report_overviewstats-charts', function (Y, NAME) {

M.report_overviewstats = M.report_overviewstats || {};
M.report_overviewstats.charts = M.report_overviewstats.charts || {};
M.report_overviewstats.charts.countries = {

    /**
     * @method init
     * @param data
     */
    init: function(data) {
        var chart = new Y.Chart({
            type: "bar",
            axes: {
                values: {
                    labelFormat: {
                        decimalPlaces: 1
                    }
                }
            },
            categoryKey: "country",
            verticalGridlines: true,
            dataProvider: data
        });

        Y.one("#chart_countries").setStyle("backgroundImage", "none");
        chart.render("#chart_countries");
    }
};
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


}, '@VERSION@', {"requires": ["base", "node", "charts", "charts-legend"]});
