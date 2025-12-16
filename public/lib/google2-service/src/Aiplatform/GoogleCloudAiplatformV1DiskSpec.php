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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DiskSpec extends \Google\Model
{
  /**
   * Size in GB of the boot disk (default is 100GB).
   *
   * @var int
   */
  public $bootDiskSizeGb;
  /**
   * Type of the boot disk. For non-A3U machines, the default value is "pd-ssd",
   * for A3U machines, the default value is "hyperdisk-balanced". Valid values:
   * "pd-ssd" (Persistent Disk Solid State Drive), "pd-standard" (Persistent
   * Disk Hard Disk Drive) or "hyperdisk-balanced".
   *
   * @var string
   */
  public $bootDiskType;

  /**
   * Size in GB of the boot disk (default is 100GB).
   *
   * @param int $bootDiskSizeGb
   */
  public function setBootDiskSizeGb($bootDiskSizeGb)
  {
    $this->bootDiskSizeGb = $bootDiskSizeGb;
  }
  /**
   * @return int
   */
  public function getBootDiskSizeGb()
  {
    return $this->bootDiskSizeGb;
  }
  /**
   * Type of the boot disk. For non-A3U machines, the default value is "pd-ssd",
   * for A3U machines, the default value is "hyperdisk-balanced". Valid values:
   * "pd-ssd" (Persistent Disk Solid State Drive), "pd-standard" (Persistent
   * Disk Hard Disk Drive) or "hyperdisk-balanced".
   *
   * @param string $bootDiskType
   */
  public function setBootDiskType($bootDiskType)
  {
    $this->bootDiskType = $bootDiskType;
  }
  /**
   * @return string
   */
  public function getBootDiskType()
  {
    return $this->bootDiskType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DiskSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DiskSpec');
