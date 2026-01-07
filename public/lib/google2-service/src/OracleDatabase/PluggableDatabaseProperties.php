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

namespace Google\Service\OracleDatabase;

class PluggableDatabaseProperties extends \Google\Collection
{
  /**
   * The lifecycle state is unspecified.
   */
  public const LIFECYCLE_STATE_PLUGGABLE_DATABASE_LIFECYCLE_STATE_UNSPECIFIED = 'PLUGGABLE_DATABASE_LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * The pluggable database is provisioning.
   */
  public const LIFECYCLE_STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The pluggable database is available.
   */
  public const LIFECYCLE_STATE_AVAILABLE = 'AVAILABLE';
  /**
   * The pluggable database is terminating.
   */
  public const LIFECYCLE_STATE_TERMINATING = 'TERMINATING';
  /**
   * The pluggable database is terminated.
   */
  public const LIFECYCLE_STATE_TERMINATED = 'TERMINATED';
  /**
   * The pluggable database is updating.
   */
  public const LIFECYCLE_STATE_UPDATING = 'UPDATING';
  /**
   * The pluggable database is in a failed state.
   */
  public const LIFECYCLE_STATE_FAILED = 'FAILED';
  /**
   * The pluggable database is relocating.
   */
  public const LIFECYCLE_STATE_RELOCATING = 'RELOCATING';
  /**
   * The pluggable database is relocated.
   */
  public const LIFECYCLE_STATE_RELOCATED = 'RELOCATED';
  /**
   * The pluggable database is refreshing.
   */
  public const LIFECYCLE_STATE_REFRESHING = 'REFRESHING';
  /**
   * The pluggable database is restoring.
   */
  public const LIFECYCLE_STATE_RESTORE_IN_PROGRESS = 'RESTORE_IN_PROGRESS';
  /**
   * The pluggable database restore failed.
   */
  public const LIFECYCLE_STATE_RESTORE_FAILED = 'RESTORE_FAILED';
  /**
   * The pluggable database is backing up.
   */
  public const LIFECYCLE_STATE_BACKUP_IN_PROGRESS = 'BACKUP_IN_PROGRESS';
  /**
   * The pluggable database is disabled.
   */
  public const LIFECYCLE_STATE_DISABLED = 'DISABLED';
  /**
   * The status is not specified.
   */
  public const OPERATIONS_INSIGHTS_STATE_OPERATIONS_INSIGHTS_STATE_UNSPECIFIED = 'OPERATIONS_INSIGHTS_STATE_UNSPECIFIED';
  /**
   * Operations Insights is enabling.
   */
  public const OPERATIONS_INSIGHTS_STATE_ENABLING = 'ENABLING';
  /**
   * Operations Insights is enabled.
   */
  public const OPERATIONS_INSIGHTS_STATE_ENABLED = 'ENABLED';
  /**
   * Operations Insights is disabling.
   */
  public const OPERATIONS_INSIGHTS_STATE_DISABLING = 'DISABLING';
  /**
   * Operations Insights is not enabled.
   */
  public const OPERATIONS_INSIGHTS_STATE_NOT_ENABLED = 'NOT_ENABLED';
  /**
   * Operations Insights failed to enable.
   */
  public const OPERATIONS_INSIGHTS_STATE_FAILED_ENABLING = 'FAILED_ENABLING';
  /**
   * Operations Insights failed to disable.
   */
  public const OPERATIONS_INSIGHTS_STATE_FAILED_DISABLING = 'FAILED_DISABLING';
  protected $collection_key = 'pdbNodeLevelDetails';
  /**
   * Required. The OCID of the compartment.
   *
   * @var string
   */
  public $compartmentId;
  protected $connectionStringsType = PluggableDatabaseConnectionStrings::class;
  protected $connectionStringsDataType = '';
  /**
   * Required. The OCID of the CDB.
   *
   * @var string
   */
  public $containerDatabaseOcid;
  protected $databaseManagementConfigType = DatabaseManagementConfig::class;
  protected $databaseManagementConfigDataType = '';
  protected $definedTagsType = DefinedTagValue::class;
  protected $definedTagsDataType = 'map';
  /**
   * Optional. Free-form tags for this resource. Each tag is a simple key-value
   * pair with no predefined name, type, or namespace.
   *
   * @var string[]
   */
  public $freeformTags;
  /**
   * Optional. The restricted mode of the pluggable database. If a pluggable
   * database is opened in restricted mode, the user needs both create a session
   * and have restricted session privileges to connect to it.
   *
   * @var bool
   */
  public $isRestricted;
  /**
   * Output only. Additional information about the current lifecycle state.
   *
   * @var string
   */
  public $lifecycleDetails;
  /**
   * Output only. The current state of the pluggable database.
   *
   * @var string
   */
  public $lifecycleState;
  /**
   * Output only. The OCID of the pluggable database.
   *
   * @var string
   */
  public $ocid;
  /**
   * Output only. The status of Operations Insights for this Database.
   *
   * @var string
   */
  public $operationsInsightsState;
  /**
   * Required. The database name.
   *
   * @var string
   */
  public $pdbName;
  protected $pdbNodeLevelDetailsType = PluggableDatabaseNodeLevelDetails::class;
  protected $pdbNodeLevelDetailsDataType = 'array';

