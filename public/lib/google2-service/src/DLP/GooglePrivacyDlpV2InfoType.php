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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2InfoType extends \Google\Model
{
  /**
   * Name of the information type. Either a name of your choosing when creating
   * a CustomInfoType, or one of the names listed at
   * https://cloud.google.com/sensitive-data-protection/docs/infotypes-reference
   * when specifying a built-in type. When sending Cloud DLP results to Data
   * Catalog, infoType names should conform to the pattern
   * `[A-Za-z0-9$_-]{1,64}`.
   *
   * @var string
   */
  public $name;
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  /**
   * Optional version name for this InfoType.
   *
   * @var string
   */
  public $version;

  /**
   * Name of the information type. Either a name of your choosing when creating
   * a CustomInfoType, or one of the names listed at
   * https://cloud.google.com/sensitive-data-protection/docs/infotypes-reference
   * when specifying a built-in type. When sending Cloud DLP results to Data
   * Catalog, infoType names should conform to the pattern
   * `[A-Za-z0-9$_-]{1,64}`.
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
   * Optional custom sensitivity for this InfoType. This only applies to data
   * profiling.
   *
   * @param GooglePrivacyDlpV2SensitivityScore $sensitivityScore
   */
  public function setSensitivityScore(GooglePrivacyDlpV2SensitivityScore $sensitivityScore)
  {
    $this->sensitivityScore = $sensitivityScore;
  }
  /**
   * @return GooglePrivacyDlpV2SensitivityScore
   */
  public function getSensitivityScore()
  {
    return $this->sensitivityScore;
  }
  /**
   * Optional version name for this InfoType.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2InfoType::class, 'Google_Service_DLP_GooglePrivacyDlpV2InfoType');
