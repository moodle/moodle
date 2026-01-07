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

class GoogleCloudAiplatformV1PersistentDiskSpec extends \Google\Model
{
  /**
   * Size in GB of the disk (default is 100GB).
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Type of the disk (default is "pd-standard"). Valid values: "pd-ssd"
   * (Persistent Disk Solid State Drive) "pd-standard" (Persistent Disk Hard
   * Disk Drive) "pd-balanced" (Balanced Persistent Disk) "pd-extreme" (Extreme
   * Persistent Disk)
   *
   * @var string
   */
  public $diskType;

  /**
   * Size in GB of the disk (default is 100GB).
   *
   * @param string $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return string
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Type of the disk (default is "pd-standard"). Valid values: "pd-ssd"
   * (Persistent Disk Solid State Drive) "pd-standard" (Persistent Disk Hard
   * Disk Drive) "pd-balanced" (Balanced Persistent Disk) "pd-extreme" (Extreme
   * Persistent Disk)
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PersistentDiskSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PersistentDiskSpec');
