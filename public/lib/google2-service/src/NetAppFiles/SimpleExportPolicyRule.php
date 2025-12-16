<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\NetAppFiles;

class SimpleExportPolicyRule extends \Google\Model
{
  /**
   * Unspecified Access Type
   */
  public const ACCESS_TYPE_ACCESS_TYPE_UNSPECIFIED = 'ACCESS_TYPE_UNSPECIFIED';
  /**
   * Read Only
   */
  public const ACCESS_TYPE_READ_ONLY = 'READ_ONLY';
  /**
   * Read Write
   */
  public const ACCESS_TYPE_READ_WRITE = 'READ_WRITE';
  /**
   * None
   */
  public const ACCESS_TYPE_READ_NONE = 'READ_NONE';
  /**
   * Defaults to `NO_ROOT_SQUASH`.
   */
  public const SQUASH_MODE_SQUASH_MODE_UNSPECIFIED = 'SQUASH_MODE_UNSPECIFIED';
  /**
   * The root user (UID 0) retains full access. Other users are unaffected.
   */
  public const SQUASH_MODE_NO_ROOT_SQUASH = 'NO_ROOT_SQUASH';
  /**
   * The root user (UID 0) is squashed to anonymous user ID. Other users are
   * unaffected.
   */
  public const SQUASH_MODE_ROOT_SQUASH = 'ROOT_SQUASH';
  /**
   * All users are squashed to anonymous user ID.
   */
  public const SQUASH_MODE_ALL_SQUASH = 'ALL_SQUASH';
  /**
   * Access type (ReadWrite, ReadOnly, None)
   *
   * @var string
   */
  public $accessType;
  /**
   * Comma separated list of allowed clients IP addresses
   *
   * @var string
   */
  public $allowedClients;
  /**
   * Optional. An integer representing the anonymous user ID. Range is 0 to
   * 4294967295. Required when `squash_mode` is `ROOT_SQUASH` or `ALL_SQUASH`.
   *
   * @var string
   */
  public $anonUid;
  /**
   * Whether Unix root access will be granted.
   *
   * @var string
   */
  public $hasRootAccess;
  /**
   * If enabled (true) the rule defines a read only access for clients matching
   * the 'allowedClients' specification. It enables nfs clients to mount using
   * 'authentication' kerberos security mode.
   *
   * @var bool
   */
  public $kerberos5ReadOnly;
  /**
   * If enabled (true) the rule defines read and write access for clients
   * matching the 'allowedClients' specification. It enables nfs clients to
   * mount using 'authentication' kerberos security mode. The
   * 'kerberos5ReadOnly' value be ignored if this is enabled.
   *
   * @var bool
   */
  public $kerberos5ReadWrite;
  /**
   * If enabled (true) the rule defines a read only access for clients matching
   * the 'allowedClients' specification. It enables nfs clients to mount using
   * 'integrity' kerberos security mode.
   *
   * @var bool
   */
  public $kerberos5iReadOnly;
  /**
   * If enabled (true) the rule defines read and write access for clients
   * matching the 'allowedClients' specification. It enables nfs clients to
   * mount using 'integrity' kerberos security mode. The 'kerberos5iReadOnly'
   * value be ignored if this is enabled.
   *
   * @var bool
   */
  public $kerberos5iReadWrite;
  /**
   * If enabled (true) the rule defines a read only access for clients matching
   * the 'allowedClients' specification. It enables nfs clients to mount using
   * 'privacy' kerberos security mode.
   *
   * @var bool
   */
  public $kerberos5pReadOnly;
  /**
   * If enabled (true) the rule defines read and write access for clients
   * matching the 'allowedClients' specification. It enables nfs clients to
   * mount using 'privacy' kerberos security mode. The 'kerberos5pReadOnly'
   * value be ignored if this is enabled.
   *
   * @var bool
   */
  public $kerberos5pReadWrite;
  /**
   * NFS V3 protocol.
   *
   * @var bool
   */
  public $nfsv3;
  /**
   * NFS V4 protocol.
   *
   * @var bool
   */
  public $nfsv4;
  /**
   * Optional. Defines how user identity squashing is applied for this export
   * rule. This field is the preferred way to configure squashing behavior and
   * takes precedence over `has_root_access` if both are provided.
   *
   * @var string
   */
  public $squashMode;

