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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaChangeCustomerConfigRequest extends \Google\Model
{
  protected $customerConfigType = GoogleCloudIntegrationsV1alphaCustomerConfig::class;
  protected $customerConfigDataType = '';
  /**
   * Required. Field mask specifying the fields in the customer config that have
   * been modified and must be updated. If absent or empty, no fields are
   * updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Optional. The customer configuration to be updated.
   *
   * @param GoogleCloudIntegrationsV1alphaCustomerConfig $customerConfig
   */
  public function setCustomerConfig(GoogleCloudIntegrationsV1alphaCustomerConfig $customerConfig)
  {
    $this->customerConfig = $customerConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaCustomerConfig
   */
  public function getCustomerConfig()
  {
    return $this->customerConfig;
  }
  /**
   * Required. Field mask specifying the fields in the customer config that have
   * been modified and must be updated. If absent or empty, no fields are
   * updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaChangeCustomerConfigRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaChangeCustomerConfigRequest');
