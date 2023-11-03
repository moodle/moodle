/**
 * Standard Report wrapper for Moodle. It calls the central JS file for Report plugin,
 * Also it includes JS libraries like Select2,Datatables and Highcharts
 * @module     block_learnerscript/report
 * @class      report
 * @package    block_learnerscript
 * @copyright  2017 Naveen kumar <naveen@eabyas.in>
 * @since      3.3
 */
define(['block_learnerscript/ajax', 'jquery', 'block_learnerscript/report', 'block_reportdashboard/radioslider','block_reportdashboard/flatpickr'],
    function(ajax, $, report, RadiosToSlider, flatpickr) {
        var coursels;
        return coursels = {
            init: function() {
                this.BootSideMenu({refreshdashboard:1, side:'right', title:'Course Statistics', width:'30%'});
            },
            CourseInsights: function(){
                // $('#internalbsm1').append($("#reportloading").text());
                    var dlg = $("#internalbsm1").dialog({
                        resizable: true,
                        autoOpen: false,
                        width: '50%',
                        title: '',
                        modal: true,
                        show: {
                            effect: "slide",
                            duration: 1000
                        },
                        position: {
                            my: "left",
                            at: "top",
                            of: "body"
                        },
                        create: function( event, ui ) {
                            $('.ui-dialog').css('z-index',9999);
                        },
                        open: function(event){

                            RadiosToSlider.init($('#segmented-button'), {
                                size: 'medium',
                                animation: true,
                                // onSelect: report.DurationFilter(this)
                            });
                            flatpickr('#customrange',{
                                mode: 'range',
                                onChange: function(selectedDates, dateStr, instance) {
                                    $('#ls_fstartdate').val(selectedDates[0].getTime() / 1000);
                                    $('#ls_fenddate').val(selectedDates[1].getTime() / 1000);
                                    require(['block_learnerscript/report'], function(reportjs) {
                                        reportjs.DashboardTiles();
                                        reportjs.DashboardWidgets();
                                    });
                                }
                            });
                            require(['block_learnerscript/report'], function(reportjs) {
                                reportjs.DashboardTiles();
                                reportjs.DashboardWidgets();
                            });
                            $(document).ajaxStop(function() {
                                $(".loader").fadeOut("slow");
                            });
                        },
                        close: function(event, ui) {
                            $(this).dialog('destroy');
                            $(".report_dashboard_container").html('');
                            $(".plotreport_dashboard_container").html('');
                        }
                    });
                    $( document ).ajaxComplete(function() {
                      $("#reportloading").hide();
                    });
                    dlg.dialog("open");
                },
            BootSideMenu: function(userOptions) {
                var placeholder = $('#internalbsm');
                var initialCode;
                var newCode;
                var menu;
                var prevStatus;
                var body = {};

                var defaults = {
                    side: "left",
                    duration: 500,
                    autoClose: true,
                    pushBody: false,
                    closeOnClick: true,
                    remember: false,
                    width: userOptions.width,
                    onTogglerClick: function() {
                        //code to be executed when the toggler arrow was clicked
                    },
                    onBeforeOpen: function() {
                        //code to be executed before menu open
                    },
                    onBeforeClose: function() {
                        //code to be executed before menu close
                    },
                    onOpen: function() {
                        //code to be executed after menu open
                        if(userOptions.refreshdashboard){
                            report.DashboardTiles();
                            report.DashboardWidgets();
                        }
                    },
                    onClose: function() {
                        //code to be executed after menu close
                    },
                    onStartup: function() {
                        //code to be executed when the plugin is called
                    }
                };

                var options = $.extend({}, defaults, userOptions);


                body.originalMarginLeft = $("body").css("margin-left");
                body.originalMarginRight = $("body").css("margin-right");
                body.width = $("body").width();

                initialCode = placeholder.html();

                newCode = " <div class=\"menu-wrapper\">\n" + initialCode + " </div>";
                newCode += "<div class=\"toggler\" data-whois=\"toggler\">";
                newCode += " "+ options.title + "";
                newCode += "</div>";

                placeholder.empty();
                placeholder.append(newCode);

                menu = $(placeholder);

                menu.addClass("container");
                menu.addClass("bootsidemenu");
                menu.css("width", options.width);

                if (options.side == "left") {
                    menu.addClass("bootsidemenu-left");
                } else if (options.side == "right") {
                    menu.addClass("bootsidemenu-right");
                }

                menu.id = menu.attr("id");
                menu.cookieName = "bsm2-" + menu.id;
                menu.toggler = $(menu.children()[1]);
                menu.originalPushBody = options.pushBody;
                menu.originalCloseOnClick = options.closeOnClick;


                if (options.remember) {
                    prevStatus = this.readCookie(menu.cookieName);
                } else {
                    prevStatus = null;
                }


                this.forSmallBody(options, menu);

                switch (prevStatus) {
                    case "opened":
                        this.startOpened(options, menu);
                        break;
                    case "closed":
                        this.startClosed(options, menu);
                        break;
                    default:
                        this.startDefault(options, menu);
                        break;
                }

                if (options.onStartup !== undefined) {
                    options.onStartup(menu);
                }

                $("[data-toggle=\"collapse\"]", menu).each(function() {
                    var icona = $("<span class=\"glyphicon glyphicon-chevron-right\"></span>");
                    $(placeholder).prepend(icona);
                });

                menu.off("click", "[data-whois=toggler]");
                menu.on("click", "[data-whois=toggler]", function() {
                    coursels.toggle(options, menu, body);
                    if (options.onTogglerClick !== undefined) {
                        options.onTogglerClick(menu);
                    }
                });

                menu.off("click", ".list-group-item");
                menu.on("click", ".list-group-item", function() {
                    menu.find(".list-group-item").each(function() {
                        $(placeholder).removeClass("active");
                    });
                    $(placeholder).addClass("active");
                    $(".glyphicon", placeholder).toggleClass("glyphicon-chevron-right").toggleClass("glyphicon-chevron-down");
                });


                menu.off("click", "a.list-group-item");
                menu.on("click", "a.list-group-item", function() {
                    if (options.closeOnClick) {
                        if ($(placeholder).attr("data-toggle") != "collapse") {
                            this.closeMenu(true, options, menu);
                        }
                    }
                });
            },
            ToggleArea: function() {
                var container = $("#internalbsm");
                var listaClassi = container[0].classList;
                var side = this.getSide(listaClassi);
                var containerWidth = container.width();
                var status = container.attr('data-status');
                if (!status) {
                    status = "opened";
                }
                if (status === 'closed') {
                    report.DashboardTiles();
                    report.DashboardWidgets();
                }
                this.doAnimation(container, containerWidth, side, status);
            },
            /*Cerca un div con classe submenu e id uguale a quello passato*/
            searchSubMenu: function(id) {
                var found = false;
                $('.submenu').each(function() {
                    var thisId = $(this).attr('id');
                    if (id == thisId) {
                        found = true;
                    }
                });
                return found;
            },
            readCookie: function(nome) {
                var name = nome + "=";
                var ca = document.cookie.split(";");
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == " ")
                        c = c.substring(1);
                    if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
                }
                return null;
            },
            startDefault: function(options, menu) {
                if (options.side == "left") {
                    if (options.autoClose) {
                        menu.status = "closed";
                        menu.hide().animate({
                            left: -(menu.width() + 2)
                        }, 1, function() {
                            menu.show();
                            //this.switchArrow("left",menu);
                        });
                    } else if (!options.autoClose) {
                        //this.switchArrow("right",menu);
                        menu.status = "opened";
                        if (options.pushBody) {
                            $("body").css("margin-left", menu.width() + 20);
                        }
                    }
                } else if (options.side == "right") {
                    if (options.autoClose) {
                        menu.status = "closed";
                        menu.hide().animate({
                            right: -(menu.width() + 2)
                        }, 1, function() {
                            menu.show();
                            // this.switchArrow("right",menu);
                        });
                    } else {
                        // this.switchArrow("left",menu);
                        menu.status = "opened";
                        if (options.pushBody) {
                            $("body").css("margin-right", menu.width() + 20);
                        }
                    }
                }
            },

            startClosed: function(options, menu) {
                if (options.side == "left") {
                    menu.status = "closed";
                    menu.hide().animate({
                        left: -(menu.width() + 2)
                    }, 1, function() {
                        menu.show();
                        //  this.switchArrow("left",menu);
                    });
                } else if (options.side == "right") {
                    menu.status = "closed";
                    menu.hide().animate({
                        right: -(menu.width() + 2)
                    }, 1, function() {
                        menu.show();
                        // this.switchArrow("right",menu);
                    })
                }
            },

            startOpened: function(options, menu) {
                if (options.side == "left") {
                    // this.switchArrow("right",menu);
                    menu.status = "opened";
                    if (options.pushBody) {
                        $("body").css("margin-left", menu.width() + 20);
                    }

                } else if (options.side == "right") {
                    //  this.switchArrow("left",menu);
                    menu.status = "opened";
                    if (options.pushBody) {
                        $("body").css("margin-right", menu.width() + 20);
                    }
                }
            },
            switchArrow: function(side, menu) {
                var span = menu.toggler.find("span.glyphicon");
                if (side == "left") {
                    span.removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
                } else if (side == "right") {
                    span.removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
                }
            },
            forSmallBody: function(options, menu) {
                var windowWidth = $(window).width();

                if (windowWidth <= 480) {
                    options.pushBody = false;
                    options.closeOnClick = true;
                } else {
                    options.pushBody = menu.originalPushBody;
                    options.closeOnClick = menu.originalCloseOnClick;
                }
            },
            //restituisce il lato del sidebar in base alla classe che trova settata
            getSide: function(listaClassi) {
                var side;
                for (var i = 0; i < listaClassi.length; i++) {
                    if (listaClassi[i] == 'sidebar-left') {
                        side = "left";
                        break;
                    } else if (listaClassi[i] == 'sidebar-right') {
                        side = "right";
                        break;
                    } else {
                        side = null;
                    }
                }
                return side;
            },
            //esegue l'animazione
            doAnimation: function(container, containerWidth, sidebarSide, sidebarStatus) {
                var toggler = container.children()[1];
                if (sidebarStatus == "opened") {
                    if (sidebarSide == "left") {
                        container.animate({
                            left: -(containerWidth + 2)
                        });
                        this.toggleArrow(toggler, "left");
                    } else if (sidebarSide == "right") {
                        container.animate({
                            right: -(containerWidth + 2)
                        });
                        this.toggleArrow(toggler, "right");
                    }
                    container.attr('data-status', 'closed');
                } else {
                    if (sidebarSide == "left") {
                        container.animate({
                            left: 0
                        });
                        this.toggleArrow(toggler, "right");
                    } else if (sidebarSide == "right") {
                        container.animate({
                            right: 0
                        });
                        this.toggleArrow(toggler, "left");
                    }
                    container.attr('data-status', 'opened');
                }
            },
            toggle: function(options, menu, body) {
                if (menu.status == "opened") {
                    this.closeMenu(true, options, menu, body);
                } else {
                    this.openMenu(true, options, menu, body);
                }
            },
            closeMenu: function(execFunctions, options, menu, body) {
                if (execFunctions) {
                    if (options.onBeforeClose !== undefined) {
                        options.onBeforeClose(menu);
                    }
                }
                if (options.side == "left") {

                    if (options.pushBody) {
                        $("body").animate({
                            marginLeft: body.originalMarginLeft
                        }, {
                            duration: options.duration
                        });
                    }

                    menu.animate({
                        left: -(menu.width() + 2)
                    }, {
                        duration: options.duration,
                        done: function() {
                            // switchArrow("left");
                            menu.status = "closed";

                            if (execFunctions) {
                                if (options.onClose !== undefined) {
                                    options.onClose(menu);
                                }
                            }
                        }
                    });
                } else if (options.side == "right") {

                    if (options.pushBody) {
                        $("body").animate({
                            marginRight: body.originalMarginRight
                        }, {
                            duration: options.duration
                        });
                    }

                    menu.animate({
                        right: -(menu.width() + 2)
                    }, {
                        duration: options.duration,
                        done: function() {
                            // switchArrow("right");
                            menu.status = "closed";

                            if (execFunctions) {
                                if (options.onClose !== undefined) {
                                    options.onClose(menu);
                                }
                            }
                        }
                    });
                }

                if (options.remember) {
                    this.storeCookie(menu.cookieName, "closed");
                }

            },
            storeCookie: function(nome, valore) {
                var d = new Date();
                d.setTime(d.getTime() + (24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = nome + "=" + valore + "; " + expires + "; path=/";
            },
            openMenu: function(execFunctions, options, menu, body) {

                if (execFunctions) {
                    if (options.onBeforeOpen !== undefined) {
                        options.onBeforeOpen(menu);
                    }
                }

                if (options.side == "left") {

                    if (options.pushBody) {
                        $("body").animate({
                            marginLeft: menu.width() + 20
                        }, {
                            duration: options.duration
                        });
                    }

                    menu.animate({
                        left: 0
                    }, {
                        duration: options.duration,
                        done: function() {
                            //switchArrow("right");
                            menu.status = "opened";

                            if (execFunctions) {
                                if (options.onOpen !== undefined) {
                                    options.onOpen(menu);
                                }
                            }
                        }
                    });
                } else if (options.side == "right") {

                    if (options.pushBody) {
                        $("body").animate({
                            marginRight: menu.width() + 20
                        }, {
                            duration: options.duration
                        });
                    }

                    menu.animate({
                        right: 0
                    }, {
                        duration: options.duration,
                        done: function() {
                            // switchArrow("left");
                            menu.status = "opened";

                            if (execFunctions) {
                                if (options.onOpen !== undefined) {
                                    options.onOpen(menu);
                                }
                            }
                        }
                    });
                }

                if (options.remember) {
                    this.storeCookie(menu.cookieName, "opened");
                }
            },
            toggleArrow: function(toggler, side) {
                if (side == "left") {
                    $(toggler).children(".glyphicon-chevron-right").css('display', 'block');
                    $(toggler).children(".glyphicon-chevron-left").css('display', 'none');
                } else if (side == "right") {
                    $(toggler).children(".glyphicon-chevron-left").css('display', 'block');
                    $(toggler).children(".glyphicon-chevron-right").css('display', 'none');
                }
            }
        };
    });