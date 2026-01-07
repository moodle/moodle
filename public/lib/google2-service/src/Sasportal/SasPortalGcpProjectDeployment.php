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

namespace Google\Service\Sasportal;

class SasPortalGcpProjectDeployment extends \Google\Model
{
  protected $deploymentType = SasPortalDeployment::class;
  protected $deploymentDataType = '';
  /**
   * Whether SAS analytics has been enabled.
   *
   * @var bool
   */
  public $hasEnabledAnalytics;

  /**
   * Deployment associated with the GCP project.
   *
   * @param SasPortalDeployment $deployment
   */
  public function setDeployment(SasPortalDeployment $deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return SasPortalDeployment
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Whether SAS analytics has been enabled.
   *
   * @param bool $hasEnabledAnalytics
   */
  public function setHasEnabledAnalytics($hasEnabledAnalytics)
  {
    $this->hasEnabledAnalytics = $hasEnabledAnalytics;
  }
  /**
   * @return bool
   */
  public function getHasEnabledAnalytics()
  {
    return $this->hasEnabledAnalytics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalGcpProjectDeployment::class, 'Google_Service_Sasportal_SasPortalGcpProjectDeployment');
