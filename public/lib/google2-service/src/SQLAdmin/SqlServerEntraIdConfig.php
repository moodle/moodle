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

namespace Google\Service\SQLAdmin;

class SqlServerEntraIdConfig extends \Google\Model
{
  /**
   * Optional. The application ID for the Entra ID configuration.
   *
   * @var string
   */
  public $applicationId;
  /**
   * Output only. This is always sql#sqlServerEntraIdConfig
   *
   * @var string
   */
  public $kind;
  /**
   * Optional. The tenant ID for the Entra ID configuration.
   *
   * @var string
   */
  public $tenantId;

  /**
   * Optional. The application ID for the Entra ID configuration.
   *
   * @param string $applicationId
   */
  public function setApplicationId($applicationId)
  {
    $this->applicationId = $applicationId;
  }
  /**
   * @return string
   */
  public function getApplicationId()
  {
    return $this->applicationId;
  }
  /**
   * Output only. This is always sql#sqlServerEntraIdConfig
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Optional. The tenant ID for the Entra ID configuration.
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
class_alias(SqlServerEntraIdConfig::class, 'Google_Service_SQLAdmin_SqlServerEntraIdConfig');
