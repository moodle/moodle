/**
 * Standard Report wrapper for Moodle. It calls the central JS file for Report plugin,
 * Also it includes JS libraries like Select2,Datatables and Highcharts
 * @module     block_learnerscript/report
 * @class      report
 * @package    block_learnerscript
 * @copyright  2017 Naveen kumar <naveen@eabyas.in>
 * @since      3.3
 */
define(['block_learnerscript/select2',
    'block_learnerscript/responsive.bootstrap',
    'block_learnerscript/reportwidget',
    'block_learnerscript/chart',
    'block_learnerscript/smartfilter',
    'block_learnerscript/helper',
    'block_learnerscript/ajaxforms',
    'block_learnerscript/ajax',
    'jquery',
    'block_learnerscript/radioslider',
    'block_learnerscript/flatpickr',
    'core/templates',
    'jqueryui'
], function(select2, DataTable, reportwidget, chart, smartfilter, helper, AjaxForms, ajax,
    $, RadiosToSlider, flatpickr, templates) {
    var report;
    var BasicparamCourse = $('.basicparamsform #id_filter_courses');
    var BasicparamUser = $('.basicparamsform #id_filter_users');
    var BasicparamActivity = $('.basicparamsform #id_filter_activities');

    var FilterCourse = $('.filterform #id_filter_courses');
    var FilterUser = $('.filterform #id_filter_users');
    var FilterActivity = $('.filterform #id_filter_activities');
    var FilterModule = $('.filterform #id_filter_modules');
    var FilterCohort = $('.filterform #id_filter_cohort');

    var NumberOfBasicParams = 0;

    return report = {
        init: function(args) {
            /**
             * Initialization
             */
            $.ui.dialog.prototype._focusTabbable = $.noop;
            $.fn.dataTable.ext.errMode = 'none';

            $('.plotgraphcontainer').on('click', function() {
                var reportid = $(this).data('reportid');
                // $(this).removeClass('show').addClass('show');
                $('.plotgraphcontainer').removeClass('show').addClass('hide');
                $('#plotreportcontainer' + reportid).html('');

            })
            /**
             * Select2 initialization
             */
            $("select[data-select2='1']").select2({
                theme: "classic"
            }).on("select2:selecting", function(e) {
                if ($(this).val() && $(this).data('maximumSelectionLength') &&
                    $(this).val().length >= $(this).data('maximumSelectionLength')) {
                    e.preventDefault();
                    $(this).select2('close');
                }
            });

            /*
             * Report search
             */
            $("#reportsearch").val(args.reportid).trigger('change.select2');
            $("#reportsearch").change(function() {
                var reportid = $(this).find(":selected").val();
                window.location = M.cfg.wwwroot + '/blocks/learnerscript/viewreport.php?id=' + reportid;
            });
            /**
             * Duration buttons
             */
            RadiosToSlider.init($('#segmented-button'), {
                size: 'medium',
                animation: true,
                reportdashboard: false
            });
            /**
             * Duration Filter
             */
            flatpickr('#customrange', {
                mode: 'range',
                onOpen: function(selectedDates, dateStr,instance){
                    instance.clear();
                },
                onClose: function(selectedDates, dateStr, instance) {
                    if(selectedDates.length !== 0){
                        $('#ls_fstartdate').val(selectedDates[0].getTime() / 1000);
                        $('#ls_fenddate').val((selectedDates[1].getTime() / 1000) + (60 * 60 * 24));
                        require(['block_learnerscript/report'], function(report) {
                            report.CreateReportPage({ reportid: args.reportid, instanceid: args.reportid, reportdashboard: false });
                        });
                    }
                }
            });
            /*
             * Get users for selected course 
             */
            $('#id_filter_cohort').change(function() {
                var cohortid = $(this).find(":selected").val();
                if (cohortid > 0 && (FilterUser.length > 0 || BasicparamUser.length > 0)) {
                    if(BasicparamUser.length > 0){
                        FirstElementActive = true;
                    }
                    smartfilter.CohortUsers({ cohortid: cohortid, reporttype: args.reporttype, reportid: args.reportid, 
                                              firstelementactive: FirstElementActive});
                }
            });

            /*
             * Get Activities and Enrolled users for selected course
             */
            if (typeof BasicparamCourse != 'undefined' || typeof FilterCourse != 'undefined') {
                $('#id_filter_courses').change(function() {
                    args.courseid = $(this).find(":selected").val();
                    smartfilter.CourseData(args);
                });
            }

            /*
             * Get Enrolled courses for selected user
             */
            $('#id_filter_users').change(function() {
                var userid = $(this).find(":selected").val();
                if (userid > 0 && (FilterCourse.length > 0 || BasicparamCourse.length > 0)) {
                    if(BasicparamCourse.length > 0){
                        FirstElementActive = true;
                    }
                    // smartfilter.UserCourses({ userid: userid, reporttype: args.reporttype, reportid: args.reportid, 
                    //                           firstelementactive: FirstElementActive, triggercourseactivities: true });
                }
            });

            $('#id_filter_coursecategories').change(function() {
                var categoryid = $(this).find(":selected").val();
                smartfilter.categoryCourses({ categoryid: categoryid, reporttype: args.reporttype });
            });

            schedule.SelectRoleUsers();

            if (args.basicparams != null) {
                if (args.basicparams[0].name == 'courses') {
                    $("#id_filter_courses").trigger('change');
                    NumberOfBasicParams++;
                }
            }
            if (args.basicparams != null) {
                var FirstElementActive = false;
                if (args.basicparams[0].name == 'users') {
                    if (BasicparamCourse.length > 0) {
                        FirstElementActive = true;
                    }
                    var userid = $("#id_filter_users").find(":selected").val();
                    if (userid > 0) {
                        //args.courseid = $(this).find(":selected").val();
                        args.courseid = $('#id_filter_courses').find(":selected").val();
                        smartfilter.CourseData(args);
                        // smartfilter.UserCourses({ userid: userid, reportid: args.reportid, reporttype: args.reporttype,
                        //                           firstelementactive: FirstElementActive, triggercourseactivities: true });
                    }
                }
            }

            //For forms formatting..can't make unique everywhere, so little trick ;)
            $('.filterform' + args.reportid + ' .fitemtitle').hide();
            $('.filterform' + args.reportid + ' .felement').attr('style', 'margin:0');

            $('.basicparamsform' + args.reportid + ' .fitemtitle').hide();
            $('.basicparamsform' + args.reportid + ' .felement').attr('style', 'margin:0');

            /*
             * Filter form submission
             */
            $(".filterform #id_filter_clear").click(function(e) {
                var NumberOfBasicParams = 0;
                $(".filterform" + args.reportid).trigger("reset");
                var activityelement = $(this).parent().find('#id_filter_activities');
                if (FilterUser.length > 0) {
                    if (FilterCourse.length > 0 || BasicparamCourse.length > 0) {
                        if(BasicparamCourse.length > 0){
                            FirstElementActive = true;
                        }
                        // smartfilter.UserCourses({ userid: 0, reportid: args.reportid, reporttype: args.reporttype, firstelementactive: FirstElementActive });
                    }
                    // $("select[data-select2='1']").select2("destroy").select2({ theme: "classic" });
                }
                if (FilterCourse.length > 0) {
                    if (FilterUser.length > 0 || BasicparamUser.length > 0) {
                        // smartfilter.EnrolledUsers({ courseid: 0, reportid: args.reportid, reporttype: args.reporttype, components: args.components });
                    }

                    if (FilterActivity.length > 0 || BasicparamActivity.length > 0) {
                        smartfilter.CourseActivities({ courseid: 0 });
                    }
                    // $("select[data-select2='1']").select2("destroy").select2({ theme: "classic" });
                }
                if (FilterActivity.length > 0 || FilterModule.length > 0) {
                    if ((FilterCourse.length > 0 || BasicparamCourse.length > 0) && BasicparamUser.length == 0) {
                        if(BasicparamCourse.length > 0 && BasicparamUser.length == 0){
                            FirstElementActive = true;
                        }
                        // smartfilter.UserCourses({ userid: 0, reportid: args.reportid, reporttype: args.reporttype, firstelementactive: FirstElementActive });
                    }
                    if (BasicparamCourse.length > 0 && BasicparamUser.length > 0) {
                            $(".basicparamsform #id_filter_apply").trigger('click', [true]);
                    }
                    // $("select[data-select2='1']").select2("destroy").select2({ theme: "classic" });
                }
                if (FilterCohort.length > 0) {
                    if (FilterUser.length > 0) {
                        smartfilter.CohortUsers({ cohortid: 0});
                    }
                }

                if ($(".basicparamsform #id_filter_apply").length > 0) {
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        // if (settings.url.indexOf("blocks/learnerscript/ajax.php") > 0) {
                        //     if (typeof settings.data != 'undefined') {
                        //         var ajaxaction = $.parseJSON(settings.data);
                        //         if (typeof ajaxaction.basicparam != 'undefined' && ajaxaction.basicparam == true) {
                        //             NumberOfBasicParams++;
                        //         }
                        //     }
                        //     if (args.basicparams.length == NumberOfBasicParams) {
                        //         $(".basicparamsform #id_filter_apply").trigger('click', [true]);
                        //     }
                        // }
                    });
                    $(".basicparamsform #id_filter_apply").trigger('click', [true]);
                } else {
                    args.reporttype = $('.ls-plotgraphs_listitem.ui-tabs-active').data('cid');
                    report.CreateReportPage({ reportid: args.reportid, reporttype: args.reporttype, instanceid: args.reportid });
                }
                $(".filterform select[data-select2='1']").select2("destroy").select2({ theme: "classic" });
                $(".filterform select[data-select2-ajax='1']").val('0').trigger('change');
                $('.filterform')[0].reset();
                $(".filterform #id_filter_clear").attr('disabled', 'disabled');
                $('.plotgraphcontainer').removeClass('show').addClass('hide');
                $('#plotreportcontainer' + args.instanceid).html('');
            });

            /*
             * Basic parameters form submission
             */
            $(".basicparamsform #id_filter_apply,.filterform #id_filter_apply").click(function(e, validate) {
                var getreport = helper.validatebasicform(validate);
                e.preventDefault();
                e.stopImmediatePropagation();
                $(".filterform" + args.reportid).show();
                args.instanceid = args.reportid;
                if(e.currentTarget.value != 'Get Report'){
                    $(".filterform #id_filter_clear").removeAttr('disabled');
                }
                if ($.inArray(0, getreport) != -1) {
                    $("#report_plottabs").hide();
                    $("#reportcontainer" + args.reportid).html("<div class='alert alert-info'>No data available</div>");
                } else {
                    $("#report_plottabs").show();
                    args.reporttype = $('.ls-plotgraphs_listitem.ui-tabs-active').data('cid');
                    report.CreateReportPage({ reportid: args.reportid, reporttype: args.reporttype, instanceid: args.instanceid, reportdashboard: false });
                }
                $('.plotgraphcontainer').removeClass('show').addClass('hide');
                $('#plotreportcontainer' + args.instanceid).html('');
            });
            /*
             * Generate Plotgraph
             */
            if (args.basicparams == null) {
                report.CreateReportPage({ reportid: args.reportid, reporttype: args.reporttype, instanceid: args.reportid, reportdashboard: false });
            } else {
                if (args.basicparams.length <= 4) {
                    $(".basicparamsform #id_filter_apply").trigger('click', [true]);
                } else {
                        $(document).ajaxComplete(function(event, xhr, settings) {
                            if (settings.url.indexOf("blocks/learnerscript/ajax.php") > 0) {
                                if (typeof settings.data != 'undefined') {
                                    var ajaxaction = $.parseJSON(settings.data);
                                    if (typeof ajaxaction.basicparam != 'undefined' && ajaxaction.basicparam == true) {
                                        NumberOfBasicParams++;
                                    }
                                }
                                if (args.basicparams.length == NumberOfBasicParams
                                    && ajaxaction.action != 'plotforms' && ajaxaction.action != 'pluginlicence') {
                                    $(".basicparamsform #id_filter_apply").trigger('click', [true]);
                                }
                            }
                        });
                }
            }

            /*
             * Make sure will have vertical tabs for plotoptions for report
             */
            // $tabs = $('#report_plottabs').tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
            // $("#report_plottabs li").removeClass("ui-corner-top").addClass("ui-corner-left");

            // helper.tabsdraggable($tabs);

        },
        CreateReportPage: function(args) {
            var disabletable = 0;
            if (args.reportdashboard == false) {
                var disabletable = $('#disabletable').val();
                if (disabletable) {
                    args.reporttype = $($('.ls-plotgraphs_listitem')[0]).data('cid');
                }
            }
            if (disabletable == 1 && args.reporttype.length > 0) {
                chart.HighchartsAjax({
                    'reportid': args.reportid,
                    'action': 'generate_plotgraph',
                    'cols': args.cols,
                    'reporttype': args.reporttype
                });
            } else if (disabletable == 0) {
                reportwidget.CreateDashboardwidget({
                    reportid: args.reportid,
                    reporttype: 'table',
                    instanceid: args.instanceid,
                    reportdashboard: args.reportdashboard
                });
            } else {

            }
        },
        /**
         * Generates graph widget with given Highcharts ajax response
         * @param  object response Ajax response
         * @return Creates highchart widget with given response based on type of chart
         */
        generate_plotgraph: function(response) {
            var returned;
            response.containerid = 'plotreportcontainer' + response.reportinstance;
            switch (response.type) {
                case 'pie':
                    chart.piechart(response);
                    break;
                case 'spline':
                case 'bar':
                case 'column':
                    chart.lbchart(response);
                    break;
                case 'solidgauge':
                    chart.solidgauge(response);
                    break;
                case 'combination':
                    chart.combinationchart(response);
                    break;
                case 'map':
                    chart.WorldMap(response);
                    break;
                case 'treemap':
                    chart.TreeMap(response);
                    break;
            }
        },
        /**
         * Datatable serverside for all table type reports
         * @param object args reportid
         * @return Apply serverside datatable to report table
         */
        ReportDatatable: function(args) {
            var self = this;
            var params = {};
            var reportinstance = args.instanceid ? args.instanceid : args.reportid;
            params['filters'] = args.filters;
            params['basicparams'] = args.basicparams || JSON.stringify(smartfilter.BasicparamsData(reportinstance));
            params['reportid'] = args.reportid;
            params['columns'] = args.columns;
            //
            // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
            //
            $.fn.dataTable.pipeline = function(opts) {
                // Configuration options
                var conf = $.extend({
                    url: '', // script url
                    data: null, // function or object with parameters to send to the server
                    method: 'POST' // Ajax HTTP method
                }, opts);

                return function(request, drawCallback, settings) {
                    var ajax = true;
                    var requestStart = request.start;
                    var drawStart = request.start;
                    var requestLength = request.length;
                    var requestEnd = requestStart + requestLength;

                    if (typeof args.data != 'undefined' && request.draw == 1) {
                        json = args.data;
                        json.draw = request.draw; // Update the echo for each response
                        json.data.splice(0, requestStart);
                        json.data.splice(requestLength, json.data.length);
                        drawCallback(json);
                    } else if (ajax) {
                        // Need data from the server
                        request.start = requestStart;
                        request.length = requestLength;
                        $.extend(request, conf.data);

                        settings.jqXHR = $.ajax({
                            "type": conf.method,
                            "url": conf.url,
                            "data": request,
                            "dataType": "json",
                            "cache": false,
                            "success": function(json) {
                                drawCallback(json);
                            }
                        });
                    } else {
                        json = $.extend(true, {}, cacheLastJson);
                        json.draw = request.draw; // Update the echo for each response
                        json.data.splice(0, requestStart - cacheLower);
                        json.data.splice(requestLength, json.data.length);
                        drawCallback(json);
                    }
                }
            };
            if (args.reportname == 'Users profile' || args.reportname == 'Course profile') {
                var lengthoptions = [
                    [50, 100, -1],
                    ["Show 50", "Show 100", "Show All"]
                ];
            } else {
                var lengthoptions = [
                    [10, 25, 50, 100, -1],
                    ["Show 10", "Show 25", "Show 50", "Show 100", "Show All"]
                ];
            }
            var oTable = $('#reporttable_' + reportinstance).DataTable({
                'processing': true,
                'serverSide': true,
                'destroy': true,
                'dom': '<"co_report_header"Bf <"report_header_skew"  <"report_header_skew_content" Bl<"report_header_showhide" ><"report_calculation_showhide" >> > > tr <"co_report_footer"ip>',
                'ajax': $.fn.dataTable.pipeline({
                    "type": "POST",
                    "url": M.cfg.wwwroot + '/blocks/learnerscript/components/datatable/server_processing.php?sesskey=' + M.cfg.sesskey,
                    "data": params
                }),
                'columnDefs': args.columnDefs,
                "fnDrawCallback": function(oSettings, json) {
                    chart.SparkLineReport();
                    helper.DrilldownReport();
                },
                "oScroll": {},
                'responsive': true,
                "fnInitComplete": function() {
                    this.fnAdjustColumnSizing(true);
                    // $(".drilldown" + reportinstance + " .ui-dialog-title").html(args.reportname);

                    if (args.reportname == 'Users profile' || args.reportname == 'Course profile') {
                        $("#reporttable_" + reportinstance + "_wrapper .co_report_header").remove();
                        $("#reporttable_" + reportinstance + "_wrapper .co_report_footer").remove();
                    }

                    $('.download_menu' + reportinstance + ' li a').each(function(index) {
                        var link = $(this).attr('href');
                        if (typeof args.basicparams != 'undefined') {
                            var basicparamsdata = JSON.parse(args.basicparams);
                            $.each(basicparamsdata, function(key, value) {
                                if (key.indexOf('filter_') == 0) {
                                    link += '&' + key + '=' + value;
                                }
                            });
                        }
                        if (typeof(args.filters) != 'undefined') {
                            var filters = JSON.parse(args.filters);
                            $.each(filters, function(key, value) {
                                if (key.indexOf('filter_') == 0) {
                                    link += '&' + key + '=' + value;
                                }
                                if(key.indexOf('ls_') == 0) {
                                    link += '&' + key + '=' + value;
                                }
                            });
                        }
                        $(this).attr('href', link);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    $(nRow).children().each(function(index, td) {
                        $(td).css("word-break", args.columnDefs[index].wrap);
                        $(td).css("width", args.columnDefs[index].width);
                    });
                    return nRow;
                },
                "autoWidth": false,
                'aaSorting': [],
                'language': {
                    'paginate': {
                        'previous': '<',
                        'next': '>'
                    },
                    'sProcessing': "<img src='" + M.util.image_url('loading', 'block_learnerscript') + "'>",
                    'search': "_INPUT_",
                    'searchPlaceholder': "Search",
                    'lengthMenu': "_MENU_",
                    "emptyTable": "<div class='alert alert-info'>No data available</div>"
                },
                "lengthMenu": lengthoptions
            });
            $(".drilldown" + reportinstance + " .ui-dialog-title").html(args.reportname);
            $("#page-blocks-learnerscript-viewreport #reporttable_" + args.reportid + "_wrapper div.report_header_showhide").
            html($('#export_options' + args.reportid).html());
            if ($('.reportcalculation' + args.reportid).length > 0) {
                $("#page-blocks-learnerscript-viewreport #reporttable_" + args.reportid + "_wrapper div.report_calculation_showhide").
                html('<img src="' + M.util.image_url('calculationicon', 'block_learnerscript') + '" onclick="(function(e){ require(\'block_learnerscript/helper\').reportCalculations({reportid:' + args.reportid + '}) })(event)" title ="Calculations" />');
            }
            // $('#export_options' + args.reportid).remove();
        },
        AddExpressions: function(e, value) {
            $(e.target).on('select2:unselecting', function(e){
                $('#fitem_id_'+e.params.args.data.id+'').remove();
            });
            var columns = $(e.target).val();
            $.each(columns, function(index){
                if($('#fitem_id_'+columns[index]).length > 0){
                    return;
                }
                var column = [];
                 column['name'] = columns[index];
                 column.conditionsymbols = [];
                 var conditions = ["=", ">", "<", ">=", "<=", "<>"];
                 $.each(conditions, function(index, value){
                    column.conditionsymbols.push({
                        'value': value
                    })
                 });
                var requestdata = { column: column };
                templates.render('block_learnerscript/plotconditions', requestdata).then(function(html){
                    //$(e.target).closest('form').find('#fitem_id_yaxis_bar').after(html);
                    //$(e.target).closest('form').find('#fitem_id_yaxis').after(html);
                    if(value == 'yaxisbarvalue') {
                        $('#yaxis_bar1').append(html);
                    } else {
                        $('#yaxis1').append(html);
                    }

                }).fail(function(ex){});
            });
        },
        block_statistics_help: function(reportid){
                var promise = ajax.call({
                    args: {
                        action: 'learnerscriptdata',
                        reportid: reportid
                    },
                    url: M.cfg.wwwroot + "/blocks/learnerscript/ajax.php",
                });
                promise.done(function(response) {
                   require(['core/modal_factory'], function(ModalFactory) {
                        ModalFactory.create({
                            title: response.name,
                            body: response.summary,
                            footer: '',
                        }).done(function(modal) {
                            dialogue = modal;
                            ModalEvents = require('core/modal_events');
                            dialogue.getRoot().on(ModalEvents.hidden, function() {
                                //window.location = M.cfg.wwwroot + '/blocks/learnerscript/viewreport.php?id=' + 10;
                            });

                            dialogue.show();
                        });
                    });
                });
        }
       
    };

});
