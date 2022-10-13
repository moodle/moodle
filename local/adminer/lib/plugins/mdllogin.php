<?php
defined('MOODLE_INTERNAL') || die();

/** Enable login for password-less database
* @link https://www.adminer.org/plugins/#use
* @author Jakub Vrana, https://www.vrana.cz/
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerMdlLogin {
	/** @access protected */
	var $password_hash;
    private $mdlcfg;

    public function __construct() {
        global $CFG;
        $this->mdlcfg = $CFG;
    }

    public function credentials() {
        if(!empty($this->mdlcfg->dboptions['dbport'])) {
            return array($this->mdlcfg->dbhost.':'.$this->mdlcfg->dboptions['dbport'],
                         $this->mdlcfg->dbuser,
                         $this->mdlcfg->dbpass);
        } else {
            return array($this->mdlcfg->dbhost, $this->mdlcfg->dbuser, $this->mdlcfg->dbpass);
        }
    }

    public function loginForm() {
        $msg = get_string('pagenotusedinmoodle', 'local_adminer');

        $redirecturl = new \moodle_url('/local/adminer');

        echo '<div><h3>'.s($msg).'</h3><a target="_parent" href="'.$redirecturl.'">'.get_string('back').'</a></div>';
        page_footer("auth");
        die();
    }

	function login($login, $password) {
        return true;
    }
}
