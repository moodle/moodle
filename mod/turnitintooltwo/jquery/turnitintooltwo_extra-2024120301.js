jQuery(document).ready(function($) {
    // Add style to header row.
    $('.mod_turnitintooltwo_submissions_data_table thead tr, #mod_turnitintooltwo_course_browser_table thead tr').toggleClass("header");

    // Configure datatables language settings.
    var dataTablesLang = {
        "nointegration": M.str.turnitintooltwo.nointegration,
        "sProcessing": M.str.turnitintooltwo.sprocessing,
        "sZeroRecords": M.str.turnitintooltwo.szerorecords,
        "sInfo": M.str.turnitintooltwo.sinfo,
        "sSearch": M.str.turnitintooltwo.ssearch,
        "sLengthMenu": M.str.turnitintooltwo.slengthmenu,
        "sInfoEmpty": M.str.turnitintooltwo.semptytable,
        "oPaginate": {
            "sNext": M.str.turnitintooltwo.snext,
            "sPrevious": M.str.turnitintooltwo.sprevious
        }
    };

    // Configure datatables language settings for migration tool.
    var dataTablesLangMigration = {
        "nointegration": M.str.turnitintooltwo.nointegration,
        "sProcessing": M.str.turnitintooltwo.sprocessing,
        "sZeroRecords": M.str.turnitintooltwo.szerorecords,
        "sInfo": M.str.turnitintooltwo.sinfo,
        "sSearch": '',
        "sLengthMenu": M.str.turnitintooltwo.slengthmigrationmenu,
        "sInfoEmpty": M.str.turnitintooltwo.semptytable,
        "oPaginate": {
            "sNext": M.str.turnitintooltwo.snext,
            "sPrevious": M.str.turnitintooltwo.sprevious
        }
    };

    // Configure the unlink and relink users datatable in the plugin settings area.
    $('#unlinkUserTable').dataTable({
        "bDestroy": true,
        "bProcessing": true,
        "bServerSide": false,
        "oLanguage": dataTablesLang,
        "aaSorting": [[ 2, "asc" ]],
        "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "sAjaxSource": "ajax.php?action=get_users",
        "aoColumns": [
                        {"bSortable": false,
                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).addClass('centered_cell');
                            }},
                        null,
                        {"aDataSort": [ 2, 3 ]},
                        {"aDataSort": [ 3, 2 ]},
                        null
                     ],
        "fnDrawCallback": function () {
            $('input[name="selectallcb"]').attr('checked', false);
        }
    });

    // Disable the submit button if Turnitin v1 and v2 account ids are different in Migration Tool.
    if ( $('#sametiiaccount').data('sametiiaccount') == "0" ) {
        $('select[name="enablemigrationtool"]').attr('disabled', 'disabled');
        $('select[name="enablemigrationtool"]').closest('form').find('input[name="submitbutton"]').attr('disabled', 'disabled');
    }

    // Disable the delete button in migration tab if there are no results selected and re-enable if there are.
    $('input[name="selectallcb"]').closest('form').find('input[name="submitbutton"]').attr('disabled', 'disabled');
    $(document).on('click', '#migrationTable input[name="selectallcb"], #migrationTable .browser_checkbox', function() {
        if ($('#migrationTable .browser_checkbox:checked').length > 0) {
            $('#migrationTable .browser_checkbox').closest('form').find('input[name="submitbutton"]').removeAttr('disabled');
        } else {
            $('#migrationTable .browser_checkbox').closest('form').find('input[name="submitbutton"]').attr('disabled', 'disabled');
        }
    });

    // Ask administrator for confirmation if user clicks to delete selected V1 assignments.
    var submitbutton = $('#migrationTable').closest('form').find('input[name="submitbutton"]');
    submitbutton.click(function(ev) {
        ev.preventDefault();

        // Construct confirm message to administrator.
        var message = M.str.turnitintooltwo.confirmv1deletetitle+'\n\n';
        message += M.util.get_string('confirmv1deletetext', 'turnitintooltwo', $('#migrationTable .browser_checkbox:checked').length)+'\n\n';
        message += M.str.turnitintooltwo.confirmv1deletewarning;

        if (confirm(message)) {
            $('#migrationTable').closest('form').submit();
        }

    });

    // Configure the migration datatable in the plugin settings area.
    $('#migrationTable').dataTable({
        "bDestroy": true,
        "bProcessing": true,
        "bServerSide": false,
        "oLanguage": dataTablesLangMigration,
        "aaSorting": [[ 2, "asc" ]],
        "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "sAjaxSource": "ajax.php?action=get_migration_assignments",
        "sDom": '<"top"lf>rt<"bottom"irp><"clear">',
        "aoColumns": [
                        {"bSortable": false, "bSearchable": false,
                            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).addClass('centered_cell');
                            }},
                        {"bSortable": true, "sClass": "centered_cell", "bSearchable": false},
                        {"bSortable": true, "bSearchable": true},
                        {"bSortable": true, "sClass": "centered_cell", "bSearchable": false}
                     ],
        "fnDrawCallback": function() {
            $('input[name="selectallcb"]').attr('checked', false);
        }
    });
    $('#migrationTable_filter input').attr("placeholder", 'Search');

    // Configure the files datatable in the plugin settings area, group the files by assignment.
    $('#filesTable').dataTable( {
        "bDestroy": true,
        "bProcessing": true,
        "bServerSide": false,
        "oLanguage": dataTablesLang,
        "sAjaxSource": "ajax.php?action=get_files",
        "aoColumns": [
                    null,
                    null,
                    null,
                    {"sClass": "filename c0", "sWidth": "40%"},
                    null,
                    {"sClass": "fullname c1", "sWidth": "35%"},
                    null,
                    {"sClass": "created c2", "sWidth": "22%"},
                    {"sClass": "remove c3", "sWidth": "3%"}
                ],
        "aoColumnDefs": [
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 0 ]},
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 1 ]},
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 2 ]},
                    {"bSearchable": true, "bVisible": true, "aTargets": [ 3 ]},
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 4 ]},
                    {"bSearchable": true, "bVisible": true, "aTargets": [ 5 ]},
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 6 ]},
                    {"bSearchable": true, "bVisible": true, "aTargets": [ 7 ]},
                    {"bSearchable": true, "bVisible": true, "aTargets": [ 8 ]}
                ],
        "fnDrawCallback": function ( oSettings ) {
            if ( oSettings.aiDisplay.length == 0 )
            {
                return;
            }

            var nTrs = $('#filesTable tbody tr');
            var iColspan = nTrs[0].getElementsByTagName('td').length;
            var sLastGroup = "";
            for (var i = 0; i < nTrs.length; i++) {
                var iDisplayIndex = oSettings._iDisplayStart + i;
                var sGroup = oSettings.aoData[ oSettings.aiDisplay[i] ]._aData[0];
                if ( sGroup != sLastGroup )
                {
                    var nGroup = document.createElement( 'tr' );
                    var nCell = document.createElement( 'td' );
                    nCell.colSpan = iColspan;
                    nCell.className = "group";
                    nCell.innerHTML = sGroup;
                    nGroup.appendChild( nCell );
                    nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
                    sLastGroup = sGroup;
                }
            }
        },
        "aaSortingFixed": [[ 0, 'asc' ]],
        "aaSorting": [[ 1, 'asc' ]],
        "sDom": 'lfr<"giveHeight"t>ip'
    });

    $.datepicker.regional[""].dateFormat = 'd M yy';
    $.datepicker.setDefaults($.datepicker.regional['']);

    // Configure the course browser data table and show classes from Turnitin acocunt.
    var courseBrowserTable = $('#mod_turnitintooltwo_course_browser_table').dataTable({
        "bProcessing": true,
        "oLanguage": dataTablesLang,
        "aaSorting": [[ 6, 'asc']],
        "sAjaxSource": "ajax.php",
        "aoColumnDefs": [
                    {"bSearchable": false, "bSortable": false, "sWidth": "5%", "bVisible": true, "aTargets": [ 0 ],
                        "fnCreatedCell": function (nTd) {
                            $(nTd).addClass('center');
                        }},
                    {"bSearchable": true, "bVisible": true, "sWidth": "45%", "iDataSort": 6, "aTargets": [ 1 ]},
                    {"bSearchable": true, "bVisible": true, "sWidth": "10%", "aTargets": [ 2 ]},
                    {"bSearchable": true, "bVisible": true, "sWidth": "20%", "iDataSort": 7, "aTargets": [ 3 ],
                        "fnCreatedCell": function (nTd) {
                            $(nTd).addClass('right');
                        }},
                    {"bSearchable": false, "bVisible": true, "sWidth": "10%", "aTargets": [ 4 ],
                        "fnCreatedCell": function (nTd) {
                            $(nTd).addClass('right');
                        }},
                    {"bSearchable": true, "bVisible": true, "sWidth": "10%", "aTargets": [ 5 ],
                        "fnCreatedCell": function (nTd) {
                            $(nTd).addClass('center');
                        }},
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 6 ]},
                    {"bSearchable": true, "bVisible": false, "aTargets": [ 7 ]}
                ],
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": {action: "search_classes", course_title: $('#search_course_title').val(),
                    course_integration: $('#search_course_integration').val(),
                    course_end_date: $('#search_course_end_date').val(), sesskey: M.cfg.sesskey},
                "success": function(result) {
                    fnCallback(result);
                }
            });
        },
        "bStateSave": true,
        "fnStateSave": function (oSettings, oData) {
            try {
                localStorage.setItem( uid + 'DataTables', JSON.stringify(oData) );
            } catch ( e ) {
            }
        },
        "fnStateLoad": function (oSettings) {
            try {
                return JSON.parse( localStorage.getItem(uid + 'DataTables') );
            } catch ( e ) {
            }
        },
        "fnDrawCallback": function () {
            $('input[name="selectallcb"]').attr('checked', false);
            initialiseCourseRecreation();
            initialiseEditEndDate();
            $("#search_courses_button").removeAttr('disabled');
        }
    });

    $("#search_courses_button").click(function () {
        $(this).attr('disabled', 'disabled');
        courseBrowserTable.fnReloadAjax();
        courseBrowserTable.fnStandingRedraw();
        return false;
    });

    $('#search_course_end_date').datepicker();

    // Initialise assignment browser table.
    var oTable = $('#assignmentBrowserTable').dataTable({
        "bProcessing": true,
        "oLanguage": dataTablesLang,
        "sAjaxSource": "ajax.php",
        "fnServerData": function ( sSource, aoData, fnCallback ) {

            // Disable course buttons.
            $('#id_create_course').attr('disabled','disabled');
            $('#id_update_course').attr('disabled','disabled');

            // Move box within form.
            if ($("#assignmentBrowserTable").length > 0) {
                $(".side-pre-only #page-content #region-main").css({'margin-left' : '0px'});
            }
            $("#id_assignmentname").attr('disabled', 'disabled');
            $("#id_create_assignment").attr('disabled', 'disabled');
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": {action: "get_assignments", tii_course_id: $("#tii_course_id").html(), sesskey: M.cfg.sesskey},
                "success": function(result) {
                    eval(result);
                    initialiseCourseBrowserButtons(oTable);
                    fnCallback(result);

                    if ($("#course_id").html() != "0" && result.number_of_assignments > 0) {
                        $("#id_assignmentname").removeAttr('disabled');
                        $("#id_create_assignment").removeAttr('disabled');
                        $(".assignmentids_check").removeAttr('disabled');
                        initialiseCreateAssignmentButton(oTable);
                    } else {
                        $("#id_assignmentname").attr('disabled', 'disabled');
                        $("#id_create_assignment").attr('disabled', 'disabled');
                        $(".assignmentids_check").attr('disabled', 'disabled');
                    }

                    // Enable course buttons.
                    $('#id_create_course').removeAttr('disabled');
                    $('#id_update_course').removeAttr('disabled');
                }
            });
        },
        "aoColumnDefs": [
            {"bSearchable": false, "bVisible": true, "aTargets": [ 0 ]},
            {"bSearchable": true, "bVisible": true, "aTargets": [ 1 ],
                "fnCreatedCell": function (nTd) {
                    $(nTd).addClass('nowrap');
                }},
            {"bSearchable": true, "bVisible": true, "aTargets": [ 2 ]},
            {"bSearchable": true, "bVisible": true, "aTargets": [ 3 ]}
        ]
    });

    // Open an iframe light box which allows the creation of classes.
    $('#create_classes_button').colorbox({
        iframe:true, width:'60%', top: '200px', height:'124px', opacity: "0.7", className: "course_creation",
        href: function() {
                var category = $('.create_course_category').val();
                var assignments = ($('.create_assignment_checkbox').is(':checked')) ? "1" : "0";

                var class_ids = "";
                var i = 0;
                $('.browser_checkbox:checked').each(function(i){
                    class_ids += "&class_id" + i + "=" + $(this).val();
                    i++;
                });

                var pageurl = window.location.href;
                var url = pageurl.replace("cmd=courses", "cmd=multiple_class_recreation");

                return url + "&view_context=box&category=" + category + "&assignments=" + assignments + class_ids + "&sesskey=" + M.cfg.sesskey;
        },
        onCleanup: function() {
            window.location = window.location;
        }
    });

    if ($('#class_ids').length > 0) {

        // Move box within frame.
        $(".side-pre-only #page-content #region-main").css({
            'margin-left' : '0px'
        });

        $(".has-region-side-pre #page-content #region-main").css({
            'width': '100%',
            'margin-top': '-30px'
        });

        var class_ids = $('#class_ids').html();

        $.ajax({
            "dataType": 'html',
            "type": "POST",
            "url": "ajax.php",
            "data": {action: "create_courses", class_ids: class_ids, course_category: $("#course_category").html(),
                create_assignments: $("#create_assignments").html(), sesskey: M.cfg.sesskey},
            success: function(data) {
                $('#course_creation_status').html(data);
            }
        });
    }

    // Show light box to change the end date of a course.
    function initialiseEditEndDate() {
        $("a.edit_course_end_link").colorbox({
            inline:true, width:"60%", top: "100px", background: "#fff", height:"315px", opacity: "0.7", className: "edit_end_date_form",
            onLoad: function() {
                lightBoxCloseButton();
            },
            onComplete : function() {

                // Get current date from the span id within the link and set
                // the date of the datepicker in the lightbox to that.
                var current_date = $('#' + $(this).attr("id") + ' span').attr("id");
                current_date = current_date.split("_");
                $('#id_new_course_end_date_day').val(current_date[1]);
                $('#id_new_course_end_date_month').val(current_date[2]);
                $('#id_new_course_end_date_year').val(current_date[3]);

                var idStr = $(this).attr("id").split("_");
                var tii_course_id = idStr[2];
                $('input[name="tii_course_id"]').val(tii_course_id);
                $('input[name="tii_course_title"]').val($('a#course_' + tii_course_id).html());
                $('#cboxLoadedContent .mod_turnitintooltwo_edit_course_end_date_form').show();
                $('#dateselector-calendar-panel').css('z-index', '9999');

                $('#id_save_end_date').click(function() {
                    $.ajax({
                        "dataType": 'json',
                        "type": "POST",
                        "url": "ajax.php",
                        "data": {action: "edit_course_end_date", tii_course_id: tii_course_id,
                            tii_course_title: $('a#course_' + tii_course_id).html(), sesskey: M.cfg.sesskey,
                            end_date_d: $('#id_new_course_end_date_day').val(),
                            end_date_m: $('#id_new_course_end_date_month').val(),
                            end_date_y: $('#id_new_course_end_date_year').val()
                        },
                        success: function(data) {
                            eval(data);
                            if (data.status == "success") {
                                parent.$.fn.colorbox.close();
                                $('#course_date_' + tii_course_id + ' span').html(data.end_date);
                            } else {
                                var current_msg = $('#edit_end_date_desc').html;
                                $('#edit_end_date_desc').html(current_msg + " " + data.msg);
                            }
                        }
                    });
                });
            },
            onCleanup: function() {
                $('.mod_turnitintooltwo_edit_course_end_date_form').hide();
                $('#tii_close_bar').remove();
            }
        });
    }

    function lightBoxCloseButton() {
        $('body').append('<div id="tii_close_bar"><a href="#" onclick="$.colorbox.close(); return false;">' + M.str.turnitintooltwo.closebutton + '</a></div>');
    }

    // Show light box with a form to either create a new course or link an unlinked Moodle course
    // to the clicked Turnitin class, then subsequently create a Moodle assignment using the selected
    // existing assignments on Turnitin as parts.
    function initialiseCourseRecreation() {

        var windowWidth = $(window).width();
        var colorBoxWidth = "80%";
        if (windowWidth < 1000) {
            colorBoxWidth = "860px";
        }

        var windowHeight = $(window).width();
        var colorBoxHeight = "80%";
        if (windowHeight < 700) {
            colorBoxHeight = "600px";
        }

        $("a.course_recreate").colorbox({
            iframe:true, width:colorBoxWidth, height:colorBoxHeight, top: '100px', className: "migration", opacity: "0.7",
            onLoad: function() {
                lightBoxCloseButton();
            },
            onCleanup:function() {
                $('#tii_close_bar').remove();
            }
        });

        $('.browser_checkbox').click(function() {
            if ($('.browser_checkbox:checked').length > 0) {
                $('.create_checkboxes').slideDown();
            } else {
                $('.create_checkboxes').slideUp();
            }
        });
    }

    // Make the buttons on the course creation/link forms clickable and configure the relevant triggered event.
    function initialiseCourseBrowserButtons(oTable) {
        $('#id_create_course').click(function() {
            $.ajax({
                "dataType": 'html',
                "type": "POST",
                "url": "ajax.php",
                "data": {action: "create_course", tii_course_id: $("#tii_course_id").html(),
                    tii_course_name: encodeURIComponent($("#tii_course_name").html()),
                    course_name: encodeURIComponent($("#id_coursename").val()),
                    course_category: $("#id_coursecategory").val(), sesskey: M.cfg.sesskey},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    hideCourseCreationOptions(obj, oTable);
                }
            });
        });

        $('#id_update_course').click(function() {
            $.ajax({
                "dataType": 'html',
                "type": "POST",
                "url": "ajax.php",
                "data": {action: "link_course", tii_course_id: $("#tii_course_id").html(),
                    tii_course_name: $("#tii_course_name").html(), course_to_link: $("#id_coursetolink").val(),
                    sesskey: M.cfg.sesskey},
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    hideCourseCreationOptions(obj, oTable);
                }
            });
        });
    }

    // Hide the course creation/linking form once the selected Turnitin
    // course has been created/linked locally and initialise the create assignment button.
    function hideCourseCreationOptions(obj, oTable) {
        if (obj != 0) {
            $("#course_id").html(obj.courseid);

            var cb_element = 'input[name="check_' + $('#tii_course_id').html() + '"]';
            parent.$(cb_element).hide();
            var tick_element = '#tick_' + $('#tii_course_id').html();
            parent.$(tick_element).show();

            $('fieldset[id$="create_course_fieldset"]').parent().slideToggle();
            $('fieldset[id$="update_course_fieldset"]').parent().slideToggle();

            $("#or_container").hide();

            $('#existing_course_title_span').html(obj.coursename);
            $('.existing_course_title_h3').removeClass('hidden_class');

            if ($('.assignmentids_check').length > 0) {
                $(".assignmentids_check").removeAttr('disabled');
                $("#id_assignmentname").removeAttr('disabled');
                $("#id_create_assignment").removeAttr('disabled');
            }

            $('.assignmentids_check').change(function(){
                if ($('.assignmentids_check').filter(':checked').length >= 5) {
                    $(".assignmentids_check").not(':checked').attr('disabled', 'disabled');
                } else {
                    $(".assignmentids_check").removeAttr('disabled');
                }
            });

            initialiseCreateAssignmentButton(oTable);
        }
    }

    // Bind the event to create an assignment from the selected parts.
    function initialiseCreateAssignmentButton(oTable) {
        $('#id_create_assignment').unbind("click");
        $('#id_create_assignment').click(function() {

            var parts = "";
            $('.assignmentids_check:checked').each(function(i){
                parts += $(this).val() + ",";
            });
            if (parts.charAt(parts.length - 1) == ',') {
                parts.substring(0, parts.length - 1);
            }

            $("#id_create_assignment").attr('disabled', 'disabled');

            $.ajax({
                "dataType": 'html',
                "type": "POST",
                "url": "ajax.php",
                "data": {action: "create_assignment", course_id: $("#course_id").html(),
                    assignment_name: $("#id_assignmentname").val(), parts: parts, sesskey: M.cfg.sesskey},
                success: function(data) {
                    oTable.fnReloadAjax();
                    oTable.fnStandingRedraw();
                    $("#id_assignmentname").removeAttr('disabled');
                    $("#id_create_assignment").removeAttr('disabled');
                    $(".assignmentids_check").removeAttr('disabled');
                }
            });
        });
    }
});