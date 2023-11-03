var conditions = angular.module("conditions", ["ngMaterial"], function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});
conditions.controller("ConditionsController", function($scope, $http, $sce, $mdDialog, $location) {
$scope.reportid = angular.element('#ls_reportid').val();
var parameter = JSON.stringify({
    action: 'configureplot',
    reportid: $scope.reportid
});
$scope.columns = [];
$scope.configureplot = [];
$scope.conditionssymbols = ["=", ">", "<", ">=", "<=", "<>", "LIKE", "NOT LIKE", "LIKE % %"];
$http.post(M.cfg.wwwroot + '/blocks/learnerscript/ajax.php', parameter).
    success(function(data, status, headers, config) {
        $scope.configuredata = data;
        $scope.columns = $scope.configuredata.columns;
        $scope.configureplot = $scope.configuredata.plot;
    }).error(function(data, status, headers, config) {
        // called asynchronously if an error occurs
        // or server returns response with an error status.
    }).finally(function() {
        // called no matter success or failure
        $scope.loading = false;
    });
$scope.DialogController = DialogController;
$scope.showDialog = showDialog;
function showDialog ($event) {
        var parentEl = angular.element(document.body);
        $mdDialog.show({
            parent: parentEl,
            targetEvent: $event,
            controller: DialogController,
            preserveScope: true,
            scope: $scope,
            clickOutsideToClose: true,
            templateUrl: M.cfg.wwwroot + '/blocks/learnerscript/templates/conditions.mustache',
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
    $scope.onSelect = function(columnid) {
        $scope.configureplot.yaxis[columnid] = {name: '', operator: '', value: ''};
    }
    });