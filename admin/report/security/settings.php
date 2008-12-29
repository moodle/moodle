<?php  //$Id$

$ADMIN->add('reports', new admin_externalpage('reportsecurity', get_string('reportsecurity', 'report_security'), "$CFG->wwwroot/$CFG->admin/report/security/index.php",'report/security:view'));
