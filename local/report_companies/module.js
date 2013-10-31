M.local_report_companies = {};

M.local_report_companies.init = function(Y) {

    Y.on('change', function(e) {
        Y.one('#mform1').submit();
    }, '#id_company' );

    Y.on('change', function(e) {
        Y.one('#mform1').submit();
    }, '#id_repcourse' );

};
