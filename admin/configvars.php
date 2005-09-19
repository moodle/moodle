<?php // $Id$
      // Shane Elliott

/// Add new sitewide configuration variables to this file.


/// $configvars is parsed by config.php
/// It is an array of arrays of objects
/// $configvars[sectionname][configvariablename] = configvar object
    $configvars = array();


/// no, yes strings and menu options are used in a number of places
/// so we define them here to save time on repeatedly calling
/// get_string()
    $stryes = get_string('yes');
    $strno  = get_string('no');

    $noyesoptions[0] = $strno;
    $noyesoptions[1] = $stryes;



/// A class to hold the configurable information
/// $field   - the html code for the form field
/// $help    - help text for the field
/// $warning - optional warning text to be displayed
/// method display_warning() - a generic function that can be used in an extended class
///     e.g. enablerssfeeds
class configvar {

    var $field;
    var $help;
    var $warning;

    function configvar($help, $field, $warning='') {
        $this->help    = $help;
        $this->field   = $field;
        $this->warning = $warning;
    }

    function display_warning() {
        return false;
    }
}





////////////////////////////////////////////////////////////////////
/// Miscellaneous config variables
////////////////////////////////////////////////////////////////////
    $misc = array();


/// maxeditingtime
    unset($options);
    $options[3600] = get_string('numminutes', '', 60);
    $options[2700] = get_string('numminutes', '', 45);
    $options[1800] = get_string('numminutes', '', 30);
    $options[900]  = get_string('numminutes', '', 15);
    $options[300]  = get_string('numminutes', '', 5);
    $options[60]   = get_string('numminutes', '', 1);

    $misc['maxeditingtime'] = new configvar (get_string('configmaxeditingtime', 'admin'),
        choose_from_menu ($options, 'maxeditingtime', $config->maxeditingtime, '', '', '', true) );

/// debug
    unset($options);
    $options[7]  = $strno;
    $options[15] = $stryes;

    $misc['debug'] = new configvar (get_string('configdebug', 'admin'),
        choose_from_menu ($options, 'debug', $config->debug, '', '', '', true) );

/// enablerssfeeds
class configvarrss extends configvar {
    function display_warning() {
        return (!function_exists('utf8_encode'));
    }
}

    $misc['enablerssfeeds'] = new configvarrss (get_string('configenablerssfeeds', 'admin'),
        choose_from_menu ($noyesoptions, 'enablerssfeeds', $config->enablerssfeeds, '', '', '', true),
        '<font color="red"> You need to add XML support to your PHP installation.</font>' );

    $misc['mymoodleredirect'] = new configvar (get_string('configmymoodleredirect','admin'),
        choose_from_menu($noyesoptions,'mymoodleredirect',$config->mymoodleredirect,'','','',true));


////////////////////////////////////////////////////////////////////
/// OPERATING SYSTEM config variables
////////////////////////////////////////////////////////////////////
    $operatingsystem = array();

/// gdversion
    unset($options);
    $options[0] = get_string('gdnot');
    $options[1] = get_string('gd1');
    $options[2] = get_string('gd2');

    $installed  = check_gd_version();

    $operatingsystem['gdversion'] = new configvar (get_string('configgdversion', 'admin'),
        choose_from_menu ($options, 'gdversion', $installed, '', '', '', true) );

/// dbsessions
    $operatingsystem['dbsessions'] = new configvar (get_string('configdbsessions', 'admin'),
        choose_from_menu ($noyesoptions, 'dbsessions', $config->dbsessions, '', '', '', true) );

/// sessiontimeout
    unset($options);
    $options[14400] = get_string('numhours', '', 4);
    $options[10800] = get_string('numhours', '', 3);
    $options[7200]  = get_string('numhours', '', 2);
    $options[5400]  = get_string('numhours', '', '1.5');
    $options[3600]  = get_string('numminutes', '', 60);
    $options[2700]  = get_string('numminutes', '', 45);
    $options[1800]  = get_string('numminutes', '', 30);
    $options[900]   = get_string('numminutes', '', 15);
    $options[300]   = get_string('numminutes', '', 5);

    $operatingsystem['sessiontimeout'] = new configvar (get_string('configsessiontimeout', 'admin'),
        choose_from_menu ($options, 'sessiontimeout', $config->sessiontimeout, '', '', '', true) );

