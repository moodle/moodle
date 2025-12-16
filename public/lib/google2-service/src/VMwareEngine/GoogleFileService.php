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

namespace Google\Service\VMwareEngine;

class GoogleFileService extends \Google\Model
{
  /**
   * Google filestore instance resource name e.g. projects/my-
   * project/locations/me-west1-b/instances/my-instance
   *
   * @var string
   */
  public $filestoreInstance;
  /**
   * Google netapp volume resource name e.g. projects/my-project/locations/me-
   * west1-b/volumes/my-volume
   *
   * @var string
   */
  public $netappVolume;

  /**
   * Google filestore instance resource name e.g. projects/my-
   * project/locations/me-west1-b/instances/my-instance
   *
   * @param string $filestoreInstance
   */
  public function setFilestoreInstance($filestoreInstance)
  {
    $this->filestoreInstance = $filestoreInstance;
  }
  /**
   * @return string
   */
  public function getFilestoreInstance()
  {
    return $this->filestoreInstance;
  }
  /**
   * Google netapp volume resource name e.g. projects/my-project/locations/me-
   * west1-b/volumes/my-volume
   *
   * @param string $netappVolume
   */
  public function setNetappVolume($netappVolume)
  {
    $this->netappVolume = $netappVolume;
  }
  /**
   * @return string
   */
  public function getNetappVolume()
  {
    return $this->netappVolume;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFileService::class, 'Google_Service_VMwareEngine_GoogleFileService');
