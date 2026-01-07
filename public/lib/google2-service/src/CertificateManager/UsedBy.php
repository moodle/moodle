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

namespace Google\Service\CertificateManager;

class UsedBy extends \Google\Model
{
  /**
   * Output only. Full name of the resource https://google.aip.dev/122#full-
   * resource-names, e.g. `//certificatemanager.googleapis.com/projects/location
   * s/certificateMaps/certificateMapEntries` or
   * `//compute.googleapis.com/projects/locations/targetHttpsProxies`.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Full name of the resource https://google.aip.dev/122#full-
   * resource-names, e.g. `//certificatemanager.googleapis.com/projects/location
   * s/certificateMaps/certificateMapEntries` or
   * `//compute.googleapis.com/projects/locations/targetHttpsProxies`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsedBy::class, 'Google_Service_CertificateManager_UsedBy');
