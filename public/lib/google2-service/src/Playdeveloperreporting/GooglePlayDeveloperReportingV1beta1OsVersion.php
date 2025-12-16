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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1OsVersion extends \Google\Model
{
  /**
   * Numeric version code of the OS - API level
   *
   * @var string
   */
  public $apiLevel;

  /**
   * Numeric version code of the OS - API level
   *
   * @param string $apiLevel
   */
  public function setApiLevel($apiLevel)
  {
    $this->apiLevel = $apiLevel;
  }
  /**
   * @return string
   */
  public function getApiLevel()
  {
    return $this->apiLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1OsVersion::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1OsVersion');
