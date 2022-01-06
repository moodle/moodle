<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Methods for the Socket driver used for a CATENATE command.
 *
 * NOTE: This class is NOT intended to be accessed outside of a Base object.
 * There is NO guarantees that the API of this class will not change across
 * versions.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @internal
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Socket_Catenate
{
    /**
     * Socket object.
     *
     * @var Horde_Imap_Client_Socket
     */
    protected $_socket;

    /**
     * Constructor.
     *
     * @param Horde_Imap_Client_Socket $socket  Socket object.
     */
    public function __construct(Horde_Imap_Client_Socket $socket)
    {
        $this->_socket = $socket;
    }

    /**
     * Given an IMAP URL, fetches the corresponding part.
     *
     * @param Horde_Imap_Client_Url_Imap $url  An IMAP URL.
     *
     * @return resource  The section contents in a stream. Returns null if
     *                   the part could not be found.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function fetchFromUrl(Horde_Imap_Client_Url_Imap $url)
    {
        $ids_ob = $this->_socket->getIdsOb($url->uid);

        // BODY[]
        if (is_null($url->section)) {
            $query = new Horde_Imap_Client_Fetch_Query();
            $query->fullText(array(
                'peek' => true
            ));

            $fetch = $this->_socket->fetch($url->mailbox, $query, array(
                'ids' => $ids_ob
            ));
            return $fetch[$url->uid]->getFullMsg(true);
        }

        $section = trim($url->section);

        // BODY[<#.>HEADER.FIELDS<.NOT>()]
        if (($pos = stripos($section, 'HEADER.FIELDS')) !== false) {
            $hdr_pos = strpos($section, '(');
            $cmd = substr($section, 0, $hdr_pos);

            $query = new Horde_Imap_Client_Fetch_Query();
            $query->headers(
                'section',
                explode(' ', substr($section, $hdr_pos + 1, strrpos($section, ')') - $hdr_pos)),
                array(
                    'id' => ($pos ? substr($section, 0, $pos - 1) : 0),
                    'notsearch' => (stripos($cmd, '.NOT') !== false),
                    'peek' => true
                )
            );

            $fetch = $this->_socket->fetch($url->mailbox, $query, array(
                'ids' => $ids_ob
            ));
            return $fetch[$url->uid]->getHeaders('section', Horde_Imap_Client_Data_Fetch::HEADER_STREAM);
        }

        // BODY[#]
        if (is_numeric(substr($section, -1))) {
            $query = new Horde_Imap_Client_Fetch_Query();
            $query->bodyPart($section, array(
                'peek' => true
            ));

            $fetch = $this->_socket->fetch($url->mailbox, $query, array(
                'ids' => $ids_ob
            ));
            return $fetch[$url->uid]->getBodyPart($section, true);
        }

        // BODY[<#.>HEADER]
        if (($pos = stripos($section, 'HEADER')) !== false) {
            $id = $pos
                ? substr($section, 0, $pos - 1)
                : 0;

            $query = new Horde_Imap_Client_Fetch_Query();
            $query->headerText(array(
                'id' => $id,
                'peek' => true
            ));

            $fetch = $this->_socket->fetch($url->mailbox, $query, array(
                'ids' => $ids_ob
            ));
            return $fetch[$url->uid]->getHeaderText($id, Horde_Imap_Client_Data_Fetch::HEADER_STREAM);
        }

        // BODY[<#.>TEXT]
        if (($pos = stripos($section, 'TEXT')) !== false) {
            $id = $pos
                ? substr($section, 0, $pos - 1)
                : 0;

            $query = new Horde_Imap_Client_Fetch_Query();
            $query->bodyText(array(
                'id' => $id,
                'peek' => true
            ));

            $fetch = $this->_socket->fetch($url->mailbox, $query, array(
                'ids' => $ids_ob
            ));
            return $fetch[$url->uid]->getBodyText($id, true);
        }

        // BODY[<#.>MIMEHEADER]
        if (($pos = stripos($section, 'MIME')) !== false) {
            $id = $pos
                ? substr($section, 0, $pos - 1)
                : 0;

            $query = new Horde_Imap_Client_Fetch_Query();
            $query->mimeHeader($id, array(
                'peek' => true
            ));

            $fetch = $this->_socket->fetch($url->mailbox, $query, array(
                'ids' => $ids_ob
            ));
            return $fetch[$url->uid]->getMimeHeader($id, Horde_Imap_Client_Data_Fetch::HEADER_STREAM);
        }

        return null;
    }

}
