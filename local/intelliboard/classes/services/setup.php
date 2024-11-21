<?php

namespace local_intelliboard\services;

use local_intelliboard\helpers\DBHelper;

class setup
{
    public function protocol_handler($enablerest, $enablesoap)
    {
        global $CFG;

        $protocolsdata = ["rest" => $enablerest, "soap" => $enablesoap];
        $available_webservices = \core_component::get_plugin_list('webservice');
        $active_webservices = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);

        foreach ($active_webservices as $key => $active) {
            if (empty($available_webservices[$active])) {
                unset($active_webservices[$key]);
            }
        }

        foreach ($protocolsdata as $protocol => $enable) {
            if (!$enable && in_array($protocol, $active_webservices)) {
                $key = array_search($protocol, $active_webservices);
                unset($active_webservices[$key]);
            } elseif ($enable && !in_array($protocol, $active_webservices)) {
                if (!in_array($protocol, $active_webservices)) {
                    $active_webservices[] = $protocol;
                    $active_webservices = array_unique($active_webservices);
                }
            }
        }

        set_config('webserviceprotocols', implode(',', $active_webservices));
    }

    public function webservices_handler($enablewebservices)
    {
        if ($enablewebservices) {
            set_config('enablewebservices', 1);
        } else {
            set_config('enablewebservices', 0);
        }
    }

    /**
     * @param $useridentifier
     * @return int User ID | 0
     * @throws \dml_exception
     */
    public function webservice_users_handler($useridentifier, $serviceid) {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/webservice/lib.php');

        if (!$useridentifier) {
            return 0;
        }

        $typecast = DBHelper::get_typecast("text");
        $user = $DB->get_record_sql(
            "SELECT * 
               FROM {user}
              WHERE id{$typecast} = ? OR username = ?",
            [$useridentifier, $useridentifier]
        );

        if (!$user) {
            return 0;
        }

        $webservicemanager = new \webservice();
        $serviceuser = new \stdClass();
        $serviceuser->externalserviceid = $serviceid;
        $serviceuser->userid = $user->id;
        $webservicemanager->add_ws_authorised_user($serviceuser);

        return $user->id;
    }

    public function intelliboard_plugin_handler($data)
    {
        if (isset($data["enable_tracking"])) {
            $status = $data["enable_tracking"] ? 1 : 0;
            set_config("enabled", $status, "local_intelliboard");
        }

        if (isset($data["enable_sso_link"])) {
            $status = $data["enable_sso_link"] ? 1 : 0;
            set_config("sso", $status, "local_intelliboard");
        }
    }

    /**
     * @param $serviceid
     * @param $userid
     * @return string Generated token
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function webservice_token_handler($serviceid, $userid)
    {
        global $CFG;

        require_once($CFG->libdir . "/externallib.php");

        return external_generate_token(EXTERNAL_TOKEN_PERMANENT, $serviceid, $userid, \context_system::instance());
    }
}