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

namespace Google\Service\SecurityCommandCenter;

class ComplianceDetails extends \Google\Collection
{
  protected $collection_key = 'frameworks';
  protected $cloudControlType = CloudControl::class;
  protected $cloudControlDataType = '';
  /**
   * Cloud Control Deployments associated with the finding. For example, organiz
   * ations/123/locations/global/cloudControlDeployments/deploymentIdentifier
   *
   * @var string[]
   */
  public $cloudControlDeploymentNames;
  protected $frameworksType = Framework::class;
  protected $frameworksDataType = 'array';

  /**
   * CloudControl associated with the finding
   *
   * @param CloudControl $cloudControl
   */
  public function setCloudControl(CloudControl $cloudControl)
  {
    $this->cloudControl = $cloudControl;
  }
  /**
   * @return CloudControl
   */
  public function getCloudControl()
  {
    return $this->cloudControl;
  }
  /**
   * Cloud Control Deployments associated with the finding. For example, organiz
   * ations/123/locations/global/cloudControlDeployments/deploymentIdentifier
   *
   * @param string[] $cloudControlDeploymentNames
   */
  public function setCloudControlDeploymentNames($cloudControlDeploymentNames)
  {
    $this->cloudControlDeploymentNames = $cloudControlDeploymentNames;
  }
  /**
   * @return string[]
   */
  public function getCloudControlDeploymentNames()
  {
    return $this->cloudControlDeploymentNames;
  }
  /**
   * Details of Frameworks associated with the finding
   *
   * @param Framework[] $frameworks
   */
  public function setFrameworks($frameworks)
  {
    $this->frameworks = $frameworks;
  }
  /**
   * @return Framework[]
   */
  public function getFrameworks()
  {
    return $this->frameworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComplianceDetails::class, 'Google_Service_SecurityCommandCenter_ComplianceDetails');
