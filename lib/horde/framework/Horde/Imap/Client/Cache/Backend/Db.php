<?php
/**
 * Copyright 2013-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * A SQL database implementation for caching IMAP/POP data.
 * Requires the Horde_Db package.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Cache_Backend_Db extends Horde_Imap_Client_Cache_Backend
{
    /** SQL table names. */
    const BASE_TABLE = 'horde_imap_client_data';
    const MD_TABLE = 'horde_imap_client_metadata';
    const MSG_TABLE = 'horde_imap_client_message';

    /**
     * Handle for the database connection.
     *
     * @var Horde_Db_Adapter
     */
    protected $_db;

    /**
     * Constructor.
     *
     * @param array $params  Configuration parameters:
     * <pre>
     *   - REQUIRED Parameters:
     *     - db: (Horde_Db_Adapter) DB object.
     * </pre>
     */
    public function __construct(array $params = array())
    {
        if (!isset($params['db'])) {
            throw new InvalidArgumentException('Missing db parameter.');
        }

        parent::__construct($params);
    }

    /**
     */
    protected function _initOb()
    {
        $this->_db = $this->_params['db'];
    }

    /**
     */
    public function get($mailbox, $uids, $fields, $uidvalid)
    {
        $this->getMetaData($mailbox, $uidvalid, array('uidvalid'));

        $query = $this->_baseSql($mailbox, self::MSG_TABLE);
        $query[0] = 'SELECT t.data, t.msguid ' . $query[0];

        $uid_query = array();
        foreach ($uids as $val) {
            $uid_query[] = 't.msguid = ?';
            $query[1][] = strval($val);
        }
        $query[0] .= ' AND (' . implode(' OR ', $uid_query) . ')';

        $compress = new Horde_Compress_Fast();
        $out = array();

        try {
            $columns = $this->_db->columns(self::MSG_TABLE);
            $res = $this->_db->select($query[0], $query[1]);

            foreach ($res as $row) {
                $out[$row['msguid']] = @unserialize($compress->decompress(
                    $columns['data']->binaryToString($row['data'])
                ));
            }
        } catch (Horde_Db_Exception $e) {}

        return $out;
    }

    /**
     */
    public function getCachedUids($mailbox, $uidvalid)
    {
        $this->getMetaData($mailbox, $uidvalid, array('uidvalid'));

        $query = $this->_baseSql($mailbox, self::MSG_TABLE);
        $query[0] = 'SELECT DISTINCT t.msguid ' . $query[0];

        try {
            return $this->_db->selectValues($query[0], $query[1]);
        } catch (Horde_Db_Exception $e) {
            return array();
        }
    }

    /**
     */
    public function set($mailbox, $data, $uidvalid)
    {
        if ($uid = $this->_getUid($mailbox)) {
            $res = $this->get($mailbox, array_keys($data), array(), $uidvalid);
        } else {
            $res = array();
            $uid = $this->_createUid($mailbox);
        }

        $compress = new Horde_Compress_Fast();

        foreach ($data as $key => $val) {
            if (isset($res[$key])) {
                try {
                    /* Update */
                    $this->_db->update(
                        sprintf('UPDATE %s SET data = ? WHERE messageid = ? AND msguid = ?', self::MSG_TABLE),
                        array(
                            new Horde_Db_Value_Binary($compress->compress(serialize(array_merge($res[$key], $val)))),
                            $uid,
                            strval($key)
                        )
                    );
                } catch (Horde_Db_Exception $e) {}
            } else {
                /* Insert */
                try {
                    $this->_db->insert(
                        sprintf('INSERT INTO %s (data, msguid, messageid) VALUES (?, ?, ?)', self::MSG_TABLE),
                        array(
                            new Horde_Db_Value_Binary($compress->compress(serialize($val))),
                            strval($key),
                            $uid
                        )
                    );
                } catch (Horde_Db_Exception $e) {}
            }
        }

        /* Update modified time. */
        try {
            $this->_db->update(
                sprintf(
                    'UPDATE %s SET modified = ? WHERE messageid = ?',
                    self::BASE_TABLE
                ),
                array(time(), $uid)
            );
        } catch (Horde_Db_Exception $e) {}

        /* Update uidvalidity. */
        $this->setMetaData($mailbox, array('uidvalid' => $uidvalid));
    }

    /**
     */
    public function getMetaData($mailbox, $uidvalid, $entries)
    {
        $query = $this->_baseSql($mailbox, self::MD_TABLE);
        $query[0] = 'SELECT t.field, t.data ' . $query[0];

        if (!empty($entries)) {
            $entries[] = 'uidvalid';
            $entry_query = array();

            foreach (array_unique($entries) as $val) {
                $entry_query[] = 't.field = ?';
                $query[1][] = $val;
            }
            $query[0] .= ' AND (' . implode(' OR ', $entry_query) . ')';
        }

        try {
            if ($res = $this->_db->selectAssoc($query[0], $query[1])) {
                $columns = $this->_db->columns(self::MD_TABLE);
                foreach ($res as $key => $val) {
                    switch ($key) {
                    case 'uidvalid':
                        $res[$key] = $columns['data']->binaryToString($val);
                        break;

                    default:
                        $res[$key] = @unserialize(
                            $columns['data']->binaryToString($val)
                        );
                        break;
                    }
                }

                if (is_null($uidvalid) ||
                    !isset($res['uidvalid']) ||
                    ($res['uidvalid'] == $uidvalid)) {
                    return $res;
                }

                $this->deleteMailbox($mailbox);
            }
        } catch (Horde_Db_Exception $e) {}

        return array();
    }

    /**
     */
    public function setMetaData($mailbox, $data)
    {
        if (!($uid = $this->_getUid($mailbox))) {
            $uid = $this->_createUid($mailbox);
        }

        $query = sprintf('SELECT field FROM %s where messageid = ?', self::MD_TABLE);
        $values = array($uid);

        try {
            $fields = $this->_db->selectValues($query, $values);
        } catch (Horde_Db_Exception $e) {
            return;
        }

        foreach ($data as $key => $val) {
            $val = new Horde_Db_Value_Binary(($key == 'uidvalid') ? $val : serialize($val));

            if (in_array($key, $fields)) {
                /* Update */
                try {
                    $this->_db->update(
                        sprintf(
                            'UPDATE %s SET data = ? WHERE field = ? AND messageid = ?',
                            self::MD_TABLE
                        ),
                        array($val, $key, $uid)
                    );
                } catch (Horde_Db_Exception $e) {}
            } else {
                /* Insert */
                try {
                    $this->_db->insert(
                        sprintf(
                            'INSERT INTO %s (data, field, messageid) VALUES (?, ?, ?)',
                            self::MD_TABLE
                        ),
                        array($val, $key, $uid)
                    );
                } catch (Horde_Db_Exception $e) {}
            }
        }
    }

    /**
     */
    public function deleteMsgs($mailbox, $uids)
    {
        if (empty($uids)) {
            return;
        }

        $query = $this->_baseSql($mailbox);
        $query[0] = sprintf(
            'DELETE FROM %s WHERE messageid IN (SELECT messageid ' . $query[0] . ')',
            self::MSG_TABLE
        );

        $uid_query = array();
        foreach ($uids as $val) {
            $uid_query[] = 'msguid = ?';
            $query[1][] = strval($val);
        }
        $query[0] .= ' AND (' . implode(' OR ', $uid_query) . ')';

        try {
            $this->_db->delete($query[0], $query[1]);
        } catch (Horde_Db_Exception $e) {}
    }

    /**
     */
    public function deleteMailbox($mailbox)
    {
        if (is_null($uid = $this->_getUid($mailbox))) {
            return;
        }

        foreach (array(self::BASE_TABLE, self::MD_TABLE, self::MSG_TABLE) as $val) {
            try {
                $this->_db->delete(
                    sprintf('DELETE FROM %s WHERE messageid = ?', $val),
                    array($uid)
                );
            } catch (Horde_Db_Exception $e) {}
        }
    }

    /**
     */
    public function clear($lifetime)
    {
        if (is_null($lifetime)) {
            try {
                $this->_db->delete(sprintf('DELETE FROM %s', self::BASE_TABLE));
                $this->_db->delete(sprintf('DELETE FROM %s', self::MD_TABLE));
                $this->_db->delete(sprintf('DELETE FROM %s', self::MSG_TABLE));
            } catch (Horde_Db_Exception $e) {}
            return;
        }

        $purge = time() - $lifetime;
        $sql = 'DELETE FROM %s WHERE messageid IN (SELECT messageid FROM %s WHERE modified < ?';

        foreach (array(self::MD_TABLE, self::MSG_TABLE) as $val) {
            try {
                $this->_db->delete(
                    sprintf($sql, $val, self::BASE_TABLE),
                    array($purge)
                );
            } catch (Horde_Db_Exception $e) {}
        }

        try {
            $this->_db->delete(
                sprintf('DELETE FROM %s WHERE modified < ?', self::BASE_TABLE),
                array($purge)
            );
        } catch (Horde_Db_Exception $e) {}
    }

    /**
     * Prepare the base SQL query.
     *
     * @param string $mailbox  The mailbox.
     * @param string $join     The table to join with the base table.
     *
     * @return array  SQL query and bound parameters.
     */
    protected function _baseSql($mailbox, $join = null)
    {
        $sql = sprintf('FROM %s d', self::BASE_TABLE);

        if (!is_null($join)) {
            $sql .= sprintf(' INNER JOIN %s t ON d.messageid = t.messageid', $join);
        }

        return array(
            $sql . ' WHERE d.hostspec = ? AND d.port = ? AND d.username = ? AND d.mailbox = ?',
            array(
                $this->_params['hostspec'],
                $this->_params['port'],
                $this->_params['username'],
                $mailbox
            )
        );
    }

    /**
     * @param string $mailbox
     *
     * @return string  UID from base table.
     */
    protected function _getUid($mailbox)
    {
        $query = $this->_baseSql($mailbox);
        $query[0] = 'SELECT d.messageid ' . $query[0];

        try {
            return $this->_db->selectValue($query[0], $query[1]);
        } catch (Horde_Db_Exception $e) {
            return null;
        }
    }

    /**
     * @param string $mailbox
     *
     * @return string  UID from base table.
     */
    protected function _createUid($mailbox)
    {
        return $this->_db->insert(
            sprintf(
                'INSERT INTO %s (hostspec, mailbox, port, username) ' .
                    'VALUES (?, ?, ?, ?)',
                self::BASE_TABLE
            ),
            array(
                $this->_params['hostspec'],
                $mailbox,
                $this->_params['port'],
                $this->_params['username']
            )
        );
    }

}
