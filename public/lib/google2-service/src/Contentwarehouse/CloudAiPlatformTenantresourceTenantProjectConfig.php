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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceTenantProjectConfig extends \Google\Collection
{
  protected $collection_key = 'services';
  protected $billingConfigType = GoogleApiServiceconsumermanagementV1BillingConfig::class;
  protected $billingConfigDataType = '';
  /**
   * Input/Output [Required]. The folder that holds tenant projects and folder-
   * level permissions will be automatically granted to all tenant projects
   * under the folder. Note: the valid folder format is
   * `folders/{folder_number}`.
   *
   * @var string
   */
  public $folder;
  protected $policyBindingsType = GoogleApiServiceconsumermanagementV1PolicyBinding::class;
  protected $policyBindingsDataType = 'array';
  /**
   * Input/Output [Required]. The API services that are enabled on the tenant
   * project during creation.
   *
   * @var string[]
   */
  public $services;

  /**
   * Input/Output [Required]. The billing account properties to create the
   * tenant project.
   *
   * @param GoogleApiServiceconsumermanagementV1BillingConfig $billingConfig
   */
  public function setBillingConfig(GoogleApiServiceconsumermanagementV1BillingConfig $billingConfig)
  {
    $this->billingConfig = $billingConfig;
  }
  /**
   * @return GoogleApiServiceconsumermanagementV1BillingConfig
   */
  public function getBillingConfig()
  {
    return $this->billingConfig;
  }
  /**
   * Input/Output [Required]. The folder that holds tenant projects and folder-
   * level permissions will be automatically granted to all tenant projects
   * under the folder. Note: the valid folder format is
   * `folders/{folder_number}`.
   *
   * @param string $folder
   */
  public function setFolder($folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return string
   */
  public function getFolder()
  {
    return $this->folder;
  }
  /**
   * Input/Output [Required]. The policy bindings that are applied to the tenant
   * project during creation. At least one binding must have the role
   * `roles/owner` with either `user` or `group` type.
   *
   * @param GoogleApiServiceconsumermanagementV1PolicyBinding[] $policyBindings
   */
  public function setPolicyBindings($policyBindings)
  {
    $this->policyBindings = $policyBindings;
  }
  /**
   * @return GoogleApiServiceconsumermanagementV1PolicyBinding[]
   */
  public function getPolicyBindings()
  {
    return $this->policyBindings;
  }
  /**
   * Input/Output [Required]. The API services that are enabled on the tenant
   * project during creation.
   *
   * @param string[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return string[]
   */
  public function getServices()
  {
    return $this->services;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceTenantProjectConfig::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceTenantProjectConfig');