/// sessioncookie
    $operatingsystem['sessioncookie'] = new configvar (get_string('configsessioncookie', 'admin'),
        '<input name="sessioncookie" type="text" size="10" value="'.s($config->sessioncookie).'" alt="sessioncookie" />' );

/// zip
    $operatingsystem['zip'] = new configvar (get_string('configzip', 'admin'),
        '<input name="zip" type="text" size="30" value="'.s($config->zip).'" alt="zip" />' );

/// unzip
    $operatingsystem['unzip'] = new configvar (get_string('configunzip', 'admin'),
        '<input name="unzip" type="text" size="30" value="'.s($config->unzip).'" alt="unzip" />' );

    $operatingsystem['pathtodu'] = new configvar(get_string('configpathtodu', 'admin'),
        '<input name="pathtodu" type="text" size="30" value="'.s($config->pathtodu).'" alt="pathtodu" />');                                                

/// slasharguments
    unset($options);
    $options[0] = "file.php?file=/pic.jpg";
    $options[1] = "file.php/pic.jpg";

    $operatingsystem['slasharguments'] = new configvar (get_string('configslasharguments', 'admin'),
        choose_from_menu ($options, 'slasharguments', $config->slasharguments, '', '', '', true) );

/// proxyhost
    $operatingsystem['proxyhost'] = new configvar (get_string('configproxyhost', 'admin'),
        '<input name="proxyhost" type="text" size="30" value="'.s($config->proxyhost).'" alt="proxyhost" />' );

/// proxyport
    $operatingsystem['proxyport'] = new configvar ('',
        '<input name="proxyport" type="text" size="5" value="'.s($config->proxyport).'" alt="proxyport" />' );



////////////////////////////////////////////////////////////////////
/// PERMISSIONS config variables
////////////////////////////////////////////////////////////////////
    $permissions = array();

/// teacherassignteachers
    $permissions['teacherassignteachers'] = new configvar (get_string('configteacherassignteachers', 'admin'),
        choose_from_menu ($noyesoptions, 'teacherassignteachers', $config->teacherassignteachers, '', '', '', true) );

/// allowunenroll
    $permissions['allowunenroll'] = new configvar (get_string('configallowunenroll', 'admin'),
        choose_from_menu ($noyesoptions, 'allowunenroll', $config->allowunenroll, '', '', '', true) );

/// allusersaresitestudents
    $permissions['allusersaresitestudents'] = new configvar (get_string('configallusersaresitestudents', 'admin'),
        choose_from_menu ($noyesoptions, 'allusersaresitestudents', $config->allusersaresitestudents, '', '', '', true) );

/// showsiteparticipantslist
    unset($options);
    $options[0]  = get_string('siteteachers');
    $options[1]  = get_string('allteachers');
    $options[2]  = get_string('studentsandteachers');

    $permissions['showsiteparticipantslist'] = new configvar (get_string('configshowsiteparticipantslist', 'admin'),
        choose_from_menu ($options, 'showsiteparticipantslist', $config->showsiteparticipantslist, '', '', '', true) );

/// forcelogin
    $permissions['forcelogin'] = new configvar (get_string('configforcelogin', 'admin'),
        choose_from_menu ($noyesoptions, 'forcelogin', $config->forcelogin, '', '', '', true) );

/// forceloginforprofiles
   $permissions['forceloginforprofiles'] = new configvar (get_string('configforceloginforprofiles', 'admin'),
        choose_from_menu ($noyesoptions, 'forceloginforprofiles', $config->forceloginforprofiles, '', '', '', true) );

