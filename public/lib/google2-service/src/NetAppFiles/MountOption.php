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

class MountOption extends \Google\Model
{
  /**
   * Unspecified protocol
   */
  public const PROTOCOL_PROTOCOLS_UNSPECIFIED = 'PROTOCOLS_UNSPECIFIED';
  /**
   * NFS V3 protocol
   */
  public const PROTOCOL_NFSV3 = 'NFSV3';
  /**
   * NFS V4 protocol
   */
  public const PROTOCOL_NFSV4 = 'NFSV4';
  /**
   * SMB protocol
   */
  public const PROTOCOL_SMB = 'SMB';
  /**
   * ISCSI protocol
   */
  public const PROTOCOL_ISCSI = 'ISCSI';
  /**
   * Export string
   *
   * @var string
   */
  public $export;
  /**
   * Full export string
   *
   * @var string
   */
  public $exportFull;
  /**
   * Instructions for mounting
   *
   * @var string
   */
  public $instructions;
  /**
   * Output only. IP Address.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Protocol to mount with.
   *
   * @var string
   */
  public $protocol;

  /**
   * Export string
   *
   * @param string $export
   */
  public function setExport($export)
  {
    $this->export = $export;
  }
  /**
   * @return string
   */
  public function getExport()
  {
    return $this->export;
  }
  /**
   * Full export string
   *
   * @param string $exportFull
   */
  public function setExportFull($exportFull)
  {
    $this->exportFull = $exportFull;
  }
  /**
   * @return string
   */
  public function getExportFull()
  {
    return $this->exportFull;
  }
  /**
   * Instructions for mounting
   *
   * @param string $instructions
   */
  public function setInstructions($instructions)
  {
    $this->instructions = $instructions;
  }
  /**
   * @return string
   */
  public function getInstructions()
  {
    return $this->instructions;
  }
  /**
   * Output only. IP Address.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Protocol to mount with.
   *
   * Accepted values: PROTOCOLS_UNSPECIFIED, NFSV3, NFSV4, SMB, ISCSI
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MountOption::class, 'Google_Service_NetAppFiles_MountOption');
