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

namespace Google\Service\ServiceConsumerManagement;

class TenantProjectConfig extends \Google\Collection
{
  protected $collection_key = 'services';
  protected $billingConfigType = BillingConfig::class;
  protected $billingConfigDataType = '';
  /**
   * Folder where project in this tenancy unit must be located This folder must
   * have been previously created with the required permissions for the caller
   * to create and configure a project in it. Valid folder resource names have
   * the format `folders/{folder_number}` (for example, `folders/123456`).
   *
   * @var string
   */
  public $folder;
  /**
   * Labels that are applied to this project.
   *
   * @var string[]
   */
  public $labels;
  protected $serviceAccountConfigType = ServiceAccountConfig::class;
  protected $serviceAccountConfigDataType = '';
  /**
   * Google Cloud API names of services that are activated on this project
   * during provisioning. If any of these services can't be activated, the
   * request fails. For example:
   * 'compute.googleapis.com','cloudfunctions.googleapis.com'
   *
   * @var string[]
   */
  public $services;
  protected $tenantProjectPolicyType = TenantProjectPolicy::class;
  protected $tenantProjectPolicyDataType = '';

  /**
   * Billing account properties. The billing account must be specified.
   *
   * @param BillingConfig $billingConfig
   */
  public function setBillingConfig(BillingConfig $billingConfig)
  {
    $this->billingConfig = $billingConfig;
  }
  /**
   * @return BillingConfig
   */
  public function getBillingConfig()
  {
    return $this->billingConfig;
  }
  /**
   * Folder where project in this tenancy unit must be located This folder must
   * have been previously created with the required permissions for the caller
   * to create and configure a project in it. Valid folder resource names have
   * the format `folders/{folder_number}` (for example, `folders/123456`).
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
   * Labels that are applied to this project.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Configuration for the IAM service account on the tenant project.
   *
   * @param ServiceAccountConfig $serviceAccountConfig
   */
  public function setServiceAccountConfig(ServiceAccountConfig $serviceAccountConfig)
  {
    $this->serviceAccountConfig = $serviceAccountConfig;
  }
  /**
   * @return ServiceAccountConfig
   */
  public function getServiceAccountConfig()
  {
    return $this->serviceAccountConfig;
  }
  /**
   * Google Cloud API names of services that are activated on this project
   * during provisioning. If any of these services can't be activated, the
   * request fails. For example:
   * 'compute.googleapis.com','cloudfunctions.googleapis.com'
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
  /**
   * Describes ownership and policies for the new tenant project.
   *
   * @param TenantProjectPolicy $tenantProjectPolicy
   */
  public function setTenantProjectPolicy(TenantProjectPolicy $tenantProjectPolicy)
  {
    $this->tenantProjectPolicy = $tenantProjectPolicy;
  }
  /**
   * @return TenantProjectPolicy
   */
  public function getTenantProjectPolicy()
  {
    return $this->tenantProjectPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TenantProjectConfig::class, 'Google_Service_ServiceConsumerManagement_TenantProjectConfig');
