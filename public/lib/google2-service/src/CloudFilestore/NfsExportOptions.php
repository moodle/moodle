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

namespace Google\Service\CloudFilestore;

class NfsExportOptions extends \Google\Collection
{
  /**
   * AccessMode not set.
   */
  public const ACCESS_MODE_ACCESS_MODE_UNSPECIFIED = 'ACCESS_MODE_UNSPECIFIED';
  /**
   * The client can only read the file share.
   */
  public const ACCESS_MODE_READ_ONLY = 'READ_ONLY';
  /**
   * The client can read and write the file share (default).
   */
  public const ACCESS_MODE_READ_WRITE = 'READ_WRITE';
  /**
   * SquashMode not set.
   */
  public const SQUASH_MODE_SQUASH_MODE_UNSPECIFIED = 'SQUASH_MODE_UNSPECIFIED';
  /**
   * The Root user has root access to the file share (default).
   */
  public const SQUASH_MODE_NO_ROOT_SQUASH = 'NO_ROOT_SQUASH';
  /**
   * The Root user has squashed access to the anonymous uid/gid.
   */
  public const SQUASH_MODE_ROOT_SQUASH = 'ROOT_SQUASH';
  protected $collection_key = 'ipRanges';
  /**
   * Either READ_ONLY, for allowing only read requests on the exported
   * directory, or READ_WRITE, for allowing both read and write requests. The
   * default is READ_WRITE.
   *
   * @var string
   */
  public $accessMode;
  /**
   * An integer representing the anonymous group id with a default value of
   * 65534. Anon_gid may only be set with squash_mode of ROOT_SQUASH. An error
   * will be returned if this field is specified for other squash_mode settings.
   *
   * @var string
   */
  public $anonGid;
  /**
   * An integer representing the anonymous user id with a default value of
   * 65534. Anon_uid may only be set with squash_mode of ROOT_SQUASH. An error
   * will be returned if this field is specified for other squash_mode settings.
   *
   * @var string
   */
  public $anonUid;
  /**
   * List of either an IPv4 addresses in the format
   * `{octet1}.{octet2}.{octet3}.{octet4}` or CIDR ranges in the format
   * `{octet1}.{octet2}.{octet3}.{octet4}/{mask size}` which may mount the file
   * share. Overlapping IP ranges are not allowed, both within and across
   * NfsExportOptions. An error will be returned. The limit is 64 IP
   * ranges/addresses for each FileShareConfig among all NfsExportOptions.
   *
   * @var string[]
   */
  public $ipRanges;
  /**
   * Optional. The source VPC network for ip_ranges. Required for instances
   * using Private Service Connect, optional otherwise. If provided, must be the
   * same network specified in the `NetworkConfig.network` field.
   *
   * @var string
   */
  public $network;
  /**
   * Either NO_ROOT_SQUASH, for allowing root access on the exported directory,
   * or ROOT_SQUASH, for not allowing root access. The default is
   * NO_ROOT_SQUASH.
   *
   * @var string
   */
  public $squashMode;

  /**
   * Either READ_ONLY, for allowing only read requests on the exported
   * directory, or READ_WRITE, for allowing both read and write requests. The
   * default is READ_WRITE.
   *
   * Accepted values: ACCESS_MODE_UNSPECIFIED, READ_ONLY, READ_WRITE
   *
   * @param self::ACCESS_MODE_* $accessMode
   */
  public function setAccessMode($accessMode)
  {
    $this->accessMode = $accessMode;
  }
  /**
   * @return self::ACCESS_MODE_*
   */
  public function getAccessMode()
  {
    return $this->accessMode;
  }
  /**
   * An integer representing the anonymous group id with a default value of
   * 65534. Anon_gid may only be set with squash_mode of ROOT_SQUASH. An error
   * will be returned if this field is specified for other squash_mode settings.
   *
   * @param string $anonGid
   */
  public function setAnonGid($anonGid)
  {
    $this->anonGid = $anonGid;
  }
  /**
   * @return string
   */
  public function getAnonGid()
  {
    return $this->anonGid;
  }
  /**
   * An integer representing the anonymous user id with a default value of
   * 65534. Anon_uid may only be set with squash_mode of ROOT_SQUASH. An error
   * will be returned if this field is specified for other squash_mode settings.
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
   * List of either an IPv4 addresses in the format
   * `{octet1}.{octet2}.{octet3}.{octet4}` or CIDR ranges in the format
   * `{octet1}.{octet2}.{octet3}.{octet4}/{mask size}` which may mount the file
   * share. Overlapping IP ranges are not allowed, both within and across
   * NfsExportOptions. An error will be returned. The limit is 64 IP
   * ranges/addresses for each FileShareConfig among all NfsExportOptions.
   *
   * @param string[] $ipRanges
   */
  public function setIpRanges($ipRanges)
  {
    $this->ipRanges = $ipRanges;
  }
  /**
   * @return string[]
   */
  public function getIpRanges()
  {
    return $this->ipRanges;
  }
  /**
   * Optional. The source VPC network for ip_ranges. Required for instances
   * using Private Service Connect, optional otherwise. If provided, must be the
   * same network specified in the `NetworkConfig.network` field.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Either NO_ROOT_SQUASH, for allowing root access on the exported directory,
   * or ROOT_SQUASH, for not allowing root access. The default is
   * NO_ROOT_SQUASH.
   *
   * Accepted values: SQUASH_MODE_UNSPECIFIED, NO_ROOT_SQUASH, ROOT_SQUASH
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
class_alias(NfsExportOptions::class, 'Google_Service_CloudFilestore_NfsExportOptions');
