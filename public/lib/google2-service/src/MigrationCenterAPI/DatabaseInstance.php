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

namespace Google\Service\MigrationCenterAPI;

class DatabaseInstance extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Primary.
   */
  public const ROLE_PRIMARY = 'PRIMARY';
  /**
   * Secondary.
   */
  public const ROLE_SECONDARY = 'SECONDARY';
  /**
   * Arbiter.
   */
  public const ROLE_ARBITER = 'ARBITER';
  /**
   * Optional. The instance's name.
   *
   * @var string
   */
  public $instanceName;
  protected $networkType = DatabaseInstanceNetwork::class;
  protected $networkDataType = '';
  /**
   * Optional. The instance role in the database engine.
   *
   * @var string
   */
  public $role;

  /**
   * Optional. The instance's name.
   *
   * @param string $instanceName
   */
  public function setInstanceName($instanceName)
  {
    $this->instanceName = $instanceName;
  }
  /**
   * @return string
   */
  public function getInstanceName()
  {
    return $this->instanceName;
  }
  /**
   * Optional. Networking details.
   *
   * @param DatabaseInstanceNetwork $network
   */
  public function setNetwork(DatabaseInstanceNetwork $network)
  {
    $this->network = $network;
  }
  /**
   * @return DatabaseInstanceNetwork
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The instance role in the database engine.
   *
   * Accepted values: ROLE_UNSPECIFIED, PRIMARY, SECONDARY, ARBITER
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseInstance::class, 'Google_Service_MigrationCenterAPI_DatabaseInstance');
