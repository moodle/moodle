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

/**
* @package    backup-convert
* @subpackage cc-library
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once 'cc_version1.php';


/**
 * Version 1.1 class of Common Cartridge
 *
 */
class cc_version11 extends cc_version1 {
    const webcontent         = 'webcontent';
    const questionbank       = 'imsqti_xmlv1p2/imscc_xmlv1p1/question-bank';
    const assessment         = 'imsqti_xmlv1p2/imscc_xmlv1p1/assessment';
    const associatedcontent  = 'associatedcontent/imscc_xmlv1p1/learning-application-resource';
    const discussiontopic    = 'imsdt_xmlv1p1';
    const weblink            = 'imswl_xmlv1p1';
    const basiclti           = 'imsbasiclti_xmlv1p0';

    public static $checker = array(self::webcontent,
                                   self::assessment,
                                   self::associatedcontent,
                                   self::discussiontopic,
                                   self::questionbank,
                                   self::weblink,
                                   self::basiclti);

    /**
    * Validate if the type are valid or not
    *
    * @param string $type
    * @return bool
    */
    public function valid($type) {
        return in_array($type, self::$checker);
    }

    public function __construct() {
        $this->ccnamespaces = array('imscc'    => 'http://www.imsglobal.org/xsd/imsccv1p1/imscp_v1p1',
                                    'lomimscc' => 'http://ltsc.ieee.org/xsd/imsccv1p1/LOM/manifest'  ,
                                    'lom'      => 'http://ltsc.ieee.org/xsd/imsccv1p1/LOM/resource'  ,
                                    'xsi'      => 'http://www.w3.org/2001/XMLSchema-instance'
                                   );

        $this->ccnsnames    = array('imscc'    => 'http://www.imsglobal.org/profile/cc/ccv1p1/ccv1p1_imscp_v1p2_v1p0.xsd'     ,
                                    'lomimscc' => 'http://www.imsglobal.org/profile/cc/ccv1p1/LOM/ccv1p1_lommanifest_v1p0.xsd',
                                    'lom'      => 'http://www.imsglobal.org/profile/cc/ccv1p1/LOM/ccv1p1_lomresource_v1p0.xsd'
                                   );

        $this->ccversion    = '1.1.0';
        $this->camversion   = '1.1.0';
        $this->_generator   = 'Moodle 2 Common Cartridge generator';
    }

    protected function update_items($items, DOMDocument &$doc, DOMElement &$xmlnode) {
        foreach ($items as $key => $item) {
            $itemnode = $doc->createElementNS($this->ccnamespaces['imscc'], 'item');
            $this->update_attribute($doc, 'identifier'   , $key                , $itemnode);
            $this->update_attribute($doc, 'identifierref', $item->identifierref, $itemnode);
            if (!is_null($item->title)) {
                $titlenode = $doc->createElementNS($this->ccnamespaces['imscc'],
                                                       'title',
                $item->title);
                $itemnode->appendChild($titlenode);
            }
            if ($item->has_child_items()) {
                $this->update_items($item->childitems, $doc, $itemnode);
            }
            $xmlnode->appendChild($itemnode);
        }
    }

}
