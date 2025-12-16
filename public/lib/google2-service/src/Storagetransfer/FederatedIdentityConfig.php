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

namespace Google\Service\Storagetransfer;

class FederatedIdentityConfig extends \Google\Model
{
  /**
   * Required. The client (application) ID of the application with federated
   * credentials.
   *
   * @var string
   */
  public $clientId;
  /**
   * Required. The tenant (directory) ID of the application with federated
   * credentials.
   *
   * @var string
   */
  public $tenantId;

  /**
   * Required. The client (application) ID of the application with federated
   * credentials.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Required. The tenant (directory) ID of the application with federated
   * credentials.
   *
   * @param string $tenantId
   */
  public function setTenantId($tenantId)
  {
    $this->tenantId = $tenantId;
  }
  /**
   * @return string
   */
  public function getTenantId()
  {
    return $this->tenantId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FederatedIdentityConfig::class, 'Google_Service_Storagetransfer_FederatedIdentityConfig');
