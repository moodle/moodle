var learnerscript = angular.module("learnerscript", ["dndLists", "smart-table", "ngMaterial"], function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});
learnerscript.controller("Reportdesign", function($scope, $http, $sce, $mdDialog, $location) {
    $scope.designdata = {};
    $scope.loading = true;
    $scope.aligns = [{
        name: 'left',
        image: 'pix/format_align_left.svg'
    }, {
        name: 'center',
        image: 'pix/format_align_center.svg'
    }, {
        name: 'right',
        image: 'pix/format_align_right.svg'
    }];
    $scope.text_format = 'justify';
    $scope.wraping = ["wrap", "nowrap"];
    $scope.reportid = angular.element('#ls_reportid').val();
    var parameter = JSON.stringify({
        action: 'designdata',
        reportid: $scope.reportid
    });
    // $scope.advancedColumns = advancedColumns;
    $scope.showDialog = showDialog;
    $scope.DialogController = DialogController;
    $scope.advancedColumns = function($event, avcolumn) {
        require(['block_learnerscript/helper'], function(helper) {
            helper.PlotForm({
                action: 'advancedcolumns',
                reportid: $scope.reportid,
                advancedcolumn: avcolumn,
                component: 'columns',
                title: 'Advanced Columns'
            });
        });
    }
    $scope.preview = function(ev) {
        require(['block_learnerscript/helper'], function(helper) {
            helper.Preview({
                container: '.smart_table_container'
            });
        });
        // $mdDialog.show({
        //       contentElement: '.smart_table_container',
        //       parent: angular.element(document.body),
        //       targetEvent: ev,
        //       clickOutsideToClose: true
        //   });
    };
    function showDialog ($event) {
        var parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            controller: DialogController,
            preserveScope: true,
            scope: $scope,
            clickOutsideToClose: true,
            templateUrl: 'popuptemp.html',
            locals: {
                conditions: $scope.conditions,
                reportid: $scope.reportid
            },
        });
    }

        function DialogController ($scope, $mdDialog, conditions, reportid, $filter, $http) {
            $scope.conditions = conditions;
            $scope.reportid = reportid;
            $scope.closeDialog = function() {
                $mdDialog.hide();
            }
            $scope.resetConditions = function(fields, columns) {
                removeValFromIndex = [];
                angular.forEach(conditions.elements, function(mainfield, mkey) {
                    if ($scope.conditions.finalelements.elements.indexOf(mkey) < 0) {
                        var i = 0;
                        angular.forEach(columns, function(colvalue, colkey) {
                            if (colvalue.indexOf(mkey) == 0) {
                                removeValFromIndex[i] = colkey;
                            }
                            i++;
                        });
                    }
                });
                for (var i = removeValFromIndex.length - 1; i >= 0; i--) {
                    delete $scope.conditions.finalelements.selectedfields;
                    delete $scope.conditions.finalelements.selectedcondition;
                    delete $scope.conditions.finalelements.selectedvalue;
                }
            }
            $scope.addCondition = function() {
                var req = {
                    method: 'POST',
                    url: M.cfg.wwwroot + '/blocks/learnerscript/ajax.php',
                    data: {
                        action: 'updatereport_conditions',
                        reportid: $scope.reportid,
                        conditions: $filter('json')($scope.conditions.finalelements)
                    }
                }
                $http(req).then(function(response) {
                    $scope.closeDialog();
                });
            }
        }
    $scope.AdvController = function($scope, $mdDialog, items, conditions, $filter, $http) {
        $scope.closeDialog = function() {
            $mdDialog.hide();
        }
    }
    $scope.setChoiceIndex = function(element, val, type) {
        if (type === 'align') {
            element.align = val;
            $scope.$parent.text_format = val;
        } else if (type === 'wrap') {
            element.wrap = val;
        }
    }
    $scope.alignment = function() {}
    $scope.setwrap = function() {}
    $scope.randomString = function(length) {
        return Math.round((Math.pow(15, length + 1) - Math.random() * Math.pow(15, length))).toString(15).slice(1);
    }
    $scope.onDrop = function(srcList, srcIndex, targetList, targetIndex, type) {
        targetList.splice(targetIndex, 0, srcList[srcIndex]);
        if (type != 'selectedcolumns') {
            $scope.lists.columns.elements[targetIndex].type = 'selectedcolumns';
            $scope.lists.availablecolumns.elements[srcIndex].formdata.value = true;
        }
        if (type == 'selectedcolumns') {
            $scope.lists.calculations.calcpluginname.splice(targetIndex, 0, null );
        }
        // if ($scope.lists.calculations && $scope.lists.calculations.elements) {
        //     calcusoptions = $scope.lists.calculations.calcoptions;
        //     for (var i = $scope.lists.calculations.elements.length - 1; i >= 0; i--) {
        //         calcjsondata = angular.toJson($scope.lists.calculations.calcpluginname, true);
        //         idexist = calcjsondata.search($scope.lists.calculations.elements[i].id);
        //         if (i == targetIndex || idexist == -1) {
        //             $scope.lists.calculations.calcpluginname[i] = '';
        //         } else {
        //             if (idexist != -1) {
        //                 $scope.lists.calculations.calcpluginname[i] = $scope.lists.calculations.elements[i].pluginname;
        //             } else {
        //                 continue;
        //             }
        //         }
        //     }
        // }
        return true;
    };
    $scope.updateSelectColumns = function(column, index) {
        var count = 0;
        angular.forEach($scope.lists.columns.elements, function(value, key) {
            count += value.formdata.column == column.formdata.column ? 1 : 0;
        });
        var selectedcolumn = angular.copy(column);
        if (selectedcolumn.formdata.value == true) {
            selectedcolumn.type = 'selectedcolumns';
            $scope.lists.availablecolumns.elements[index].type = 'selectedcolumns';
            $scope.lists.columns.elements.push(selectedcolumn);
        } else {
            for (var i = $scope.lists.columns.elements.length - 1; i >= 0; i--) {
                if ($scope.lists.columns.elements[i].formdata.column == column.formdata.column) {
                    $scope.lists.columns.elements.splice(i, 1);
                    column.type = 'columns';
                }
            }
            if (typeof $scope.lists.calculations != 'undefined') {
                for (var i = $scope.lists.calculations.elements.length - 1; i >= 0; i--) {
                    if ($scope.lists.calculations.elements[i].formdata.column == column.formdata.column) {
                        $scope.lists.calculations.elements.splice(i, 1);
                        // $scope.lists.availablecolumns.elements[i].type = 'calculations';
                    }
                }
            }
            if (count == 0) {
                for (var i = $scope.lists.availablecolumns.elements.length - 1; i >= 0; i--) {
                    if ($scope.lists.availablecolumns.elements[i].formdata.column == column.formdata.column) {
                        $scope.lists.availablecolumns.elements[i].formdata.value = false;
                        $scope.lists.availablecolumns.elements[i].type = 'columns';
                    }
                }
            }
        }
    }
    $scope.checkAvailability = function(column) {
        var count = 0;
        angular.forEach($scope.lists.columns.elements, function(value, key) {
            count += value.formdata.column == column.formdata.column ? 1 : 0;
        });
        if (typeof $scope.lists.calculations != 'undefined') {
            for (var i = $scope.lists.calculations.elements.length - 1; i >= 0; i--) {
                if ($scope.lists.calculations.elements[i].formdata.column == column.formdata.column) {
                    $scope.lists.calculations.elements.splice(i, 1);
                    // $scope.lists.availablecolumns.elements[i].type = 'calculations';
                }
            }
        }
        if (count == 0) {
            for (var i = $scope.lists.availablecolumns.elements.length - 1; i >= 0; i--) {
                if ($scope.lists.availablecolumns.elements[i].formdata.column == column.formdata.column) {
                    $scope.lists.availablecolumns.elements[i].formdata.value = false;
                    $scope.lists.availablecolumns.elements[i].type = 'columns';
                }
            }
        }
    }
    $scope.updateCalculations = function(column, val, index) {
        var calc = angular.copy(column);
        calc.type = 'calculations'
        var ranstring, idunique;
        comp = angular.toJson($scope.lists, true);
        ranstring = $scope.randomString(15);
        stringpos = comp.search(ranstring);
        if (stringpos == -1) {
            calc.id = ranstring;
        } else {
            idunique = $scope.randomString(15);
            calc.id = targetIndex;
        }
        delete calc.formdata.value;
        delete calc.formdata.heading;
        calc.pluginname = val;
        $scope.lists.calculations.elements[index] = calc;
    };

    $http.post(M.cfg.wwwroot + '/blocks/learnerscript/ajax.php', parameter).
    success(function(data, status, headers, config) {
        $scope.designdata = data;
        $scope.reportdata = {};
        $scope.reportTable = $scope.designdata.rows;
        $scope.lists = {};
        $scope.conditions = $scope.designdata.conditioncolumns;
        $scope.response = '';
        $scope.exports = data.exportlist;
        $scope.exportoptions = function(exportoption, index) {
            $scope.lists.exports = $scope.exports;
            var oldclass = $('.' + exportoption);
            if (oldclass.hasClass("exportopt")) {
                $('.' + exportoption).addClass('intro');
                $('.' + exportoption).removeClass('exportopt');
            } else {
                $('.' + exportoption).addClass('exportopt');
                $('.' + exportoption).removeClass('intro');
            }
            $scope.lists.exports[index].value = !$scope.lists.exports[index].value;
        };
        if (data.availablecolumns == null || data.availablecolumns.length == 0) {
            require(['core/modal_factory'], function(ModalFactory) {
                ModalFactory.create({
                    title: 'No Columns',
                    body: '<p>No columns available to create report!!!</p>',
                    footer: '',
                }).done(function(modal) {
                    dialogue = modal;
                    ModalEvents = require('core/modal_events');
                    dialogue.getRoot().on(ModalEvents.hidden, function() {
                        window.location = M.cfg.wwwroot + '/blocks/learnerscript/viewreport.php?' + $('#ls_reportparams').val();
                    });

                    dialogue.show();
                });
            });
        }
        $scope.lists['availablecolumns'] = {
            label: "Available Columns",
            type: "static",
            elements: $scope.designdata.availablecolumns,
            effect: 'link',
            dnd: true,
            widthclass: 'width-3'
        };
        $scope.lists['columns'] = {
            label: "Selected Columns",
            allowedTypes: ['columns'],
            type: "input",
            elements: $scope.designdata.selectedcolumns,
            effect: 'link',
            widthclass: 'width-6'
        };
        if (data.filtercolumns != null && data.filtercolumns != '') {
            $scope.lists['filters'] = {
                label: "Filters",
                allowedTypes: [],
                type: "static",
                elements: $scope.designdata.filtercolumns,
                effect: 'link',
                widthclass: 'width-3'
            };
        }
        if (data.calculations != '' && data.calculations != null) {
            $scope.lists['calculations'] = {
                label: "Calculations",
                allowedTypes: ['selectedcolumns'],
                type: "static",
                elements: $scope.designdata.calccolumns,
                effect: 'link',
                widthclass: 'width-3',
                calcpluginname: $scope.designdata.calcpluginname,
                calcoptions: $scope.designdata.calculations
            };
        }
        if (data.ordercolumns != '' && data.ordercolumns != null) {
            $scope.lists['ordering'] = {
                label: "Ordering",
                allowedTypes: [],
                type: "static",
                elements: $scope.designdata.ordercolumns,
                effect: 'link',
                widthclass: 'width-3'
            };
        }
        if ($scope.lists.calculations) {
            calcusoptions = $scope.lists.calculations.calcoptions;
            $.each($scope.lists.calculations.calcpluginname, function(key, value) {
                for (var i = $scope.lists.calculations.elements.length - 1; i >= 0; i--) {
                    if ($scope.lists.calculations.elements[i].id === key) {
                        $scope.lists.calculations.calcpluginname[i] = value;
                    }
                }
            });
        }
        $scope.trustAsHtml = $sce.trustAsHtml;
        // Model to JSON for demo purpose
        $scope.$watch('lists', function(model) {
            $scope.reportData = $scope.designdata.reportdata;
            $scope.modelAsJson = angular.toJson(model, true);
            $scope.save = function() {
                var req = {
                    method: 'POST',
                    url: M.cfg.wwwroot + '/blocks/learnerscript/ajax.php',
                    data: {
                        action: 'updatereport',
                        reportid: $scope.reportid,
                        components: JSON.stringify(model)
                    }
                }
                $http(req).then(function(response) {
                    window.location = M.cfg.wwwroot + '/blocks/learnerscript/viewreport.php?' + $('#ls_reportparams').val();
                });
            };
        }, true);
    }).error(function(data, status, headers, config) {
        // called asynchronously if an error occurs
        // or server returns response with an error status.
    }).finally(function() {
        // called no matter success or failure
        $scope.loading = false;
    });
});