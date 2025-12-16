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

class CloudAiPlatformTenantresourceTenantResource extends \Google\Collection
{
  protected $collection_key = 'tenantProjectResources';
  protected $p4ServiceAccountsType = CloudAiPlatformTenantresourceServiceAccountIdentity::class;
  protected $p4ServiceAccountsDataType = 'array';
  protected $tenantProjectResourcesType = CloudAiPlatformTenantresourceTenantProjectResource::class;
  protected $tenantProjectResourcesDataType = 'array';

  /**
   * A list of P4 service accounts (go/p4sa) to provision or deprovision.
   *
   * @param CloudAiPlatformTenantresourceServiceAccountIdentity[] $p4ServiceAccounts
   */
  public function setP4ServiceAccounts($p4ServiceAccounts)
  {
    $this->p4ServiceAccounts = $p4ServiceAccounts;
  }
  /**
   * @return CloudAiPlatformTenantresourceServiceAccountIdentity[]
   */
  public function getP4ServiceAccounts()
  {
    return $this->p4ServiceAccounts;
  }
  /**
   * A list of tenant projects and tenant resources to provision or deprovision.
   *
   * @param CloudAiPlatformTenantresourceTenantProjectResource[] $tenantProjectResources
   */
  public function setTenantProjectResources($tenantProjectResources)
  {
    $this->tenantProjectResources = $tenantProjectResources;
  }
  /**
   * @return CloudAiPlatformTenantresourceTenantProjectResource[]
   */
  public function getTenantProjectResources()
  {
    return $this->tenantProjectResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceTenantResource::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceTenantResource');
