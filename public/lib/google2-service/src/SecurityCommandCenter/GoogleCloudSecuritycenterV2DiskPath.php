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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2DiskPath extends \Google\Model
{
  /**
   * UUID of the partition (format
   * https://wiki.archlinux.org/title/persistent_block_device_naming#by-uuid)
   *
   * @var string
   */
  public $partitionUuid;
  /**
   * Relative path of the file in the partition as a JSON encoded string.
   * Example: /home/user1/executable_file.sh
   *
   * @var string
   */
  public $relativePath;

  /**
   * UUID of the partition (format
   * https://wiki.archlinux.org/title/persistent_block_device_naming#by-uuid)
   *
   * @param string $partitionUuid
   */
  public function setPartitionUuid($partitionUuid)
  {
    $this->partitionUuid = $partitionUuid;
  }
  /**
   * @return string
   */
  public function getPartitionUuid()
  {
    return $this->partitionUuid;
  }
  /**
   * Relative path of the file in the partition as a JSON encoded string.
   * Example: /home/user1/executable_file.sh
   *
   * @param string $relativePath
   */
  public function setRelativePath($relativePath)
  {
    $this->relativePath = $relativePath;
  }
  /**
   * @return string
   */
  public function getRelativePath()
  {
    return $this->relativePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2DiskPath::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2DiskPath');
