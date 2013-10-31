M.local_iomad_dashboard = {};

M.local_iomad_dashboard.init = function(Y) {

    var handleClick = function(e) {
       Y.log(e);
    };
    var raisecompany1frommain = function(e) {
       YAHOO.util.Dom.setStyle('iomad_company_admin_company1', 'top','-200px');
       YAHOO.util.Dom.setStyle('iomad_company_admin_main1', 'top','200px');
    };
    var raiseuser1frommain = function(e) {
       YAHOO.util.Dom.setStyle('iomad_company_admin_user1', 'top','-400px');
       YAHOO.util.Dom.setStyle('iomad_company_admin_main1', 'top','200px');
    };
    var raisecourse1frommain = function(e) {
       YAHOO.util.Dom.setStyle('iomad_company_admin_course1', 'top','-600px');
       YAHOO.util.Dom.setStyle('iomad_company_admin_main1', 'top','200px');
    };
    var raisemain1fromuser1 = function(e) {
       YAHOO.util.Dom.setStyle('iomad_company_admin_user1', 'top','0px');
       YAHOO.util.Dom.setStyle('iomad_company_admin_main1', 'top','0px');
    };
    var raisemain1fromcourse1 = function(e) {
       YAHOO.util.Dom.setStyle('iomad_company_admin_course1', 'top','0px');
       YAHOO.util.Dom.setStyle('iomad_company_admin_main1', 'top','0px');
    };
    var raisemain1fromcompany1 = function(e) {
       YAHOO.util.Dom.setStyle('iomad_company_admin_company1', 'top','0px');
       YAHOO.util.Dom.setStyle('iomad_company_admin_main1', 'top','0px');
    };

    Y.on('click', raisecompany1frommain, '#ELDMSCAMCB' );
    Y.on('click', raiseuser1frommain, '#ELDMSCAMUB' );
    Y.on('click', raisecourse1frommain, '#ELDMSCAMLB' );
    Y.on('click', raisemain1fromcompany1, '#ELDMSCACBB' );
    Y.on('click', raisemain1fromuser1, '#ELDMSCAUBB' );
    Y.on('click', raisemain1fromcourse1, '#ELDMSCALBB' );

};
