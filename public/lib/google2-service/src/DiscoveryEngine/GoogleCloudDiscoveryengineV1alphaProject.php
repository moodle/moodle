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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaProject extends \Google\Model
{
  protected $configurableBillingStatusType = GoogleCloudDiscoveryengineV1alphaProjectConfigurableBillingStatus::class;
  protected $configurableBillingStatusDataType = '';
  /**
   * Output only. The timestamp when this project is created.
   *
   * @var string
   */
  public $createTime;
  protected $customerProvidedConfigType = GoogleCloudDiscoveryengineV1alphaProjectCustomerProvidedConfig::class;
  protected $customerProvidedConfigDataType = '';
  /**
   * Output only. Full resource name of the project, for example
   * `projects/{project}`. Note that when making requests, project number and
   * project id are both acceptable, but the server will always respond in
   * project number.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when this project is successfully provisioned.
   * Empty value means this project is still provisioning and is not ready for
   * use.
   *
   * @var string
   */
  public $provisionCompletionTime;
  protected $serviceTermsMapType = GoogleCloudDiscoveryengineV1alphaProjectServiceTerms::class;
  protected $serviceTermsMapDataType = 'map';

  /**
   * Output only. The current status of the project's configurable billing.
   *
   * @param GoogleCloudDiscoveryengineV1alphaProjectConfigurableBillingStatus $configurableBillingStatus
   */
  public function setConfigurableBillingStatus(GoogleCloudDiscoveryengineV1alphaProjectConfigurableBillingStatus $configurableBillingStatus)
  {
    $this->configurableBillingStatus = $configurableBillingStatus;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaProjectConfigurableBillingStatus
   */
  public function getConfigurableBillingStatus()
  {
    return $this->configurableBillingStatus;
  }
  /**
   * Output only. The timestamp when this project is created.
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
   * Optional. Customer provided configurations.
   *
   * @param GoogleCloudDiscoveryengineV1alphaProjectCustomerProvidedConfig $customerProvidedConfig
   */
  public function setCustomerProvidedConfig(GoogleCloudDiscoveryengineV1alphaProjectCustomerProvidedConfig $customerProvidedConfig)
  {
    $this->customerProvidedConfig = $customerProvidedConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaProjectCustomerProvidedConfig
   */
  public function getCustomerProvidedConfig()
  {
    return $this->customerProvidedConfig;
  }
  /**
   * Output only. Full resource name of the project, for example
   * `projects/{project}`. Note that when making requests, project number and
   * project id are both acceptable, but the server will always respond in
   * project number.
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
   * Output only. The timestamp when this project is successfully provisioned.
   * Empty value means this project is still provisioning and is not ready for
   * use.
   *
   * @param string $provisionCompletionTime
   */
  public function setProvisionCompletionTime($provisionCompletionTime)
  {
    $this->provisionCompletionTime = $provisionCompletionTime;
  }
  /**
   * @return string
   */
  public function getProvisionCompletionTime()
  {
    return $this->provisionCompletionTime;
  }
  /**
   * Output only. A map of terms of services. The key is the `id` of
   * ServiceTerms.
   *
   * @param GoogleCloudDiscoveryengineV1alphaProjectServiceTerms[] $serviceTermsMap
   */
  public function setServiceTermsMap($serviceTermsMap)
  {
    $this->serviceTermsMap = $serviceTermsMap;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaProjectServiceTerms[]
   */
  public function getServiceTermsMap()
  {
    return $this->serviceTermsMap;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaProject::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaProject');
