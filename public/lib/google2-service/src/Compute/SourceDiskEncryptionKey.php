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

namespace Google\Service\Compute;

class SourceDiskEncryptionKey extends \Google\Model
{
  protected $diskEncryptionKeyType = CustomerEncryptionKey::class;
  protected $diskEncryptionKeyDataType = '';
  /**
   * URL of the disk attached to the source instance. This can be a full or
   * valid partial URL. For example, the following are valid values:
   * - https://www.googleapis.com/compute/v1/projects/project/zones/zone/disks/d
   * isk     - projects/project/zones/zone/disks/disk     -
   * zones/zone/disks/disk
   *
   * @var string
   */
  public $sourceDisk;

  /**
   * Thecustomer-supplied encryption key of the source disk. Required if the
   * source disk is protected by a customer-supplied encryption key.
   *
   * @param CustomerEncryptionKey $diskEncryptionKey
   */
  public function setDiskEncryptionKey(CustomerEncryptionKey $diskEncryptionKey)
  {
    $this->diskEncryptionKey = $diskEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getDiskEncryptionKey()
  {
    return $this->diskEncryptionKey;
  }
  /**
   * URL of the disk attached to the source instance. This can be a full or
   * valid partial URL. For example, the following are valid values:
   * - https://www.googleapis.com/compute/v1/projects/project/zones/zone/disks/d
   * isk     - projects/project/zones/zone/disks/disk     -
   * zones/zone/disks/disk
   *
   * @param string $sourceDisk
   */
  public function setSourceDisk($sourceDisk)
  {
    $this->sourceDisk = $sourceDisk;
  }
  /**
   * @return string
   */
  public function getSourceDisk()
  {
    return $this->sourceDisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceDiskEncryptionKey::class, 'Google_Service_Compute_SourceDiskEncryptionKey');
