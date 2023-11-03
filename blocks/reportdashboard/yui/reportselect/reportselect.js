YUI.add('moodle-block_reportdashboard-reportselect', function (Y) {
    var REPORTSELECT = 'block_reportdashboard-reportselect';
    var REPORTSELECT = function () {
        REPORTSELECT.superclass.constructor.apply(this, arguments);
    }

    Y.extend(REPORTSELECT, Y.Base, {
        initializer: function (params) {
            if (params && params.formid) {
                var updatebut = Y.one('#' + params.formid + ' #id_updatereportselect');
                var reportselector = Y.one('#' + params.formid + ' #id_config_reportlist');
                if (updatebut && reportselector) {
                    updatebut.setStyle('display', 'none');
                    reportselector.on('change', function () {
                        updatebut.simulate('click');
                    });
                }
            }
        }
    });

    M.block_reportdashboard = M.block_reportdashboard || {};
    M.block_reportdashboard.init_reportselect = function (params) {
        return new REPORTSELECT(params);
    }
}, '@VERSION@', {requires: ['base', 'node', 'node-event-simulate']});
