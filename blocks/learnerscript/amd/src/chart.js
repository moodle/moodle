define(['jquery',
        'core/ajax',
        'block_learnerscript/highcharts',
        'block_learnerscript/smartfilter',
        'block_learnerscript/report'
    ],
    function($, Ajax, Highcharts, smartfilter, report) {
        /**
         * Get highchart report for report with Ajax request
         * @param object reportid and reportdata
         * @return Generate highchart report
         */
        return chart = {
            HighchartsAjax: function(args) {
                args.cols = JSON.stringify(args.cols);
                args.instanceid = args.reportid;
                args.filters = smartfilter.FilterData(args.instanceid);
                args.basicparams = JSON.stringify(smartfilter.BasicparamsData(args.instanceid));
                args.filters['ls_fstartdate'] = $('#ls_fstartdate').val();
                args.filters['ls_fenddate'] = $('#ls_fenddate').val();
                if (typeof args.filters['filter_courses'] == 'undefined') {
                    var filter_courses = $('.dashboardcourses').val();
                    if (filter_courses != 1) {
                        args.filters['filter_courses'] = filter_courses;
                    }
                }
                args.filters = JSON.stringify(args.filters);

                //Request
                var promise = Ajax.call([{
                    methodname: 'block_learnerscript_generate_plotgraph',
                    args: args,
                }]);

                //Preload
                $('#report_plottabs').show();
                $("#plotreportcontainer" + args.instanceid).html('<img src="' + M.util.image_url('loading', 'block_learnerscript') + '" id="reportloadingimage" />');

                //Response process
                promise[0].done(function(response) {
                    response = JSON.parse(response);
                    if (response.plot) {
                        if (response.plot.error === true) {
                            $('#plotreportcontainer' + args.instanceid).html("<div class='alert alert-warning'>No data available</div>");
                        } else {
                            response.plot.reportid = args.reportid;
                            response.plot.reportinstance = args.reportid;
                            if (response.plot.data && response.plot.data.length > 0) {
                                require(['block_learnerscript/report'], function(report) {
                                    report.generate_plotgraph(response.plot);
                                });
                            } else {
                                $('#plotreportcontainer' + args.instanceid).html("<div class='alert alert-warning'>No data available</div>");
                            }
                        }
                    }
                });
            },

            //Piechart
            piechart: function(chartdata) {
                var chart = Highcharts.chart(chartdata.containerid, {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        width: chartdata.width || null,
                        height: chartdata.height || null,
                        margin: chartdata.margin || null
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: chartdata.title
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            shadow: false,
                            dataLabels: {
                                enabled: chartdata.datalabels
                            },
                            showInLegend: chartdata.showlegend
                        }
                    },
                    series: [{
                        name: chartdata.serieslabel,
                        colorByPoint: true,
                        data: chartdata.data,
                    }]
                });
            },
            //Line, Bar and column charts
            lbchart: function(chartdata) {
                var charttype = chartdata.type;
                var chartcolors = ['#336B87', '#90AFC5', '#688B8A', '#A0B084', '#FAEFD4','#A57C65', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'];
                if (typeof $(chartdata.container).data('chartcolor') != 'undefined') {
                    chartcolors = ['#' + $(chartdata.container).data('chartcolor') + ''];
                }
                var chart = Highcharts.chart(chartdata.containerid, {
                    chart: {
                        type: charttype,
                        height: chartdata.height || null,
                        zoomType: 'x',
                        styledMode: true
                    },
                    colors: chartcolors,
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: chartdata.title
                    },
                    xAxis: {
                        categories: chartdata.categorydata,
                        labels: {
                            enabled: chartdata.datalabels == 1 ? true : false
                        },
                        title: {
                            text: chartdata.ylabel
                        }
                    },
                    yAxis: {

                        title: {
                            text: chartdata.pcalcs ? chartdata.pcalcs : chartdata.title                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: chartdata.datalabels == 1 ? true : false,
                                formatter: function () {
                                  return this.point.label;
                                }
                            },
                            enableMouseTracking: chartdata.datalabels == 1 ? false : true,
                            borderRadius: 5
                        },
                        spline: {
                            dataLabels: {
                                enabled: chartdata.datalabels == 1 ? true : false,
                                 formatter: function () {
                                  return this.point.label;
                                }
                            },
                            enableMouseTracking: chartdata.datalabels == 1 ? false : true
                        },
                        column: {
                            dataLabels: {
                                enabled: chartdata.datalabels === 1 ? true : false,
                                formatter: function () {
                                  return this.point.label;
                                }
                            },
                            enableMouseTracking: chartdata.datalabels === 1 ? false : true,
                            borderRadius: 5
                        }
                    },
                    tooltip: {
                        pointFormatter: function () {
                            return '<span style="color:' + this.series.color + ';">\u25CF</span> ' + this.series.name + ' - ' + this.label;
                        }
                    },
                    credits: {
                        enabled: true
                    },
                    legend: {
                        enabled: chartdata.showlegend,
                    },
                    series: chartdata.data,
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                },
                                yAxis: {
                                    labels: {
                                        align: 'left',
                                        x: 0,
                                        y: 10
                                    },
                                    title: {
                                        text: null
                                    }
                                },
                                subtitle: {
                                    text: null
                                },
                                credits: {
                                    enabled: false
                                }
                            }
                        }]
                    }
                });
            },
            //Combination chart with line,bar and pie
            combinationchart: function(chartdata) {
                var chartcolors = ['#90AFC5', '#336B87', '#FAEFD4','#A57C65', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'];
                if (typeof $(chartdata.container).data('chartcolor') != 'undefined') {
                    chartcolors = ['#' + $(chartdata.container).data('chartcolor') + ''];
                }
                Highcharts.chart(chartdata.containerid, {
                    zoomType: 'xy',
                    title: {
                        text: chartdata.title
                    },
                    colors: chartcolors,
                    xAxis: {
                        categories: chartdata.categorydata,
                        crosshair: true,
                        labels: {
                            enabled: chartdata.datalabels == 1 ? true : false
                        }
                    },
                     yAxis: [{
                            title: {
                                text: chartdata.name
                            }
                            }, {
                                opposite: true, //optional, you can have it on the same side.
                                title: {
                                    text: chartdata.name
                                }
                            }],
                    credits: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormatter: function () {
                            return '<span style="color:' + this.series.color + ';">\u25CF</span> ' + this.series.name + ' - ' + this.label;
                        }
                    },
                    dataLabels: {
                        enabled: chartdata.datalabels == 1 ? true : false,
                        formatter: function () {
                            return this.point.label;
                        }
                    },
                    legend: {
                        enabled: chartdata.showlegend == 1 ? true : false
                    },
                    series: chartdata.data
                });
            },
            WorldMap: function(chartdata) {
                // Instanciate the map
                Highcharts.mapChart(chartdata.containerid, {
                    chart: {
                        borderWidth: 1
                    },
                    colors: ['#07575B', '#66A5AD', '#C4DFE6','#003B46', 'rgba(19,64,117,0.6)', 'rgba(19,64,117,0.8)', 'rgba(19,64,117,1)'
                    ],
                    title: {
                        text: chartdata.title
                    },
                    credits: {
                        enabled: false
                    },
                    mapNavigation: {
                        enabled: true,
                        enableMouseWheelZoom: false,
                        enableDoubleClickZoom: true
                    },
                    legend: {
                        title: {
                            text: chartdata.serieslabel,
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                            }
                        },
                        align: 'left',
                        verticalAlign: 'bottom',
                        floating: true,
                        layout: 'vertical',
                        valueDecimals: 0,
                        symbolRadius: 0,
                        symbolHeight: 14
                    },
                    colorAxis: {
                        dataClasses: [{
                            to: 3
                        }, {
                            from: 3,
                            to: 10
                        }, {
                            from: 10,
                            to: 30
                        }, {
                            from: 30,
                            to: 100
                        }, {
                            from: 100,
                            to: 300
                        }, {
                            from: 300,
                            to: 1000
                        }, {
                            from: 1000
                        }]
                    },
                    series: [{
                        data: chartdata.data,
                        mapData: Highcharts.maps['custom/world'],
                        joinBy: ['iso-a2', 'code'],
                        name: chartdata.serieslabel,
                        states: {
                            hover: {
                                color: '#a4edba'
                            }
                        },
                        shadow: false
                    }]
                });
            },
            TreeMap: function(chartdata) {
                // Instanciate the map
                Highcharts.mapChart(chartdata.containerid, {
                    credits: {
                        enabled: false
                    },
                     title: {
                        text: chartdata.title
                    },
                    series: [{
                        type: "treemap",
                        allowDrillToNode: true,
                        layoutAlgorithm: 'squarified',
                        events: {
                            click: function(event) {
                                $("#ls_courseid").val(event.point.id);
                                report.DashboardTiles();
                                report.DashboardWidgets();
                            }
                        },
                        levelIsConstant: false,
                        levels: [{
                            level: 1,
                            dataLabels: {
                                enabled: true,
                                align: 'left',
                                verticalAlign: 'top',
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        }],
                        data: chartdata.data
                    }]
                });
            },
             SparkLines: function(chartdata) {
            Highcharts.SparkLine = function(a, b, c) {
                var hasRenderToArg = typeof a === 'string' || a.nodeName,
                    options = arguments[hasRenderToArg ? 1 : 0],
                    defaultOptions = {
                        chart: {
                            renderTo: (options.chart && options.chart.renderTo) || this,
                            backgroundColor: null,
                            borderWidth: 0,
                            type: 'area',
                            margin: [2, 0, 2, 0],
                            width: 120,
                            height: 100,
                            style: {
                                overflow: 'visible'
                            },
                            // small optimalization, saves 1-2 ms each sparkline
                            skipClone: true
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            labels: {
                                enabled: false
                            },
                            title: {
                                text: null
                            },
                            startOnTick: false,
                            endOnTick: false,
                            tickPositions: []
                        },
                        yAxis: {
                            endOnTick: false,
                            startOnTick: false,
                            labels: {
                                enabled: false
                            },
                            title: {
                                text: null
                            },
                            tickPositions: [0]
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            backgroundColor: null,
                            borderWidth: 0,
                            shadow: false,
                            useHTML: true,
                            hideDelay: 0,
                            shared: true,
                            padding: 0,
                            positioner: function(w, h, point) {
                                return {
                                    x: point.plotX - w / 2,
                                    y: point.plotY - h
                                };
                            }
                        },
                        plotOptions: {
                            series: {
                                animation: false,
                                lineWidth: 1,
                                shadow: false,
                                states: {
                                    hover: {
                                        lineWidth: 1
                                    }
                                },
                                marker: {
                                    radius: 1,
                                    states: {
                                        hover: {
                                            radius: 2
                                        }
                                    }
                                },
                                fillOpacity: 0.25
                            },
                            column: {
                                negativeColor: '#910000',
                                borderColor: 'silver',
                                borderRadius: 15
                            }
                        }
                    };
                options = Highcharts.merge(defaultOptions, options);
                return hasRenderToArg ? new Highcharts.Chart(a, options, c) : new Highcharts.Chart(options, b);
            };
        },
        ProgressBar: function(chartdata) {
            Highcharts.chart(chartdata.containerid, {
                title: {
                    text: chartdata.title
                },
                credits: {
                    enabled: false
                },
                chart: {
                    renderTo: chartdata.containerid,
                    type: 'bar',
                    height: chartdata.height || 15,
                    backgroundColor: chartdata.backgroundColor || 'transparent',
                    margin: chartdata.margin || null
                },
                plotOptions: {
                    bar: { borderRadius: 5 },
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    if (typeof chartdata.data.link !== 'undefined') {
                                        require('block_learnerscript/helper').ReportModelFromLink({
                                            container: $("#" + chartdata.containerid),
                                            url: chartdata.data.link
                                        });
                                    }
                                }
                            }
                        },
                    }
                },
                exporting: {
                    enabled: false
                },
                credits: chartdata.credits || false,
                tooltip: chartdata.tooltip || false,
                legend: chartdata.legend || false,
                xAxis: {
                    visible: false,
                },
                yAxis: {
                    visible: false,
                    min: 0,
                    max: 100,
                },
                series: [{
                    data: [100],
                    grouping: false,
                    animation: false,
                    enableMouseTracking: false,
                    showInLegend: false,
                    color: '#CCC',
                    pointWidth: 15,
                    dataLabels: {
                        className: 'highlight',
                        format: chartdata.format || null,
                        enabled: false,
                        align: 'left',
                        style: {
                            color: '#0294A5',
                            textOutline: false,
                            fontSize: '9px',
                            fontWeight: 'normal'
                        }
                    }
                }, {
                    enableMouseTracking: true,
                    data: chartdata.data || [0],
                    color: '#0294A5',
                    pointWidth: 15,
                    animation: {
                        duration: 250,
                    },
                    dataLabels: {
                        enabled: true,
                        inside: true,
                        align: 'right',
                        format: '{point.y}%',
                        style: {
                            color: '#FFF',
                            textOutline: false,
                            fontSize: '9px',
                            fontWeight: 'normal'
                        }
                    }
                }]
            });
        },
        SparkLineReport: function() {
            var start = +new Date(),
                $tds = $('.spark-report'),
                fullLen = $tds.length,
                n = 0;
            var time = +new Date(),
                i,
                $td,
                len = $tds.length,
                stringdata,
                arr,
                data,
                chart;
            for (i = 0; i < len; i += 1) {
                $td = $($tds[i]);
                stringdata = $td.data('sparkline');
                stringLabels = $td.data('labels');
                stringlink = $td.data('link');
                arr = stringdata.split('; ');
                datalabels = stringLabels.split(', ');
                data = $.map(arr[0].split(', '), parseFloat);
                data.link = stringlink;
                chart = {};
                if (arr[1]) {
                    chart.type = arr[1];
                }
                chart.containerid = $td.attr('id');
                chart.chartname = '';
                chart.datalabels = false;
                chart.showlegend = false;
                chart.serieslabel = false;
                chart.categorydata = $td.text();
                switch (arr[1]) {
                    case 'pie':
                        chart.data = data;
                        chart.width = '65',
                            chart.height = '65';
                        chart.margin = [0, 0, 0, 0],
                            this.piechart(chart);
                        break;
                    case 'spline':
                    case 'bar':
                    case 'column':
                        chart.data = {
                            data: data,
                            name: $td.data('name'),
                            type: 'bar'
                        };
                        chart.serieslabel = $td.data('serieslabel');
                        chart.title = $td.data('title');
                        this.lbchart(chart);
                        break;
                    case 'progressbar':
                        chart.data = data;
                        chart.title = $td.data('title');
                        chart.width = '100',
                            chart.height = '15';
                        chart.margin = [0, 0, 0, 0];

                        this.ProgressBar(chart);
                        break;
                    default:
                        this.SparkLines();
                        Highcharts.SparkLine($td.attr('id'), {
                            title: {
                                text: ''
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                data: data,
                                pointStart: 1,
                                dataLabels: {
                                    enabled: false
                                }
                            }],
                            tooltip: {
                                headerFormat: '<span style="font-size: 10px">' + $td.parent().find('th').html() + '<br/> Q{point.x}: </span>',
                                pointFormat: ' <b>{point.y}</b>'
                            },
                            chart: chart
                        });
                        break;
                }
                n += 1;
                // If the process takes too much time, run a timeout to allow interaction with the browser
                if (new Date() - time > 500) {
                    $tds.splice(0, i + 1);
                    setTimeout(this.SparkLineReport, 0);
                    break;
                }
            }
        }
        }
    });