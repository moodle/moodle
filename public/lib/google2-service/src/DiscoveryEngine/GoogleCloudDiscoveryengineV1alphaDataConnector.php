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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaDataConnector extends \Google\Collection
{
  /**
   * Default value.
   */
  public const ACTION_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connector is being set up.
   */
  public const ACTION_STATE_CREATING = 'CREATING';
  /**
   * The connector is successfully set up and awaiting next sync run.
   */
  public const ACTION_STATE_ACTIVE = 'ACTIVE';
  /**
   * The connector is in error. The error details can be found in
   * DataConnector.errors. If the error is unfixable, the DataConnector can be
   * deleted by [CollectionService.DeleteCollection] API.
   */
  public const ACTION_STATE_FAILED = 'FAILED';
  /**
   * The connector is actively syncing records from the data source.
   */
  public const ACTION_STATE_RUNNING = 'RUNNING';
  /**
   * The connector has completed a sync run, but encountered non-fatal errors.
   */
  public const ACTION_STATE_WARNING = 'WARNING';
  /**
   * Connector initialization failed. Potential causes include runtime errors or
   * issues in the asynchronous pipeline, preventing the request from reaching
   * downstream services (except for some connector types).
   */
  public const ACTION_STATE_INITIALIZATION_FAILED = 'INITIALIZATION_FAILED';
  /**
   * Connector is in the process of an update.
   */
  public const ACTION_STATE_UPDATING = 'UPDATING';
  /**
   * Default value.
   */
  public const CONNECTOR_TYPE_CONNECTOR_TYPE_UNSPECIFIED = 'CONNECTOR_TYPE_UNSPECIFIED';
  /**
   * Third party connector to connector to third party application.
   */
  public const CONNECTOR_TYPE_THIRD_PARTY = 'THIRD_PARTY';
  /**
   * Data connector connects between FHIR store and VAIS datastore.
   */
  public const CONNECTOR_TYPE_GCP_FHIR = 'GCP_FHIR';
  /**
   * Big query connector.
   */
  public const CONNECTOR_TYPE_BIG_QUERY = 'BIG_QUERY';
  /**
   * Google Cloud Storage connector.
   */
  public const CONNECTOR_TYPE_GCS = 'GCS';
  /**
   * Gmail connector.
   */
  public const CONNECTOR_TYPE_GOOGLE_MAIL = 'GOOGLE_MAIL';
  /**
   * Google Calendar connector.
   */
  public const CONNECTOR_TYPE_GOOGLE_CALENDAR = 'GOOGLE_CALENDAR';
  /**
   * Google Drive connector.
   */
  public const CONNECTOR_TYPE_GOOGLE_DRIVE = 'GOOGLE_DRIVE';
  /**
   * Native Cloud Identity connector for people search powered by People API.
   */
  public const CONNECTOR_TYPE_NATIVE_CLOUD_IDENTITY = 'NATIVE_CLOUD_IDENTITY';
  /**
   * Federated connector, it is a third party connector that doesn't ingestion
   * data, and search is powered by third party application's API.
   */
  public const CONNECTOR_TYPE_THIRD_PARTY_FEDERATED = 'THIRD_PARTY_FEDERATED';
  /**
   * Connector utilized for End User Authentication features.
   */
  public const CONNECTOR_TYPE_THIRD_PARTY_EUA = 'THIRD_PARTY_EUA';
  /**
   * Google Cloud NetApp Volumes connector.
   */
  public const CONNECTOR_TYPE_GCNV = 'GCNV';
  /**
   * Default value.
   */
  public const REALTIME_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connector is being set up.
   */
  public const REALTIME_STATE_CREATING = 'CREATING';
  /**
   * The connector is successfully set up and awaiting next sync run.
   */
  public const REALTIME_STATE_ACTIVE = 'ACTIVE';
  /**
   * The connector is in error. The error details can be found in
   * DataConnector.errors. If the error is unfixable, the DataConnector can be
   * deleted by [CollectionService.DeleteCollection] API.
   */
  public const REALTIME_STATE_FAILED = 'FAILED';
  /**
   * The connector is actively syncing records from the data source.
   */
  public const REALTIME_STATE_RUNNING = 'RUNNING';
  /**
   * The connector has completed a sync run, but encountered non-fatal errors.
   */
  public const REALTIME_STATE_WARNING = 'WARNING';
  /**
   * Connector initialization failed. Potential causes include runtime errors or
   * issues in the asynchronous pipeline, preventing the request from reaching
   * downstream services (except for some connector types).
   */
  public const REALTIME_STATE_INITIALIZATION_FAILED = 'INITIALIZATION_FAILED';
  /**
   * Connector is in the process of an update.
   */
  public const REALTIME_STATE_UPDATING = 'UPDATING';
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connector is being set up.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The connector is successfully set up and awaiting next sync run.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The connector is in error. The error details can be found in
   * DataConnector.errors. If the error is unfixable, the DataConnector can be
   * deleted by [CollectionService.DeleteCollection] API.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The connector is actively syncing records from the data source.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The connector has completed a sync run, but encountered non-fatal errors.
   */
  public const STATE_WARNING = 'WARNING';
  /**
   * Connector initialization failed. Potential causes include runtime errors or
   * issues in the asynchronous pipeline, preventing the request from reaching
   * downstream services (except for some connector types).
   */
  public const STATE_INITIALIZATION_FAILED = 'INITIALIZATION_FAILED';
  /**
   * Connector is in the process of an update.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The connector will sync data periodically based on the refresh_interval.
   * Use it with auto_run_disabled to pause the periodic sync, or indicate a
   * one-time sync.
   */
  public const SYNC_MODE_PERIODIC = 'PERIODIC';
  /**
   * The data will be synced in real time.
   */
  public const SYNC_MODE_STREAMING = 'STREAMING';
  /**
   * Connector that doesn't ingest data will have this value
   */
  public const SYNC_MODE_UNSPECIFIED = 'UNSPECIFIED';
  protected $collection_key = 'staticIpAddresses';
  /**
   * Optional. Whether the connector will be created with an ACL config.
   * Currently this field only affects Cloud Storage and BigQuery connectors.
   *
   * @var bool
   */
  public $aclEnabled;
  protected $actionConfigType = GoogleCloudDiscoveryengineV1alphaActionConfig::class;
  protected $actionConfigDataType = '';
  /**
   * Output only. State of the action connector. This reflects whether the
   * action connector is initializing, active or has encountered errors.
   *
   * @var string
   */
  public $actionState;
  protected $alertPolicyConfigsType = GoogleCloudDiscoveryengineV1alphaAlertPolicyConfig::class;
  protected $alertPolicyConfigsDataType = 'array';
  /**
   * Optional. Indicates whether the connector is disabled for auto run. It can
   * be used to pause periodical and real time sync. Update: with the
   * introduction of incremental_sync_disabled, auto_run_disabled is used to
   * pause/disable only full syncs
   *
   * @var bool
   */
  public $autoRunDisabled;
  protected $bapConfigType = GoogleCloudDiscoveryengineV1alphaBAPConfig::class;
  protected $bapConfigDataType = '';
  /**
   * Output only. User actions that must be completed before the connector can
   * start syncing data.
   *
   * @var string[]
   */
  public $blockingReasons;
  /**
   * Optional. The modes enabled for this connector. Default state is
   * CONNECTOR_MODE_UNSPECIFIED.
   *
   * @var string[]
   */
  public $connectorModes;
  /**
   * Output only. The type of connector. Each source can only map to one type.
   * For example, salesforce, confluence and jira have THIRD_PARTY connector
   * type. It is not mutable once set by system.
   *
   * @var string
   */
  public $connectorType;
  /**
   * Optional. Whether the END USER AUTHENTICATION connector is created in SaaS.
   *
   * @var bool
   */
  public $createEuaSaas;
  /**
   * Output only. Timestamp the DataConnector was created at.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The name of the data source. Supported values: `salesforce`,
   * `jira`, `confluence`, `bigquery`.
   *
   * @var string
   */
  public $dataSource;
  protected $destinationConfigsType = GoogleCloudDiscoveryengineV1alphaDestinationConfig::class;
  protected $destinationConfigsDataType = 'array';
  protected $endUserConfigType = GoogleCloudDiscoveryengineV1alphaDataConnectorEndUserConfig::class;
  protected $endUserConfigDataType = '';
  protected $entitiesType = GoogleCloudDiscoveryengineV1alphaDataConnectorSourceEntity::class;
  protected $entitiesDataType = 'array';
  protected $errorsType = GoogleRpcStatus::class;
  protected $errorsDataType = 'array';
  protected $federatedConfigType = GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig::class;
  protected $federatedConfigDataType = '';
  /**
   * Optional. If the connector is a hybrid connector, determines whether
   * ingestion is enabled and appropriate resources are provisioned during
   * connector creation. If the connector is not a hybrid connector, this field
   * is ignored.
   *
   * @var bool
   */
  public $hybridIngestionDisabled;
  /**
   * The refresh interval to sync the Access Control List information for the
   * documents ingested by this connector. If not set, the access control list
   * will be refreshed at the default interval of 30 minutes. The identity
   * refresh interval can be at least 30 minutes and at most 7 days.
   *
   * @deprecated
   * @var string
   */
  public $identityRefreshInterval;
  protected $identityScheduleConfigType = GoogleCloudDiscoveryengineV1alphaIdentityScheduleConfig::class;
  protected $identityScheduleConfigDataType = '';
  /**
   * Optional. The refresh interval specifically for incremental data syncs. If
   * unset, incremental syncs will use the default from env, set to 3hrs. The
   * minimum is 30 minutes and maximum is 7 days. Applicable to only 3P
   * connectors. When the refresh interval is set to the same value as the
   * incremental refresh interval, incremental sync will be disabled.
   *
   * @var string
   */
  public $incrementalRefreshInterval;
  /**
   * Optional. Indicates whether incremental syncs are paused for this
   * connector. This is independent of auto_run_disabled. Applicable to only 3P
   * connectors. When the refresh interval is set to the same value as the
   * incremental refresh interval, incremental sync will be disabled, i.e. set
   * to true.
   *
   * @var bool
   */
  public $incrementalSyncDisabled;
  /**
   * Required data connector parameters in json string format.
   *
   * @var string
   */
  public $jsonParams;
  /**
   * Input only. The KMS key to be used to protect the DataStores managed by
   * this connector. Must be set for requests that need to comply with CMEK Org
   * Policy protections. If this field is set and processed successfully, the
   * DataStores created by this connector will be protected by the KMS key.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. For periodic connectors only, the last time a data sync was
   * completed.
   *
   * @var string
   */
  public $lastSyncTime;
  /**
   * Output only. The most recent timestamp when this DataConnector was paused,
   * affecting all functionalities such as data synchronization. Pausing a
   * connector has the following effects: - All functionalities, including data
   * synchronization, are halted. - Any ongoing data synchronization job will be
   * canceled. - No future data synchronization runs will be scheduled nor can
   * be triggered.
   *
   * @var string
   */
  public $latestPauseTime;
  /**
   * Output only. The full resource name of the Data Connector. Format:
   * `projects/locations/collections/dataConnector`.
   *
   * @var string
   */
  public $name;
  protected $nextSyncTimeType = GoogleTypeDateTime::class;
  protected $nextSyncTimeDataType = '';
  /**
   * Required data connector parameters in structured json format.
   *
   * @var array[]
   */
  public $params;
  /**
   * Output only. The tenant project ID associated with private connectivity
   * connectors. This project must be allowlisted by in order for the connector
   * to function.
   *
   * @var string
   */
  public $privateConnectivityProjectId;
  /**
   * Output only. real-time sync state
   *
   * @var string
   */
  public $realtimeState;
  protected $realtimeSyncConfigType = GoogleCloudDiscoveryengineV1alphaDataConnectorRealtimeSyncConfig::class;
  protected $realtimeSyncConfigDataType = '';
  /**
   * Required. The refresh interval for data sync. If duration is set to 0, the
   * data will be synced in real time. The streaming feature is not supported
   * yet. The minimum is 30 minutes and maximum is 7 days. When the refresh
   * interval is set to the same value as the incremental refresh interval,
   * incremental sync will be disabled.
   *
   * @var string
   */
  public $refreshInterval;
  /**
   * Optional. Specifies keys to be removed from the 'params' field. This is
   * only active when 'params' is included in the 'update_mask' in an
   * UpdateDataConnectorRequest. Deletion takes precedence if a key is both in
   * 'remove_param_keys' and present in the 'params' field of the request.
   *
   * @var string[]
   */
  public $removeParamKeys;
  /**
   * Output only. State of the connector.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The static IP addresses used by this connector.
   *
   * @var string[]
   */
  public $staticIpAddresses;
  /**
   * Optional. Whether customer has enabled static IP addresses for this
   * connector.
   *
   * @var bool
   */
  public $staticIpEnabled;
  /**
   * The data synchronization mode supported by the data connector.
   *
   * @var string
   */
  public $syncMode;
  /**
   * Output only. Timestamp the DataConnector was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Whether the connector will be created with an ACL config.
   * Currently this field only affects Cloud Storage and BigQuery connectors.
   *
   * @param bool $aclEnabled
   */
  public function setAclEnabled($aclEnabled)
  {
    $this->aclEnabled = $aclEnabled;
  }
  /**
   * @return bool
   */
  public function getAclEnabled()
  {
    return $this->aclEnabled;
  }
  /**
   * Optional. Action configurations to make the connector support actions.
   *
   * @param GoogleCloudDiscoveryengineV1alphaActionConfig $actionConfig
   */
  public function setActionConfig(GoogleCloudDiscoveryengineV1alphaActionConfig $actionConfig)
  {
    $this->actionConfig = $actionConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaActionConfig
   */
  public function getActionConfig()
  {
    return $this->actionConfig;
  }
  /**
   * Output only. State of the action connector. This reflects whether the
   * action connector is initializing, active or has encountered errors.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, RUNNING,
   * WARNING, INITIALIZATION_FAILED, UPDATING
   *
   * @param self::ACTION_STATE_* $actionState
   */
  public function setActionState($actionState)
  {
    $this->actionState = $actionState;
  }
  /**
   * @return self::ACTION_STATE_*
   */
  public function getActionState()
  {
    return $this->actionState;
  }
  /**
   * Optional. The connector level alert config.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAlertPolicyConfig[] $alertPolicyConfigs
   */
  public function setAlertPolicyConfigs($alertPolicyConfigs)
  {
    $this->alertPolicyConfigs = $alertPolicyConfigs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAlertPolicyConfig[]
   */
  public function getAlertPolicyConfigs()
  {
    return $this->alertPolicyConfigs;
  }
  /**
   * Optional. Indicates whether the connector is disabled for auto run. It can
   * be used to pause periodical and real time sync. Update: with the
   * introduction of incremental_sync_disabled, auto_run_disabled is used to
   * pause/disable only full syncs
   *
   * @param bool $autoRunDisabled
   */
  public function setAutoRunDisabled($autoRunDisabled)
  {
    $this->autoRunDisabled = $autoRunDisabled;
  }
  /**
   * @return bool
   */
  public function getAutoRunDisabled()
  {
    return $this->autoRunDisabled;
  }
  /**
   * Optional. The configuration for establishing a BAP connection.
   *
   * @param GoogleCloudDiscoveryengineV1alphaBAPConfig $bapConfig
   */
  public function setBapConfig(GoogleCloudDiscoveryengineV1alphaBAPConfig $bapConfig)
  {
    $this->bapConfig = $bapConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaBAPConfig
   */
  public function getBapConfig()
  {
    return $this->bapConfig;
  }
  /**
   * Output only. User actions that must be completed before the connector can
   * start syncing data.
   *
   * @param string[] $blockingReasons
   */
  public function setBlockingReasons($blockingReasons)
  {
    $this->blockingReasons = $blockingReasons;
  }
  /**
   * @return string[]
   */
  public function getBlockingReasons()
  {
    return $this->blockingReasons;
  }
  /**
   * Optional. The modes enabled for this connector. Default state is
   * CONNECTOR_MODE_UNSPECIFIED.
   *
   * @param string[] $connectorModes
   */
  public function setConnectorModes($connectorModes)
  {
    $this->connectorModes = $connectorModes;
  }
  /**
   * @return string[]
   */
  public function getConnectorModes()
  {
    return $this->connectorModes;
  }
  /**
   * Output only. The type of connector. Each source can only map to one type.
   * For example, salesforce, confluence and jira have THIRD_PARTY connector
   * type. It is not mutable once set by system.
   *
   * Accepted values: CONNECTOR_TYPE_UNSPECIFIED, THIRD_PARTY, GCP_FHIR,
   * BIG_QUERY, GCS, GOOGLE_MAIL, GOOGLE_CALENDAR, GOOGLE_DRIVE,
   * NATIVE_CLOUD_IDENTITY, THIRD_PARTY_FEDERATED, THIRD_PARTY_EUA, GCNV
   *
   * @param self::CONNECTOR_TYPE_* $connectorType
   */
  public function setConnectorType($connectorType)
  {
    $this->connectorType = $connectorType;
  }
  /**
   * @return self::CONNECTOR_TYPE_*
   */
  public function getConnectorType()
  {
    return $this->connectorType;
  }
  /**
   * Optional. Whether the END USER AUTHENTICATION connector is created in SaaS.
   *
   * @param bool $createEuaSaas
   */
  public function setCreateEuaSaas($createEuaSaas)
  {
    $this->createEuaSaas = $createEuaSaas;
  }
  /**
   * @return bool
   */
  public function getCreateEuaSaas()
  {
    return $this->createEuaSaas;
  }
  /**
   * Output only. Timestamp the DataConnector was created at.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The name of the data source. Supported values: `salesforce`,
   * `jira`, `confluence`, `bigquery`.
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Optional. Any target destinations used to connect to third-party services.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDestinationConfig[] $destinationConfigs
   */
  public function setDestinationConfigs($destinationConfigs)
  {
    $this->destinationConfigs = $destinationConfigs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDestinationConfig[]
   */
  public function getDestinationConfigs()
  {
    return $this->destinationConfigs;
  }
  /**
   * Optional. Any params and credentials used specifically for EUA connectors.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataConnectorEndUserConfig $endUserConfig
   */
  public function setEndUserConfig(GoogleCloudDiscoveryengineV1alphaDataConnectorEndUserConfig $endUserConfig)
  {
    $this->endUserConfig = $endUserConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataConnectorEndUserConfig
   */
  public function getEndUserConfig()
  {
    return $this->endUserConfig;
  }
  /**
   * List of entities from the connected data source to ingest.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataConnectorSourceEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataConnectorSourceEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Output only. The errors from initialization or from the latest connector
   * run.
   *
   * @param GoogleRpcStatus[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Optional. Any params and credentials used specifically for hybrid
   * connectors supporting FEDERATED mode. This field should only be set if the
   * connector is a hybrid connector and we want to enable FEDERATED mode.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig $federatedConfig
   */
  public function setFederatedConfig(GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig $federatedConfig)
  {
    $this->federatedConfig = $federatedConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig
   */
  public function getFederatedConfig()
  {
    return $this->federatedConfig;
  }
  /**
   * Optional. If the connector is a hybrid connector, determines whether
   * ingestion is enabled and appropriate resources are provisioned during
   * connector creation. If the connector is not a hybrid connector, this field
   * is ignored.
   *
   * @param bool $hybridIngestionDisabled
   */
  public function setHybridIngestionDisabled($hybridIngestionDisabled)
  {
    $this->hybridIngestionDisabled = $hybridIngestionDisabled;
  }
  /**
   * @return bool
   */
  public function getHybridIngestionDisabled()
  {
    return $this->hybridIngestionDisabled;
  }
  /**
   * The refresh interval to sync the Access Control List information for the
   * documents ingested by this connector. If not set, the access control list
   * will be refreshed at the default interval of 30 minutes. The identity
   * refresh interval can be at least 30 minutes and at most 7 days.
   *
   * @deprecated
   * @param string $identityRefreshInterval
   */
  public function setIdentityRefreshInterval($identityRefreshInterval)
  {
    $this->identityRefreshInterval = $identityRefreshInterval;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getIdentityRefreshInterval()
  {
    return $this->identityRefreshInterval;
  }
  /**
   * The configuration for the identity data synchronization runs. This contains
   * the refresh interval to sync the Access Control List information for the
   * documents ingested by this connector.
   *
   * @param GoogleCloudDiscoveryengineV1alphaIdentityScheduleConfig $identityScheduleConfig
   */
  public function setIdentityScheduleConfig(GoogleCloudDiscoveryengineV1alphaIdentityScheduleConfig $identityScheduleConfig)
  {
    $this->identityScheduleConfig = $identityScheduleConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaIdentityScheduleConfig
   */
  public function getIdentityScheduleConfig()
  {
    return $this->identityScheduleConfig;
  }
  /**
   * Optional. The refresh interval specifically for incremental data syncs. If
   * unset, incremental syncs will use the default from env, set to 3hrs. The
   * minimum is 30 minutes and maximum is 7 days. Applicable to only 3P
   * connectors. When the refresh interval is set to the same value as the
   * incremental refresh interval, incremental sync will be disabled.
   *
   * @param string $incrementalRefreshInterval
   */
  public function setIncrementalRefreshInterval($incrementalRefreshInterval)
  {
    $this->incrementalRefreshInterval = $incrementalRefreshInterval;
  }
  /**
   * @return string
   */
  public function getIncrementalRefreshInterval()
  {
    return $this->incrementalRefreshInterval;
  }
  /**
   * Optional. Indicates whether incremental syncs are paused for this
   * connector. This is independent of auto_run_disabled. Applicable to only 3P
   * connectors. When the refresh interval is set to the same value as the
   * incremental refresh interval, incremental sync will be disabled, i.e. set
   * to true.
   *
   * @param bool $incrementalSyncDisabled
   */
  public function setIncrementalSyncDisabled($incrementalSyncDisabled)
  {
    $this->incrementalSyncDisabled = $incrementalSyncDisabled;
  }
  /**
   * @return bool
   */
  public function getIncrementalSyncDisabled()
  {
    return $this->incrementalSyncDisabled;
  }
  /**
   * Required data connector parameters in json string format.
   *
   * @param string $jsonParams
   */
  public function setJsonParams($jsonParams)
  {
    $this->jsonParams = $jsonParams;
  }
  /**
   * @return string
   */
  public function getJsonParams()
  {
    return $this->jsonParams;
  }
  /**
   * Input only. The KMS key to be used to protect the DataStores managed by
   * this connector. Must be set for requests that need to comply with CMEK Org
   * Policy protections. If this field is set and processed successfully, the
   * DataStores created by this connector will be protected by the KMS key.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Output only. For periodic connectors only, the last time a data sync was
   * completed.
   *
   * @param string $lastSyncTime
   */
  public function setLastSyncTime($lastSyncTime)
  {
    $this->lastSyncTime = $lastSyncTime;
  }
  /**
   * @return string
   */
  public function getLastSyncTime()
  {
    return $this->lastSyncTime;
  }
  /**
   * Output only. The most recent timestamp when this DataConnector was paused,
   * affecting all functionalities such as data synchronization. Pausing a
   * connector has the following effects: - All functionalities, including data
   * synchronization, are halted. - Any ongoing data synchronization job will be
   * canceled. - No future data synchronization runs will be scheduled nor can
   * be triggered.
   *
   * @param string $latestPauseTime
   */
  public function setLatestPauseTime($latestPauseTime)
  {
    $this->latestPauseTime = $latestPauseTime;
  }
  /**
   * @return string
   */
  public function getLatestPauseTime()
  {
    return $this->latestPauseTime;
  }
  /**
   * Output only. The full resource name of the Data Connector. Format:
   * `projects/locations/collections/dataConnector`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Defines the scheduled time for the next data synchronization. This field
   * requires hour , minute, and time_zone from the [IANA Time Zone
   * Database](https://www.iana.org/time-zones). This is utilized when the data
   * connector has a refresh interval greater than 1 day. When the hours or
   * minutes are not specified, we will assume a sync time of 0:00. The user
   * must provide a time zone to avoid ambiguity.
   *
   * @param GoogleTypeDateTime $nextSyncTime
   */
  public function setNextSyncTime(GoogleTypeDateTime $nextSyncTime)
  {
    $this->nextSyncTime = $nextSyncTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getNextSyncTime()
  {
    return $this->nextSyncTime;
  }
  /**
   * Required data connector parameters in structured json format.
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Output only. The tenant project ID associated with private connectivity
   * connectors. This project must be allowlisted by in order for the connector
   * to function.
   *
   * @param string $privateConnectivityProjectId
   */
  public function setPrivateConnectivityProjectId($privateConnectivityProjectId)
  {
    $this->privateConnectivityProjectId = $privateConnectivityProjectId;
  }
  /**
   * @return string
   */
  public function getPrivateConnectivityProjectId()
  {
    return $this->privateConnectivityProjectId;
  }
  /**
   * Output only. real-time sync state
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, RUNNING,
   * WARNING, INITIALIZATION_FAILED, UPDATING
   *
   * @param self::REALTIME_STATE_* $realtimeState
   */
  public function setRealtimeState($realtimeState)
  {
    $this->realtimeState = $realtimeState;
  }
  /**
   * @return self::REALTIME_STATE_*
   */
  public function getRealtimeState()
  {
    return $this->realtimeState;
  }
  /**
   * Optional. The configuration for realtime sync.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataConnectorRealtimeSyncConfig $realtimeSyncConfig
   */
  public function setRealtimeSyncConfig(GoogleCloudDiscoveryengineV1alphaDataConnectorRealtimeSyncConfig $realtimeSyncConfig)
  {
    $this->realtimeSyncConfig = $realtimeSyncConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataConnectorRealtimeSyncConfig
   */
  public function getRealtimeSyncConfig()
  {
    return $this->realtimeSyncConfig;
  }
  /**
   * Required. The refresh interval for data sync. If duration is set to 0, the
   * data will be synced in real time. The streaming feature is not supported
   * yet. The minimum is 30 minutes and maximum is 7 days. When the refresh
   * interval is set to the same value as the incremental refresh interval,
   * incremental sync will be disabled.
   *
   * @param string $refreshInterval
   */
  public function setRefreshInterval($refreshInterval)
  {
    $this->refreshInterval = $refreshInterval;
  }
  /**
   * @return string
   */
  public function getRefreshInterval()
  {
    return $this->refreshInterval;
  }
  /**
   * Optional. Specifies keys to be removed from the 'params' field. This is
   * only active when 'params' is included in the 'update_mask' in an
   * UpdateDataConnectorRequest. Deletion takes precedence if a key is both in
   * 'remove_param_keys' and present in the 'params' field of the request.
   *
   * @param string[] $removeParamKeys
   */
  public function setRemoveParamKeys($removeParamKeys)
  {
    $this->removeParamKeys = $removeParamKeys;
  }
  /**
   * @return string[]
   */
  public function getRemoveParamKeys()
  {
    return $this->removeParamKeys;
  }
  /**
   * Output only. State of the connector.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, FAILED, RUNNING,
   * WARNING, INITIALIZATION_FAILED, UPDATING
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
  /**
   * Output only. The static IP addresses used by this connector.
   *
   * @param string[] $staticIpAddresses
   */
  public function setStaticIpAddresses($staticIpAddresses)
  {
    $this->staticIpAddresses = $staticIpAddresses;
  }
  /**
   * @return string[]
   */
  public function getStaticIpAddresses()
  {
    return $this->staticIpAddresses;
  }
  /**
   * Optional. Whether customer has enabled static IP addresses for this
   * connector.
   *
   * @param bool $staticIpEnabled
   */
  public function setStaticIpEnabled($staticIpEnabled)
  {
    $this->staticIpEnabled = $staticIpEnabled;
  }
  /**
   * @return bool
   */
  public function getStaticIpEnabled()
  {
    return $this->staticIpEnabled;
  }
  /**
   * The data synchronization mode supported by the data connector.
   *
   * Accepted values: PERIODIC, STREAMING, UNSPECIFIED
   *
   * @param self::SYNC_MODE_* $syncMode
   */
  public function setSyncMode($syncMode)
  {
    $this->syncMode = $syncMode;
  }
  /**
   * @return self::SYNC_MODE_*
   */
  public function getSyncMode()
  {
    return $this->syncMode;
  }
  /**
   * Output only. Timestamp the DataConnector was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDataConnector::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDataConnector');
