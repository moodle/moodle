<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
namespace auth_iomadsaml2\admin;

use admin_setting_configtextarea;
use auth_iomadsaml2\idp_data;
use auth_iomadsaml2\idp_parser;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/adminlib.php");

/**
 * Class admin_setting_configtext_idpmetadata
 *
 * @package     auth_iomadsaml2
 * @copyright   Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_idpmetadata extends admin_setting_configtextarea {
    /**
     * Constructor
     */
    public function __construct($postfix = "") {

        // IOMAD set the postfix.
        $this->postfix = $postfix;
        $this->companyid = 0;
        if ("" != $postfix) {
            list ($drop, $companyid) = explode('_', $postfix);
            $this->companyid = $companyid;
        }

        // All parameters are hardcoded because there can be only one instance:
        // When it validates, it saves extra configs, preventing this component from being reused as is.
        parent::__construct(
            'auth_iomadsaml2/idpmetadata' . $postfix,
            get_string('idpmetadata', 'auth_iomadsaml2'),
            get_string('idpmetadata_help', 'auth_iomadsaml2'),
            '',
            PARAM_RAW,
            80,
            5);
    }

    /**
     * Validate data before storage
     *
     * @param string $value
     * @return true|string Error message in case of error, true otherwise.
     * @throws \coding_exception
     */
    public function validate($value) {
        $value = trim($value);
        if (empty($value)) {
            return true;
        }

        try {
            $idps = $this->get_idps_data($value);
            $this->process_all_idps_metadata($idps);
        } catch (setting_idpmetadata_exception $exception) {
            return $exception->getMessage();
        }

        return true;
    }

    /**
     * Process all idps metadata.
     *
     * @param idp_data[] $idps
     */
    private function process_all_idps_metadata($idps) {
        global $DB;

        $currentidpsrs = $DB->get_records('auth_iomadsaml2_idps', ['companyid' => $this->companyid]);
        $oldidps = array();
        foreach ($currentidpsrs as $idpentity) {
            if (!isset($oldidps[$idpentity->metadataurl])) {
                $oldidps[$idpentity->metadataurl] = array();
            }

            $oldidps[$idpentity->metadataurl][$idpentity->entityid] = $idpentity;
        }

        foreach ($idps as $idp) {
            $this->process_idp_metadata($idp, $oldidps);
        }

        // We remove any old IdPs that are left over.
        $this->remove_old_idps($oldidps);
    }

    /**
     * Process idp metadata.
     *
     * @param idp_data $idp
     * @param mixed $oldidps
     * @throws setting_idpmetadata_exception
     */
    private function process_idp_metadata(idp_data $idp, &$oldidps) {
        $xpath = $this->get_idp_xml_path($idp);
        $idpelements = $this->find_all_idp_sso_descriptors($xpath);

        if ($idpelements->length == 1) {
            $this->process_idp_xml($idp, $idpelements->item(0), $xpath, $oldidps, 1);
        } else if ($idpelements->length > 1) {
            foreach ($idpelements as $childidpelements) {
                $this->process_idp_xml($idp, $childidpelements, $xpath, $oldidps, 0);
            }
        }

        $this->save_idp_metadata_xml($idp->idpurl, $idp->get_rawxml());
    }

    /**
     * Process idp metadata.
     *
     * @param idp_data $idp
     * @param DOMElement $idpelements
     * @param DOMXPath $xpath
     * @param mixed $oldidps
     * @param int $activedefault
     */
    private function process_idp_xml(idp_data $idp, DOMElement $idpelements, DOMXPath $xpath,
                                        &$oldidps, $activedefault = 0) {
        global $DB;
        $entityid = $idpelements->getAttribute('entityID');

        // Locate a displayname element provided by the IdP XML metadata.
        $names = $xpath->query('.//mdui:DisplayName', $idpelements);
        $idpname = null;
        if ($names && $names->length > 0) {
            $idpname = $names->item(0)->textContent;
        } else if (!empty($idp->idpname)) {
            $idpname = $idp->idpname;
        } else {
            $idpname = get_string('idpnamedefault', 'auth_iomadsaml2');
        }

        // Locate a logo element provided by the IdP XML metadata.
        $logos = $xpath->query('.//mdui:Logo', $idpelements);
        $logo = null;
        if ($logos && $logos->length > 0) {
            $logo = $logos->item(0)->textContent;
        }

        if (isset($oldidps[$idp->idpurl][$entityid])) {
            $oldidp = $oldidps[$idp->idpurl][$entityid];

            if (!empty($idpname) && $oldidp->defaultname !== $idpname) {
                $DB->set_field('auth_iomadsaml2_idps', 'defaultname', $idpname, array('id' => $oldidp->id));
            }

            if (!empty($logo) && $oldidp->logo !== $logo) {
                $DB->set_field('auth_iomadsaml2_idps', 'logo', $logo, array('id' => $oldidp->id));
            }

            // Remove the idp from the current array so that we don't delete it later.
            unset($oldidps[$idp->idpurl][$entityid]);
        } else {
            $newidp = new \stdClass();
            $newidp->metadataurl = $idp->idpurl;
            $newidp->entityid = $entityid;
            $newidp->activeidp = $activedefault;
            $newidp->defaultidp = 0;
            $newidp->adminidp = 0;
            $newidp->defaultname = $idpname;
            $newidp->logo = $logo;
            $newidp->companyid = $this->companyid;

            $DB->insert_record('auth_iomadsaml2_idps', $newidp);
        }
    }

    /**
     * Process idp metadata.
     *
     * @param mixed $oldidps
     */
    private function remove_old_idps($oldidps) {
        global $DB;

        foreach ($oldidps as $metadataidps) {
            foreach ($metadataidps as $oldidp) {
                $DB->delete_records('auth_iomadsaml2_idps', array('id' => $oldidp->id));
            }
        }
    }

    /**
     * Get idps data.
     *
     * @param string $value
     * @return idp_data[]
     */
    public function get_idps_data($value) {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');

        $parser = new idp_parser();
        $idps = $parser->parse($value);

        // Download the XML if it was not parsed from the ipdmetadata field.
        foreach ($idps as $idp) {
            if (!is_null($idp->get_rawxml())) {
                continue;
            }

            $rawxml = \download_file_content($idp->idpurl);
            if ($rawxml === false) {
                throw new setting_idpmetadata_exception(
                    get_string('idpmetadata_badurl', 'auth_iomadsaml2', $idp->idpurl)
                );
            }
            $idp->set_rawxml($rawxml);
        }

        return $idps;
    }

    /**
     * Get idp xml path.
     *
     * @param idp_data $idp
     * @return DOMXPath
     */
    private function get_idp_xml_path(idp_data $idp) {
        $xml = new DOMDocument();

        libxml_use_internal_errors(true);

        $rawxml = $idp->rawxml;

        if (!$xml->loadXML($rawxml, LIBXML_PARSEHUGE)) {
            $errors = libxml_get_errors();
            $lines = explode("\n", $rawxml);
            $msg = '';
            foreach ($errors as $error) {
                $msg .= "<br>Error ({$error->code}) line $error->line char  $error->column: $error->message";
            }

            throw new setting_idpmetadata_exception(get_string('idpmetadata_invalid', 'auth_iomadsaml2') . $msg);
        }

        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace('md', 'urn:oasis:names:tc:SAML:2.0:metadata');
        $xpath->registerNamespace('mdui', 'urn:oasis:names:tc:SAML:metadata:ui');

        return $xpath;
    }

    /**
     * Find all idp SSO descriptors.
     *
     * @param DOMXPath $xpath
     * @return DOMNodeList
     */
    private function find_all_idp_sso_descriptors(DOMXPath $xpath) {
        $idpelements = $xpath->query('//md:EntityDescriptor[//md:IDPSSODescriptor]');
        return $idpelements;
    }

    /**
     * Save idp metadata xml.
     *
     * @param string $url
     * @param string $xml
     */
    private function save_idp_metadata_xml($url, $xml) {
        global $CFG, $iomadsaml2auth;
        require_once("{$CFG->dirroot}/auth/iomadsaml2/setup.php");

        $file = $iomadsaml2auth->get_file_idp_metadata_file($url);
        file_put_contents($file, $xml);
    }
}
