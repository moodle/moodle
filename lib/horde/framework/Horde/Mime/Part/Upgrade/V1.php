<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Upgrade V1 of MIME serialized data to V2.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.8.0
 */
class Horde_Mime_Part_Upgrade_V1
{
    /**
     * Converted data.
     *
     * @var array
     */
    public $data = null;

    /**
     * Constructor.
     *
     * @param array $data  V1 data.
     */
    public function __construct($data)
    {
        // Version number
        array_shift($data);

        $d = array();

        $type = array_shift($data);
        $subtype = array_shift($data);

        $ct = Horde_Mime_Headers_ContentParam_ContentType::create();
        $d[4] = new Horde_Mime_Headers();
        $d[4]->addHeaderOb($ct);
        $ct->setContentParamValue($type . '/' . $subtype);

        $d[9] = array_shift($data);

        if ($lang = array_shift($data)) {
            $d[4]->addHeaderOb(
                new Horde_Mime_Headers_ContentLanguage('', $lang)
            );
        }

        if ($cd = array_shift($data)) {
            $hdr = new Horde_Mime_Headers_ContentDescription(null, '');
            $d[4]->addHeaderOb($hdr);
            $hdr->setValue($cd);
        }

        $cd = new Horde_Mime_Headers_ContentParam_ContentDisposition(null, '');
        $d[4]->addHeaderOb($cd);
        $cd->setContentParamValue(array_shift($data));

        foreach (array_shift($data) as $key => $val) {
            $cd[$key] = $val;
        }

        foreach (array_shift($data) as $key => $val) {
            $ct[$key] = $val;
        }

        $d[7] = array_shift($data);
        $d[6] = array_shift($data);
        $d[2] = array_shift($data);
        $d[5] = array_shift($data);

        if ($boundary = array_shift($data)) {
            $ct['boundary'] = $boundary;
        }

        $d[1] = array_shift($data);

        if ($cid = array_shift($data)) {
            $hdr = new Horde_Mime_Headers_ContentId(null, $cid);
            $d[4]->addHeaderOb($hdr);
        }

        if ($cd = array_shift($data)) {
            $hdr = new Horde_Mime_Headers_Element_Single('Content-Duration', '');
            $d[4]->addHeaderOb($hdr);
            $hdr->setValue($cd);
        }

        $d[8] = 0;
        if (array_shift($data)) {
            $d[8] |= STATUS_REINDEX;
        }
        if (array_shift($data)) {
            $d[8] |= STATUS_BASEPART;
        }

        $d[3] = array_shift($data);

        if (count($data)) {
            $d[10] = reset($data);
        }

        $this->data = $d;
    }

}
