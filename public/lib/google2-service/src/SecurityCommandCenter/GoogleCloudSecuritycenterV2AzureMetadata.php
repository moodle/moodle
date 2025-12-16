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

class GoogleCloudSecuritycenterV2AzureMetadata extends \Google\Collection
{
  protected $collection_key = 'managementGroups';
  protected $managementGroupsType = GoogleCloudSecuritycenterV2AzureManagementGroup::class;
  protected $managementGroupsDataType = 'array';
  protected $resourceGroupType = GoogleCloudSecuritycenterV2AzureResourceGroup::class;
  protected $resourceGroupDataType = '';
  protected $subscriptionType = GoogleCloudSecuritycenterV2AzureSubscription::class;
  protected $subscriptionDataType = '';
  protected $tenantType = GoogleCloudSecuritycenterV2AzureTenant::class;
  protected $tenantDataType = '';

  /**
   * A list of Azure management groups associated with the resource, ordered
   * from lowest level (closest to the subscription) to highest level.
   *
   * @param GoogleCloudSecuritycenterV2AzureManagementGroup[] $managementGroups
   */
  public function setManagementGroups($managementGroups)
  {
    $this->managementGroups = $managementGroups;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AzureManagementGroup[]
   */
  public function getManagementGroups()
  {
    return $this->managementGroups;
  }
  /**
   * The Azure resource group associated with the resource.
   *
   * @param GoogleCloudSecuritycenterV2AzureResourceGroup $resourceGroup
   */
  public function setResourceGroup(GoogleCloudSecuritycenterV2AzureResourceGroup $resourceGroup)
  {
    $this->resourceGroup = $resourceGroup;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AzureResourceGroup
   */
  public function getResourceGroup()
  {
    return $this->resourceGroup;
  }
  /**
   * The Azure subscription associated with the resource.
   *
   * @param GoogleCloudSecuritycenterV2AzureSubscription $subscription
   */
  public function setSubscription(GoogleCloudSecuritycenterV2AzureSubscription $subscription)
  {
    $this->subscription = $subscription;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AzureSubscription
   */
  public function getSubscription()
  {
    return $this->subscription;
  }
  /**
   * The Azure Entra tenant associated with the resource.
   *
   * @param GoogleCloudSecuritycenterV2AzureTenant $tenant
   */
  public function setTenant(GoogleCloudSecuritycenterV2AzureTenant $tenant)
  {
    $this->tenant = $tenant;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AzureTenant
   */
  public function getTenant()
  {
    return $this->tenant;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2AzureMetadata::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2AzureMetadata');