/// opentogoogle
    $permissions['opentogoogle'] = new configvar (get_string('configopentogoogle', 'admin'),
        choose_from_menu ($noyesoptions, 'opentogoogle', $config->opentogoogle, '', '', '', true) );

/// maxbytes
    $options = get_max_upload_sizes();

    $permissions['maxbytes'] = new configvar (get_string('configmaxbytes', 'admin'),
        choose_from_menu ($options, 'maxbytes', $config->maxbytes, '', '', 0, true) );

/// messaging
    $permissions['messaging'] = new configvar (get_string('configmessaging', 'admin'),
        choose_from_menu ($noyesoptions, 'messaging', $config->messaging, '', '', '', true) );

/// allowobjectembed
    $permissions['allowobjectembed'] = new configvar (get_string('configallowobjectembed', 'admin'),
        choose_from_menu ($noyesoptions, 'allowobjectembed', $config->allowobjectembed, '', '', '', true) );


    unset($options);
    $options['none'] = 'No courses';
    $options['all'] = 'All courses';
    $options['requested'] = 'Requested courses';

    $permissions['restrictmodulesfor'] = new configvar (get_string('configrestrictmodulesfor','admin'),
   ' <script language="JavaScript">
    function togglemodules(index) {
        if (index == 0) {
            document.getElementById(\'allowedmodules\').disabled=true;
        }
        else {
            document.getElementById(\'allowedmodules\').disabled=false;
        }
    }
    </script>'.
        choose_from_menu($options,'restrictmodulesfor',$config->restrictmodulesfor,'','togglemodules(this.selectedIndex);','',true) );

    $permissions['restrictbydefault'] = new configvar (get_string('configrestrictbydefault','admin'),
        choose_from_menu($noyesoptions, 'restrictbydefault',$config->restrictbydefault,'','','',true) );

    $allowstr = '<select name="defaultallowedmodules[]" id="allowedmodules" multiple="multiple" size="10"'.((empty($config->restrictmodulesfor)) ? "disabled=\"disabled\"" : "").'>';

    $allowedmodules = array();
    if (!empty($config->defaultallowedmodules)) {
        $allowedmodules = explode(',',$config->defaultallowedmodules);
    }

//  On a fresh install of Moodle, this could be empty; prevent a warning on the following loop.
    if (!$mods = get_records("modules")) {
        $mods = array();
    }
    $s = "selected=\"selected\"";
    $allowstr .= '<option value="0" '.((empty($allowedmodules)) ? $s : '').'>'.get_string('allownone').'</option>'."\n";
    foreach ($mods as $mod) {
        $selected = "";
        if (in_array($mod->id,$allowedmodules)) 
            $selected = $s;
        $allowstr .= '<option '.$selected.' value="'.$mod->id.'">'.$mod->name.'</option>'."\n";
    }  
    $allowstr .= '</select>';

    $permissions['defaultallowedmoules'] = new configvar (get_string('configdefaultallowedmodules','admin'),$allowstr);


/// course requests
    $reqcourse['enablecourserequests'] = new configvar (get_string('configenablecourserequests', 'admin'),
        choose_from_menu ($noyesoptions,'enablecourserequests',$config->enablecourserequests,'','','',true) );

/// default category for course requests
    require_once($CFG->dirroot.'/course/lib.php');
    $reqcourse['defaultrequestedcategory'] = new configvar (get_string('configdefaultrequestedcategory', 'admin'),
        choose_from_menu (make_categories_options(), 'defaultrequestedcategory',$config->defaultrequestedcategory,'','','',true) );

    $reqcourse['requestedteachername'] = new configvar (get_string('configrequestedteachername','admin'),
        '<input type="text" name="requestedteachername" size="20" maxlength="100" value="'.s($config->requestedteachername).'" />');

    $reqcourse['requestedteachersname'] = new configvar (get_string('configrequestedteachersname','admin'),
        '<input type="text" name="requestedteachersname" size="20" maxlength="100" value="'.s($config->requestedteachersname).'" />');

    $reqcourse['requestedstudentname'] = new configvar (get_string('configrequestedstudentname','admin'),
        '<input type="text" name="requestedstudentname" size="20" maxlength="100" value="'.s($config->requestedstudentname).'" />');

    $reqcourse['requestedstudentsname'] = new configvar (get_string('configrequestedstudentsname','admin'),
        '<input type="text" name="requestedstudentsname" size="20" maxlength="100" value="'.s($config->requestedstudentsname).'" />');

////////////////////////////////////////////////////////////////////
/// INTERFACE config variables
////////////////////////////////////////////////////////////////////
    $interface = array();

/// language settings
    $interface['lang'] = new configvar ( get_string('configlang', 'admin'),
        choose_from_menu(get_list_of_languages(), 'lang', $config->lang, '', '', '', true) );

/// language menu
    $interface['langmenu'] = new configvar ( get_string('configlangmenu', 'admin'),
        choose_from_menu($noyesoptions, 'langmenu', $config->langmenu, '', '', '', true) );

/// language list
    $interface['langlist'] = new configvar ( get_string('configlanglist', 'admin'),
        '<input name="langlist" type="text" size="60" value="'.s($config->langlist).'" alt="langlist" />' );

/// language menu
    $interface['langcache'] = new configvar ( get_string('configlangcache', 'admin'),
        choose_from_menu($noyesoptions, 'langcache', $config->langcache, '', '', '', true) );
/// locale
    $interface['locale'] = new configvar ( get_string('configlocale', 'admin'),
        '<input name="locale" type="text" size="10" value="'.s($config->locale).'" alt="locale" />' );

/// timezone
    
    $interface['timezone'] = new configvar ( get_string('configtimezone', 'admin'),
        choose_from_menu (get_list_of_timezones(), 'timezone', $config->timezone, get_string('serverlocaltime'), '', '99', true ) );

/// country
    $interface['country'] = new configvar ( get_string('configcountry', 'admin'),
        choose_from_menu (get_list_of_countries(), 'country', $config->country, get_string('selectacountry'), '', 0, true) );

/// framename
    if (empty($config->framename)) {
        $config->framename = "_top";
    }

    $interface['framename'] = new configvar (get_string('configframename', 'admin'),
        '<input name="framename" type="text" size="15" value="'.s($config->framename).'" alt="framename" />' );

/// language list
    $interface['themelist'] = new configvar ( get_string('configthemelist', 'admin'),
        '<input name="themelist" type="text" size="60" value="'.s($config->themelist).'" alt="themelist" />' );

/// user themes
    $interface['allowuserthemes'] = new configvar (get_string('configallowuserthemes', 'admin'),
        choose_from_menu ($noyesoptions, 'allowuserthemes', $config->allowuserthemes, '', '', '', true) );

/// course themes
    $interface['allowcoursethemes'] = new configvar (get_string('configallowcoursethemes', 'admin'),
        choose_from_menu ($noyesoptions, 'allowcoursethemes', $config->allowcoursethemes, '', '', '', true) );

/// allowuserblockhiding
    $interface['allowuserblockhiding'] = new configvar (get_string('configallowuserblockhiding', 'admin'),
        choose_from_menu ($noyesoptions, 'allowuserblockhiding', $config->allowuserblockhiding, '', '', '', true) );

/// showblocksonmodpages
    $interface['showblocksonmodpages'] = new configvar (get_string('configshowblocksonmodpages', 'admin'),
        choose_from_menu ($noyesoptions, 'showblocksonmodpages', $config->showblocksonmodpages, '', '', '', true) );


/// tabselectedtofront
    $interface['tabselectedtofront'] = new configvar (get_string('tabselectedtofront', 'admin'),
        choose_from_menu ($noyesoptions, 'tabselectedtofront', $config->tabselectedtofront, '', '', '', true) );



////////////////////////////////////////////////////////////////////
/// USER config variables
////////////////////////////////////////////////////////////////////
    $user = array();

/// sitepolicy
    $user['sitepolicy'] = new configvar (get_string('configsitepolicy', 'admin'),
        '<input type="text" name="sitepolicy" size="60" value="'.$config->sitepolicy.'" alt="sitepolicy" />' );


/// fullnamedisplay
    unset($options);
    $options['language']  = get_string('language');
    $options['firstname lastname']  = get_string('firstname') . ' + ' . get_string('lastname');
    $options['lastname firstname']  = get_string('lastname') . ' + ' . get_string('firstname');
    $options['firstname']  = get_string('firstname');

    $user['fullnamedisplay'] = new configvar (get_string('configfullnamedisplay', 'admin'),
        choose_from_menu ($options, 'fullnamedisplay', $config->fullnamedisplay, '', '', '', true) );

/// extendedusernamechars
    $user['extendedusernamechars'] = new configvar (get_string('configextendedusernamechars', 'admin'),
        choose_from_menu ($noyesoptions, 'extendedusernamechars', $config->extendedusernamechars, '', '', '', true) );

/// autologinguests
    $user['autologinguests'] = new configvar (get_string('configautologinguests', 'admin'),
        choose_from_menu ($noyesoptions, 'autologinguests', $config->autologinguests, '', '', '', true) );





////////////////////////////////////////////////////////////////////
/// SECURITY config variables
////////////////////////////////////////////////////////////////////
    $security = array();

/// displayloginfailures
    unset($options);
    $options[''] = get_string('nobody');
    $options['admin'] = get_string('administrators');
    $options['teacher'] = get_string('administratorsandteachers');
    $options['everybody'] = get_string('everybody');

    $security['displayloginfailures'] = new configvar (get_string('configdisplayloginfailures', 'admin'),
        choose_from_menu($options, 'displayloginfailures', $config->displayloginfailures, '', '', '', true) );

/// notifyloginfailures
    unset($options);
    $options[''] = get_string('nobody');
    $options['mainadmin'] = get_string('administrator');
    $options['alladmins'] = get_string('administratorsall');

    $security['notifyloginfailures'] = new configvar (get_string('confignotifyloginfailures', 'admin'),
        choose_from_menu($options, 'notifyloginfailures', $config->notifyloginfailures, '', '', '', true) );

/// notifyloginthreshold
    unset($options);
    for ($i=1; $i<=100; $i++) {
        $options[$i] = "$i";
    }

    $security['notifyloginthreshold'] = new configvar (get_string('confignotifyloginthreshold', 'admin'),
        choose_from_menu($options, 'notifyloginthreshold', $config->notifyloginthreshold, '', '', '', true) );

/// secureforms
    $security['secureforms'] = new configvar (get_string('configsecureforms', 'admin'),
        choose_from_menu ($noyesoptions, 'secureforms', $config->secureforms, '', '', '', true) );

/// loginhttps
    $security['loginhttps'] = new configvar (get_string('configloginhttps', 'admin'),
        choose_from_menu ($noyesoptions, 'loginhttps', $config->loginhttps, '', '', '', true) );

/// runclamonupload
    $security['runclamonupload'] = new configvar (get_string('configrunclamonupload', 'admin'),
        choose_from_menu($noyesoptions, 'runclamonupload', $config->runclamonupload, '', '', '', true) );

/// pathtoclam
    $security['pathtoclam'] = new configvar (get_string('configpathtoclam', 'admin'),
        '<input type="text" name="pathtoclam" size="30" value="'.$config->pathtoclam.'" alt="pathtoclam" />' );

/// quarantinedir
    $security['quarantinedir'] = new configvar (get_string('configquarantinedir', 'admin'),
        '<input type="text" name="quarantinedir" size="30" value="'.$config->quarantinedir.'" alt="quarantinedir" />' );

/// clamfailureonupload
    unset($options);
    $options['donothing'] = get_string('configclamdonothing', 'admin');
    $options['actlikevirus'] = get_string('configclamactlikevirus', 'admin');

    $security['clamfailureonupload'] = new configvar (get_string('configclamfailureonupload', 'admin'),
        choose_from_menu($options, 'clamfailureonupload', $config->clamfailureonupload, '', '', '', true) );




////////////////////////////////////////////////////////////////////
/// MAINTENANCE config variables
////////////////////////////////////////////////////////////////////
    $maintenance = array();

/// longtimenosee
    unset($options);
    $options[0]    = get_string('never');
    $options[1000] = get_string('numdays', '', 1000);
    $options[365]  = get_string('numdays', '', 365);
    $options[180]  = get_string('numdays', '', 180);
    $options[150]  = get_string('numdays', '', 150);
    $options[120]  = get_string('numdays', '', 120);
    $options[90]   = get_string('numdays', '', 90);
    $options[60]   = get_string('numdays', '', 60);
    $options[30]   = get_string('numdays', '', 30);
    $options[21]   = get_string('numdays', '', 21);
    $options[14]   = get_string('numdays', '', 14);
    $options[7]   = get_string('numdays', '', 7);

    $maintenance['longtimenosee'] = new configvar (get_string('configlongtimenosee', 'admin'),
        choose_from_menu ($options, 'longtimenosee', $config->longtimenosee, '', '', '', true) );

/// deleteunconfirmed
    unset($options);
    $options[0]    = get_string('never');
    $options[168]  = get_string('numdays', '', 7);
    $options[144]  = get_string('numdays', '', 6);
    $options[120]  = get_string('numdays', '', 5);
    $options[96]   = get_string('numdays', '', 4);
    $options[72]   = get_string('numdays', '', 3);
    $options[48]   = get_string('numdays', '', 2);
    $options[24]   = get_string('numdays', '', 1);
    $options[12]   = get_string('numhours', '', 12);
    $options[6]    = get_string('numhours', '', 6);
    $options[1]    = get_string('numhours', '', 1);

    $maintenance['deleteunconfirmed'] = new configvar (get_string('configdeleteunconfirmed', 'admin'),
        choose_from_menu ($options, 'deleteunconfirmed', $config->deleteunconfirmed, '', '', '', true) );

/// loglifetime
    unset($options);
    $options[0]    = get_string('neverdeletelogs');
    $options[1000] = get_string('numdays', '', 1000);
    $options[365]  = get_string('numdays', '', 365);
    $options[180]  = get_string('numdays', '', 180);
    $options[150]  = get_string('numdays', '', 150);
    $options[120]  = get_string('numdays', '', 120);
    $options[90]   = get_string('numdays', '', 90);
    $options[60]   = get_string('numdays', '', 60);
    $options[30]   = get_string('numdays', '', 30);

    $maintenance['loglifetime'] = new configvar (get_string('configloglifetime', 'admin'),
        choose_from_menu ($options, 'loglifetime', $config->loglifetime, '', '', '', true) );


////////////////////////////////////////////////////////////////////
/// MAIL config variables
////////////////////////////////////////////////////////////////////
    $mail = array();

/// smtphosts
    $mail['smtphosts'] = new configvar (get_string('configsmtphosts', 'admin'),
        '<input name="smtphosts" type="text" size="30" value="'.s($config->smtphosts).'" alt="smtphosts" />' );

/// smtpuser
    $mail['smtpuser'] = new configvar (get_string('configsmtpuser', 'admin'),
        '<input name="smtpuser" type="text" size="10" value="'.s($config->smtpuser).'" alt="smtpuser" />' );

/// smtppass
    $mail['smtppass'] = new configvar ('',
        '<input name="smtppass" type="password" size="10" value="'.s($config->smtppass).'" alt="smtppass" />' );

/// noreplyaddress
    $mail['noreplyaddress'] = new configvar (get_string('confignoreplyaddress', 'admin'),
        '<input name="noreplyaddress" type="text" size="30" value="'.s($config->noreplyaddress).'" alt="noreplyaddress" />' );

/// digestmailtime
    $hours = array();
    for ($i=0; $i<=23; $i++) {
        $hours[$i] = sprintf("%02d",$i);
    }

    $mail['digestmailtime'] = new configvar (get_string('configdigestmailtime', 'admin'),
        choose_from_menu($hours, 'digestmailtime', $config->digestmailtime, '', '', 0, true) );

/// allowemailaddresses
    $mail['allowemailaddresses'] = new configvar (get_string('configallowemailaddresses', 'admin'),
        '<input name="allowemailaddresses" type="text" size="60" value="'.s($config->allowemailaddresses).'" alt="allowemailaddresses" />' );

/// denyemailaddresses
    $mail['denyemailaddresses'] = new configvar (get_string('configdenyemailaddresses', 'admin'),
        '<input name="denyemailaddresses" type="text" size="60" value="'.s($config->denyemailaddresses).'" alt="denyemailaddresses" />' );

/// enable stats
    $stats['enablestats'] = new configvar (get_string('configenablestats','admin'),
        choose_from_menu($noyesoptions, 'enablestats', $config->enablestats, '', '', '', true) );

    unset($options);
    $options['none'] = get_string('none');
    $options[60*60*24*7] = get_string('numweeks','moodle',1);
    $options[60*60*24*14] = get_string('numweeks','moodle',2);
    $options[60*60*24*21] = get_string('numweeks','moodle',3);
    $options[60*60*24*28] = get_string('nummonths','moodle',1);
    $options[60*60*24*56] = get_string('nummonths','moodle',2);
    $options[60*60*24*84] = get_string('nummonths','moodle',3);
    $options[60*60*24*112] = get_string('nummonths','moodle',4);
    $options[60*60*24*140] = get_string('nummonths','moodle',5);
    $options[60*60*24*168] = get_string('nummonths','moodle',6);
    $options['all'] = get_string('all');
    
    $stats['statsfirstrun'] = new configvar (get_string('configstatsfirstrun','admin'),
       choose_from_menu($options,'statsfirstrun',$config->statsfirstrun,'','','',true) );

    unset($options);
    $options[0] = get_string('untilcomplete');
    $options[60*60] = '1 '.get_string('hour');
    $options[60*60*2] = '2 '.get_string('hours');
    $options[60*60*3] = '3 '.get_string('hours');
    $options[60*60*4] = '4 '.get_string('hours');
    $options[60*60*5] = '5 '.get_string('hours');
    $options[60*60*6] = '6 '.get_string('hours');
    $options[60*60*7] = '7 '.get_string('hours');
    $options[60*60*8] = '8 '.get_string('hours');

    if (empty($config->statsruntimestarthour)) {
        $config->statsruntimestarthour = 0;
    }
    if (empty($config->statsruntimestartminute)) {
        $config->statsruntimestartminute = 0;
    }

    $stats['statsmaxruntime'] = new configvar (get_string('configstatsmaxruntime','admin'),
      choose_from_menu($options,'statsmaxruntime',$config->statsmaxruntime,'','','',true) );                                        

    $stats['statsruntimestart'] = new configvar (get_string('configstatsruntimestart','admin'),
      print_time_selector("statsruntimestarthour","statsruntimestartminute",make_timestamp(2000,1,1,$config->statsruntimestarthour,$config->statsruntimestartminute),5,true) );



////////////////////////////////////////////////////////////////////

    $configvars['interface']       = $interface;
    $configvars['security']        = $security;
    $configvars['operatingsystem'] = $operatingsystem;
    $configvars['maintenance']     = $maintenance;
    $configvars['mail']            = $mail;
    $configvars['user']            = $user;
    $configvars['permissions']     = $permissions;
    $configvars['requestedcourse'] = $reqcourse;
    $configvars['misc']            = $misc;
    $configvars['stats']           = $stats;

?>
