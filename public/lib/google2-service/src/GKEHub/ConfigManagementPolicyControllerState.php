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

namespace Google\Service\GKEHub;

class ConfigManagementPolicyControllerState extends \Google\Model
{
  protected $deploymentStateType = ConfigManagementGatekeeperDeploymentState::class;
  protected $deploymentStateDataType = '';
  protected $migrationType = ConfigManagementPolicyControllerMigration::class;
  protected $migrationDataType = '';
  protected $versionType = ConfigManagementPolicyControllerVersion::class;
  protected $versionDataType = '';

  /**
   * The state about the policy controller installation.
   *
   * @param ConfigManagementGatekeeperDeploymentState $deploymentState
   */
  public function setDeploymentState(ConfigManagementGatekeeperDeploymentState $deploymentState)
  {
    $this->deploymentState = $deploymentState;
  }
  /**
   * @return ConfigManagementGatekeeperDeploymentState
   */
  public function getDeploymentState()
  {
    return $this->deploymentState;
  }
  /**
   * Record state of ACM -> PoCo Hub migration for this feature.
   *
   * @param ConfigManagementPolicyControllerMigration $migration
   */
  public function setMigration(ConfigManagementPolicyControllerMigration $migration)
  {
    $this->migration = $migration;
  }
  /**
   * @return ConfigManagementPolicyControllerMigration
   */
  public function getMigration()
  {
    return $this->migration;
  }
  /**
   * The version of Gatekeeper Policy Controller deployed.
   *
   * @param ConfigManagementPolicyControllerVersion $version
   */
  public function setVersion(ConfigManagementPolicyControllerVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return ConfigManagementPolicyControllerVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementPolicyControllerState::class, 'Google_Service_GKEHub_ConfigManagementPolicyControllerState');