  /**
   * Required. The OCID of the compartment.
   *
   * @param string $compartmentId
   */
  public function setCompartmentId($compartmentId)
  {
    $this->compartmentId = $compartmentId;
  }
  /**
   * @return string
   */
  public function getCompartmentId()
  {
    return $this->compartmentId;
  }
  /**
   * Optional. The Connection strings used to connect to the Oracle Database.
   *
   * @param PluggableDatabaseConnectionStrings $connectionStrings
   */
  public function setConnectionStrings(PluggableDatabaseConnectionStrings $connectionStrings)
  {
    $this->connectionStrings = $connectionStrings;
  }
  /**
   * @return PluggableDatabaseConnectionStrings
   */
  public function getConnectionStrings()
  {
    return $this->connectionStrings;
  }
  /**
   * Required. The OCID of the CDB.
   *
   * @param string $containerDatabaseOcid
   */
  public function setContainerDatabaseOcid($containerDatabaseOcid)
  {
    $this->containerDatabaseOcid = $containerDatabaseOcid;
  }
  /**
   * @return string
   */
  public function getContainerDatabaseOcid()
  {
    return $this->containerDatabaseOcid;
  }
  /**
   * Output only. The configuration of the Database Management service.
   *
   * @param DatabaseManagementConfig $databaseManagementConfig
   */
  public function setDatabaseManagementConfig(DatabaseManagementConfig $databaseManagementConfig)
  {
    $this->databaseManagementConfig = $databaseManagementConfig;
  }
  /**
   * @return DatabaseManagementConfig
   */
  public function getDatabaseManagementConfig()
  {
    return $this->databaseManagementConfig;
  }
  /**
   * Optional. Defined tags for this resource. Each key is predefined and scoped
   * to a namespace.
   *
   * @param DefinedTagValue[] $definedTags
   */
  public function setDefinedTags($definedTags)
  {
    $this->definedTags = $definedTags;
  }
  /**
   * @return DefinedTagValue[]
   */
  public function getDefinedTags()
  {
    return $this->definedTags;
  }
  /**
   * Optional. Free-form tags for this resource. Each tag is a simple key-value
   * pair with no predefined name, type, or namespace.
   *
   * @param string[] $freeformTags
   */
  public function setFreeformTags($freeformTags)
  {
    $this->freeformTags = $freeformTags;
  }
  /**
   * @return string[]
   */
  public function getFreeformTags()
  {
    return $this->freeformTags;
  }
  /**
   * Optional. The restricted mode of the pluggable database. If a pluggable
   * database is opened in restricted mode, the user needs both create a session
   * and have restricted session privileges to connect to it.
   *
   * @param bool $isRestricted
   */
  public function setIsRestricted($isRestricted)
  {
    $this->isRestricted = $isRestricted;
  }
  /**
   * @return bool
   */
  public function getIsRestricted()
  {
    return $this->isRestricted;
  }
  /**
   * Output only. Additional information about the current lifecycle state.
   *
   * @param string $lifecycleDetails
   */
  public function setLifecycleDetails($lifecycleDetails)
  {
    $this->lifecycleDetails = $lifecycleDetails;
  }
  /**
   * @return string
   */
  public function getLifecycleDetails()
  {
    return $this->lifecycleDetails;
  }
  /**
   * Output only. The current state of the pluggable database.
   *
   * Accepted values: PLUGGABLE_DATABASE_LIFECYCLE_STATE_UNSPECIFIED,
   * PROVISIONING, AVAILABLE, TERMINATING, TERMINATED, UPDATING, FAILED,
   * RELOCATING, RELOCATED, REFRESHING, RESTORE_IN_PROGRESS, RESTORE_FAILED,
   * BACKUP_IN_PROGRESS, DISABLED
   *
   * @param self::LIFECYCLE_STATE_* $lifecycleState
   */
  public function setLifecycleState($lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return self::LIFECYCLE_STATE_*
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Output only. The OCID of the pluggable database.
   *
   * @param string $ocid
   */
  public function setOcid($ocid)
  {
    $this->ocid = $ocid;
  }
  /**
   * @return string
   */
  public function getOcid()
  {
    return $this->ocid;
  }
  /**
   * Output only. The status of Operations Insights for this Database.
   *
   * Accepted values: OPERATIONS_INSIGHTS_STATE_UNSPECIFIED, ENABLING, ENABLED,
   * DISABLING, NOT_ENABLED, FAILED_ENABLING, FAILED_DISABLING
   *
   * @param self::OPERATIONS_INSIGHTS_STATE_* $operationsInsightsState
   */
  public function setOperationsInsightsState($operationsInsightsState)
  {
    $this->operationsInsightsState = $operationsInsightsState;
  }
  /**
   * @return self::OPERATIONS_INSIGHTS_STATE_*
   */
  public function getOperationsInsightsState()
  {
    return $this->operationsInsightsState;
  }
  /**
   * Required. The database name.
   *
   * @param string $pdbName
   */
  public function setPdbName($pdbName)
  {
    $this->pdbName = $pdbName;
  }
  /**
   * @return string
   */
  public function getPdbName()
  {
    return $this->pdbName;
  }
  /**
   * Optional. Pluggable Database Node Level Details
   *
   * @param PluggableDatabaseNodeLevelDetails[] $pdbNodeLevelDetails
   */
  public function setPdbNodeLevelDetails($pdbNodeLevelDetails)
  {
    $this->pdbNodeLevelDetails = $pdbNodeLevelDetails;
  }
  /**
   * @return PluggableDatabaseNodeLevelDetails[]
   */
  public function getPdbNodeLevelDetails()
  {
    return $this->pdbNodeLevelDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PluggableDatabaseProperties::class, 'Google_Service_OracleDatabase_PluggableDatabaseProperties');
