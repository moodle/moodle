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

class RegionDisksStartAsyncReplicationRequest extends \Google\Model
{
  /**
   * The secondary disk to start asynchronous replication to. You can provide
   * this as a partial or full URL to the resource. For example, the following
   * are valid values:              -         https://www.googleapis.com/compute
   * /v1/projects/project/zones/zone/disks/disk            -         https://www
   * .googleapis.com/compute/v1/projects/project/regions/region/disks/disk
   * -         projects/project/zones/zone/disks/disk            -
   * projects/project/regions/region/disks/disk            -
   * zones/zone/disks/disk            -         regions/region/disks/disk
   *
   * @var string
   */
  public $asyncSecondaryDisk;

  /**
   * The secondary disk to start asynchronous replication to. You can provide
   * this as a partial or full URL to the resource. For example, the following
   * are valid values:              -         https://www.googleapis.com/compute
   * /v1/projects/project/zones/zone/disks/disk            -         https://www
   * .googleapis.com/compute/v1/projects/project/regions/region/disks/disk
   * -         projects/project/zones/zone/disks/disk            -
   * projects/project/regions/region/disks/disk            -
   * zones/zone/disks/disk            -         regions/region/disks/disk
   *
   * @param string $asyncSecondaryDisk
   */
  public function setAsyncSecondaryDisk($asyncSecondaryDisk)
  {
    $this->asyncSecondaryDisk = $asyncSecondaryDisk;
  }
  /**
   * @return string
   */
  public function getAsyncSecondaryDisk()
  {
    return $this->asyncSecondaryDisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionDisksStartAsyncReplicationRequest::class, 'Google_Service_Compute_RegionDisksStartAsyncReplicationRequest');
