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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityMonitoringCondition extends \Google\Model
{
  /**
   * Output only. The time of the security monitoring condition creation.
   *
   * @var string
   */
  public $createTime;
  protected $includeType = GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray::class;
  protected $includeDataType = '';
  protected $includeAllResourcesType = GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll::class;
  protected $includeAllResourcesDataType = '';
  /**
   * Identifier. Name of the security monitoring condition resource. Format: org
   * anizations/{org}/securityMonitoringConditions/{security_monitoring_conditio
   * n}
   *
   * @var string
   */
  public $name;
  /**
   * Required. ID of security profile of the security monitoring condition.
   *
   * @var string
   */
  public $profile;
  /**
   * Optional. Scope of the security monitoring condition. For Apigee, the
   * environment is the scope of the resources.
   *
   * @var string
   */
  public $scope;
  /**
   * Output only. Total number of deployed resources within scope.
   *
   * @var int
   */
  public $totalDeployedResources;
  /**
   * Output only. Total number of monitored resources within this condition.
   *
   * @var int
   */
  public $totalMonitoredResources;
  /**
   * Output only. The time of the security monitoring condition update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time of the security monitoring condition creation.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Include only these resources.
   *
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray $include
   */
  public function setInclude(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray $include)
  {
    $this->include = $include;
  }
  /**
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestResourceArray
   */
  public function getInclude()
  {
    return $this->include;
  }
  /**
   * Include all resources under the scope.
   *
   * @param GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll $includeAllResources
   */
  public function setIncludeAllResources(GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll $includeAllResources)
  {
    $this->includeAllResources = $includeAllResources;
  }
  /**
   * @return GoogleCloudApigeeV1BatchComputeSecurityAssessmentResultsRequestIncludeAll
   */
  public function getIncludeAllResources()
  {
    return $this->includeAllResources;
  }
  /**
   * Identifier. Name of the security monitoring condition resource. Format: org
   * anizations/{org}/securityMonitoringConditions/{security_monitoring_conditio
   * n}
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
   * Required. ID of security profile of the security monitoring condition.
   *
   * @param string $profile
   */
  public function setProfile($profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return string
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Optional. Scope of the security monitoring condition. For Apigee, the
   * environment is the scope of the resources.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Output only. Total number of deployed resources within scope.
   *
   * @param int $totalDeployedResources
   */
  public function setTotalDeployedResources($totalDeployedResources)
  {
    $this->totalDeployedResources = $totalDeployedResources;
  }
  /**
   * @return int
   */
  public function getTotalDeployedResources()
  {
    return $this->totalDeployedResources;
  }
  /**
   * Output only. Total number of monitored resources within this condition.
   *
   * @param int $totalMonitoredResources
   */
  public function setTotalMonitoredResources($totalMonitoredResources)
  {
    $this->totalMonitoredResources = $totalMonitoredResources;
  }
  /**
   * @return int
   */
  public function getTotalMonitoredResources()
  {
    return $this->totalMonitoredResources;
  }
  /**
   * Output only. The time of the security monitoring condition update.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityMonitoringCondition::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityMonitoringCondition');