  /**
   * Access type (ReadWrite, ReadOnly, None)
   *
   * Accepted values: ACCESS_TYPE_UNSPECIFIED, READ_ONLY, READ_WRITE, READ_NONE
   *
   * @param self::ACCESS_TYPE_* $accessType
   */
  public function setAccessType($accessType)
  {
    $this->accessType = $accessType;
  }
  /**
   * @return self::ACCESS_TYPE_*
   */
  public function getAccessType()
  {
    return $this->accessType;
  }
  /**
   * Comma separated list of allowed clients IP addresses
   *
   * @param string $allowedClients
   */
  public function setAllowedClients($allowedClients)
  {
    $this->allowedClients = $allowedClients;
  }
  /**
   * @return string
   */
  public function getAllowedClients()
  {
    return $this->allowedClients;
  }
  /**
   * Optional. An integer representing the anonymous user ID. Range is 0 to
   * 4294967295. Required when `squash_mode` is `ROOT_SQUASH` or `ALL_SQUASH`.
   *
   * @param string $anonUid
   */
  public function setAnonUid($anonUid)
  {
    $this->anonUid = $anonUid;
  }
  /**
   * @return string
   */
  public function getAnonUid()
  {
    return $this->anonUid;
  }
  /**
   * Whether Unix root access will be granted.
   *
   * @param string $hasRootAccess
   */
  public function setHasRootAccess($hasRootAccess)
  {
    $this->hasRootAccess = $hasRootAccess;
  }
  /**
   * @return string
   */
  public function getHasRootAccess()
  {
    return $this->hasRootAccess;
  }
  /**
   * If enabled (true) the rule defines a read only access for clients matching
   * the 'allowedClients' specification. It enables nfs clients to mount using
   * 'authentication' kerberos security mode.
   *
   * @param bool $kerberos5ReadOnly
   */
  public function setKerberos5ReadOnly($kerberos5ReadOnly)
  {
    $this->kerberos5ReadOnly = $kerberos5ReadOnly;
  }
  /**
   * @return bool
   */
  public function getKerberos5ReadOnly()
  {
    return $this->kerberos5ReadOnly;
  }
  /**
   * If enabled (true) the rule defines read and write access for clients
   * matching the 'allowedClients' specification. It enables nfs clients to
   * mount using 'authentication' kerberos security mode. The
   * 'kerberos5ReadOnly' value be ignored if this is enabled.
   *
   * @param bool $kerberos5ReadWrite
   */
  public function setKerberos5ReadWrite($kerberos5ReadWrite)
  {
    $this->kerberos5ReadWrite = $kerberos5ReadWrite;
  }
  /**
   * @return bool
   */
  public function getKerberos5ReadWrite()
  {
    return $this->kerberos5ReadWrite;
  }
  /**
   * If enabled (true) the rule defines a read only access for clients matching
   * the 'allowedClients' specification. It enables nfs clients to mount using
   * 'integrity' kerberos security mode.
   *
   * @param bool $kerberos5iReadOnly
   */
  public function setKerberos5iReadOnly($kerberos5iReadOnly)
  {
    $this->kerberos5iReadOnly = $kerberos5iReadOnly;
  }
  /**
   * @return bool
   */
  public function getKerberos5iReadOnly()
  {
    return $this->kerberos5iReadOnly;
  }
  /**
   * If enabled (true) the rule defines read and write access for clients
   * matching the 'allowedClients' specification. It enables nfs clients to
   * mount using 'integrity' kerberos security mode. The 'kerberos5iReadOnly'
   * value be ignored if this is enabled.
   *
   * @param bool $kerberos5iReadWrite
   */
  public function setKerberos5iReadWrite($kerberos5iReadWrite)
  {
    $this->kerberos5iReadWrite = $kerberos5iReadWrite;
  }
  /**
   * @return bool
   */
  public function getKerberos5iReadWrite()
  {
    return $this->kerberos5iReadWrite;
  }
  /**
   * If enabled (true) the rule defines a read only access for clients matching
   * the 'allowedClients' specification. It enables nfs clients to mount using
   * 'privacy' kerberos security mode.
   *
   * @param bool $kerberos5pReadOnly
   */
  public function setKerberos5pReadOnly($kerberos5pReadOnly)
  {
    $this->kerberos5pReadOnly = $kerberos5pReadOnly;
  }
  /**
   * @return bool
   */
  public function getKerberos5pReadOnly()
  {
    return $this->kerberos5pReadOnly;
  }
  /**
   * If enabled (true) the rule defines read and write access for clients
   * matching the 'allowedClients' specification. It enables nfs clients to
   * mount using 'privacy' kerberos security mode. The 'kerberos5pReadOnly'
   * value be ignored if this is enabled.
   *
   * @param bool $kerberos5pReadWrite
   */
  public function setKerberos5pReadWrite($kerberos5pReadWrite)
  {
    $this->kerberos5pReadWrite = $kerberos5pReadWrite;
  }
  /**
   * @return bool
   */
  public function getKerberos5pReadWrite()
  {
    return $this->kerberos5pReadWrite;
  }
  /**
   * NFS V3 protocol.
   *
   * @param bool $nfsv3
   */
  public function setNfsv3($nfsv3)
  {
    $this->nfsv3 = $nfsv3;
  }
  /**
   * @return bool
   */
  public function getNfsv3()
  {
    return $this->nfsv3;
  }
  /**
   * NFS V4 protocol.
   *
   * @param bool $nfsv4
   */
  public function setNfsv4($nfsv4)
  {
    $this->nfsv4 = $nfsv4;
  }
  /**
   * @return bool
   */
  public function getNfsv4()
  {
    return $this->nfsv4;
  }
  /**
   * Optional. Defines how user identity squashing is applied for this export
   * rule. This field is the preferred way to configure squashing behavior and
   * takes precedence over `has_root_access` if both are provided.
   *
   * Accepted values: SQUASH_MODE_UNSPECIFIED, NO_ROOT_SQUASH, ROOT_SQUASH,
   * ALL_SQUASH
   *
   * @param self::SQUASH_MODE_* $squashMode
   */
  public function setSquashMode($squashMode)
  {
    $this->squashMode = $squashMode;
  }
  /**
   * @return self::SQUASH_MODE_*
   */
  public function getSquashMode()
  {
    return $this->squashMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SimpleExportPolicyRule::class, 'Google_Service_NetAppFiles_SimpleExportPolicyRule');
