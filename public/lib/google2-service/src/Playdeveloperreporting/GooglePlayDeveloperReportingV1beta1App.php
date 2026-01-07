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

class GooglePlayDeveloperReportingV1beta1App extends \Google\Model
{
  /**
   * Title of the app. This is the latest title as set in the Play Console and
   * may not yet have been reviewed, so might not match the Play Store. Example:
   * `Google Maps`.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The resource name. Format: apps/{app}
   *
   * @var string
   */
  public $name;
  /**
   * Package name of the app. Example: `com.example.app123`.
   *
   * @var string
   */
  public $packageName;

  /**
   * Title of the app. This is the latest title as set in the Play Console and
   * may not yet have been reviewed, so might not match the Play Store. Example:
   * `Google Maps`.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Identifier. The resource name. Format: apps/{app}
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
  /**
   * Package name of the app. Example: `com.example.app123`.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1App::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1App');
