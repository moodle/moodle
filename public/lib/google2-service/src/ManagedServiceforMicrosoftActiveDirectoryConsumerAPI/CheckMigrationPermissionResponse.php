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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class CheckMigrationPermissionResponse extends \Google\Collection
{
  /**
   * DomainMigration is in unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Domain Migration is Disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Domain Migration is Enabled.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Domain Migration is not in valid state.
   */
  public const STATE_NEEDS_MAINTENANCE = 'NEEDS_MAINTENANCE';
  protected $collection_key = 'onpremDomains';
  protected $onpremDomainsType = OnPremDomainSIDDetails::class;
  protected $onpremDomainsDataType = 'array';
  /**
   * The state of DomainMigration.
   *
   * @var string
   */
  public $state;

  /**
   * The state of SID filtering of all the domains which has trust established.
   *
   * @param OnPremDomainSIDDetails[] $onpremDomains
   */
  public function setOnpremDomains($onpremDomains)
  {
    $this->onpremDomains = $onpremDomains;
  }
  /**
   * @return OnPremDomainSIDDetails[]
   */
  public function getOnpremDomains()
  {
    return $this->onpremDomains;
  }
  /**
   * The state of DomainMigration.
   *
   * Accepted values: STATE_UNSPECIFIED, DISABLED, ENABLED, NEEDS_MAINTENANCE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckMigrationPermissionResponse::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_CheckMigrationPermissionResponse');
