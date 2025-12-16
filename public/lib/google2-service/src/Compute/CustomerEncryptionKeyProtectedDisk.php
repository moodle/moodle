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

class CustomerEncryptionKeyProtectedDisk extends \Google\Model
{
  protected $diskEncryptionKeyType = CustomerEncryptionKey::class;
  protected $diskEncryptionKeyDataType = '';
  /**
   * Specifies a valid partial or full URL to an existing Persistent Disk
   * resource. This field is only applicable for persistent disks. For example:
   *
   * "source": "/compute/v1/projects/project_id/zones/zone/disks/ disk_name
   *
   * @var string
   */
  public $source;

  /**
   * Decrypts data associated with the disk with acustomer-supplied encryption
   * key.
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
   * Specifies a valid partial or full URL to an existing Persistent Disk
   * resource. This field is only applicable for persistent disks. For example:
   *
   * "source": "/compute/v1/projects/project_id/zones/zone/disks/ disk_name
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerEncryptionKeyProtectedDisk::class, 'Google_Service_Compute_CustomerEncryptionKeyProtectedDisk');
