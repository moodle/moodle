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

namespace Google\Service\CloudIAP;

class GcipSettings extends \Google\Collection
{
  protected $collection_key = 'tenantIds';
  /**
   * Login page URI associated with the GCIP tenants. Typically, all resources
   * within the same project share the same login page, though it could be
   * overridden at the sub resource level.
   *
   * @var string
   */
  public $loginPageUri;
  /**
   * Optional. GCIP tenant IDs that are linked to the IAP resource. `tenant_ids`
   * could be a string beginning with a number character to indicate
   * authenticating with GCIP tenant flow, or in the format of `_` to indicate
   * authenticating with GCIP agent flow. If agent flow is used, `tenant_ids`
   * should only contain one single element, while for tenant flow, `tenant_ids`
   * can contain multiple elements.
   *
   * @var string[]
   */
  public $tenantIds;

  /**
   * Login page URI associated with the GCIP tenants. Typically, all resources
   * within the same project share the same login page, though it could be
   * overridden at the sub resource level.
   *
   * @param string $loginPageUri
   */
  public function setLoginPageUri($loginPageUri)
  {
    $this->loginPageUri = $loginPageUri;
  }
  /**
   * @return string
   */
  public function getLoginPageUri()
  {
    return $this->loginPageUri;
  }
  /**
   * Optional. GCIP tenant IDs that are linked to the IAP resource. `tenant_ids`
   * could be a string beginning with a number character to indicate
   * authenticating with GCIP tenant flow, or in the format of `_` to indicate
   * authenticating with GCIP agent flow. If agent flow is used, `tenant_ids`
   * should only contain one single element, while for tenant flow, `tenant_ids`
   * can contain multiple elements.
   *
   * @param string[] $tenantIds
   */
  public function setTenantIds($tenantIds)
  {
    $this->tenantIds = $tenantIds;
  }
  /**
   * @return string[]
   */
  public function getTenantIds()
  {
    return $this->tenantIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcipSettings::class, 'Google_Service_CloudIAP_GcipSettings');
