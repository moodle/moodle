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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1RiskAssessment extends \Google\Model
{
  /**
   * Risk assessment for the extension. Currently, this is a numerical value,
   * and its interpretation is specific to each risk assessment provider.
   *
   * @var string
   */
  public $assessment;
  /**
   * A URL that a user can navigate to for more information about the risk
   * assessment.
   *
   * @var string
   */
  public $detailsUrl;
  /**
   * The version of the extension that this assessment applies to.
   *
   * @var string
   */
  public $version;

  /**
   * Risk assessment for the extension. Currently, this is a numerical value,
   * and its interpretation is specific to each risk assessment provider.
   *
   * @param string $assessment
   */
  public function setAssessment($assessment)
  {
    $this->assessment = $assessment;
  }
  /**
   * @return string
   */
  public function getAssessment()
  {
    return $this->assessment;
  }
  /**
   * A URL that a user can navigate to for more information about the risk
   * assessment.
   *
   * @param string $detailsUrl
   */
  public function setDetailsUrl($detailsUrl)
  {
    $this->detailsUrl = $detailsUrl;
  }
  /**
   * @return string
   */
  public function getDetailsUrl()
  {
    return $this->detailsUrl;
  }
  /**
   * The version of the extension that this assessment applies to.
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
class_alias(GoogleChromeManagementV1RiskAssessment::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1RiskAssessment');
