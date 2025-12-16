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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaIntegrationVersion extends \Google\Collection
{
  /**
   * Enables persistence for all execution data.
   */
  public const DATABASE_PERSISTENCE_POLICY_DATABASE_PERSISTENCE_POLICY_UNSPECIFIED = 'DATABASE_PERSISTENCE_POLICY_UNSPECIFIED';
  /**
   * Disables persistence for all execution data.
   */
  public const DATABASE_PERSISTENCE_POLICY_DATABASE_PERSISTENCE_DISABLED = 'DATABASE_PERSISTENCE_DISABLED';
  /**
   * Asynchronously persist all execution data.
   */
  public const DATABASE_PERSISTENCE_POLICY_DATABASE_PERSISTENCE_ASYNC = 'DATABASE_PERSISTENCE_ASYNC';
  public const ORIGIN_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Workflow is being created via event bus UI.
   */
  public const ORIGIN_UI = 'UI';
  /**
   * User checked in this workflow in Piper as v2 textproto format and we synced
   * it into spanner.
   *
   * @deprecated
   */
  public const ORIGIN_PIPER_V2 = 'PIPER_V2';
  /**
   * User checked in this workflow in piper as v3 textproto format and we synced
   * it into spanner.
   */
  public const ORIGIN_PIPER_V3 = 'PIPER_V3';
  /**
   * Workflow is being created via Standalone IP Provisioning
   */
  public const ORIGIN_APPLICATION_IP_PROVISIONING = 'APPLICATION_IP_PROVISIONING';
  /**
   * Workflow is being created via Test Case.
   */
  public const ORIGIN_TEST_CASE = 'TEST_CASE';
  /**
   * Default.
   */
  public const STATE_INTEGRATION_STATE_UNSPECIFIED = 'INTEGRATION_STATE_UNSPECIFIED';
  /**
   * Draft.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * Active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Archived.
   */
  public const STATE_ARCHIVED = 'ARCHIVED';
  /**
   * Snapshot.
   */
  public const STATE_SNAPSHOT = 'SNAPSHOT';
  public const STATUS_UNKNOWN = 'UNKNOWN';
  public const STATUS_DRAFT = 'DRAFT';
  public const STATUS_ACTIVE = 'ACTIVE';
  public const STATUS_ARCHIVED = 'ARCHIVED';
  public const STATUS_SNAPSHOT = 'SNAPSHOT';
  protected $collection_key = 'triggerConfigsInternal';
  /**
   * Optional. Cloud KMS resource name for the CMEK encryption key.
   *
   * @var string
   */
  public $cloudKmsKey;
  protected $cloudLoggingDetailsType = GoogleCloudIntegrationsV1alphaCloudLoggingDetails::class;
  protected $cloudLoggingDetailsDataType = '';
  /**
   * Output only. Auto-generated.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Optional. The resource name of the template from which the
   * integration is created.
   *
   * @var string
   */
  public $createdFromTemplate;
  /**
   * Optional. Flag to disable database persistence for execution data,
   * including event execution info, execution export info, execution metadata
   * index and execution param index.
   *
   * @var string
   */
  public $databasePersistencePolicy;
  /**
   * Optional. The integration description.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. True if variable masking feature should be turned on for this
   * version
   *
   * @var bool
   */
  public $enableVariableMasking;
  protected $errorCatcherConfigsType = GoogleCloudIntegrationsV1alphaErrorCatcherConfig::class;
  protected $errorCatcherConfigsDataType = 'array';
  protected $integrationConfigParametersType = GoogleCloudIntegrationsV1alphaIntegrationConfigParameter::class;
  protected $integrationConfigParametersDataType = 'array';
  protected $integrationParametersType = GoogleCloudIntegrationsV1alphaIntegrationParameter::class;
  protected $integrationParametersDataType = 'array';
  protected $integrationParametersInternalType = EnterpriseCrmFrontendsEventbusProtoWorkflowParameters::class;
  protected $integrationParametersInternalDataType = '';
  /**
   * Optional. The last modifier's email address. Generated based on the End
   * User Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $lastModifierEmail;
  /**
   * Optional. The edit lock holder's email address. Generated based on the End
   * User Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $lockHolder;
  /**
   * Output only. Auto-generated primary key.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The origin that indicates where this integration is coming from.
   *
   * @deprecated
   * @var string
   */
  public $origin;
  /**
   * Optional. The id of the template which was used to create this
   * integration_version.
   *
   * @var string
   */
  public $parentTemplateId;
  /**
   * Optional. The run-as service account email, if set and auth config is not
   * configured, that will be used to generate auth token to be used in
   * Connector task, Rest caller task and Cloud function task.
   *
   * @var string
   */
  public $runAsServiceAccount;
  /**
   * Output only. An increasing sequence that is set when a new snapshot is
   * created. The last created snapshot can be identified by [workflow_name,
   * org_id latest(snapshot_number)]. However, last created snapshot need not be
   * same as the HEAD. So users should always use "HEAD" tag to identify the
   * head.
   *
   * @var string
   */
  public $snapshotNumber;
  /**
   * Output only. User should not set it as an input.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Generated by eventbus. User should not set it as an input.
   *
   * @deprecated
   * @var string
   */
  public $status;
  protected $taskConfigsType = GoogleCloudIntegrationsV1alphaTaskConfig::class;
  protected $taskConfigsDataType = 'array';
  protected $taskConfigsInternalType = EnterpriseCrmFrontendsEventbusProtoTaskConfig::class;
  protected $taskConfigsInternalDataType = 'array';
  protected $teardownType = EnterpriseCrmEventbusProtoTeardown::class;
  protected $teardownDataType = '';
  protected $triggerConfigsType = GoogleCloudIntegrationsV1alphaTriggerConfig::class;
  protected $triggerConfigsDataType = 'array';
  protected $triggerConfigsInternalType = EnterpriseCrmFrontendsEventbusProtoTriggerConfig::class;
  protected $triggerConfigsInternalDataType = 'array';
  /**
   * Output only. Auto-generated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. A user-defined label that annotates an integration version.
   * Typically, this is only set when the integration version is created.
   *
   * @var string
   */
  public $userLabel;

  /**
   * Optional. Cloud KMS resource name for the CMEK encryption key.
   *
   * @param string $cloudKmsKey
   */
  public function setCloudKmsKey($cloudKmsKey)
  {
    $this->cloudKmsKey = $cloudKmsKey;
  }
  /**
   * @return string
   */
  public function getCloudKmsKey()
  {
    return $this->cloudKmsKey;
  }
  /**
   * Optional. Cloud Logging details for the integration version
   *
   * @param GoogleCloudIntegrationsV1alphaCloudLoggingDetails $cloudLoggingDetails
   */
  public function setCloudLoggingDetails(GoogleCloudIntegrationsV1alphaCloudLoggingDetails $cloudLoggingDetails)
  {
    $this->cloudLoggingDetails = $cloudLoggingDetails;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaCloudLoggingDetails
   */
  public function getCloudLoggingDetails()
  {
    return $this->cloudLoggingDetails;
  }
  /**
   * Output only. Auto-generated.
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
   * Optional. Optional. The resource name of the template from which the
   * integration is created.
   *
   * @param string $createdFromTemplate
   */
  public function setCreatedFromTemplate($createdFromTemplate)
  {
    $this->createdFromTemplate = $createdFromTemplate;
  }
  /**
   * @return string
   */
  public function getCreatedFromTemplate()
  {
    return $this->createdFromTemplate;
  }
  /**
   * Optional. Flag to disable database persistence for execution data,
   * including event execution info, execution export info, execution metadata
   * index and execution param index.
   *
   * Accepted values: DATABASE_PERSISTENCE_POLICY_UNSPECIFIED,
   * DATABASE_PERSISTENCE_DISABLED, DATABASE_PERSISTENCE_ASYNC
   *
   * @param self::DATABASE_PERSISTENCE_POLICY_* $databasePersistencePolicy
   */
  public function setDatabasePersistencePolicy($databasePersistencePolicy)
  {
    $this->databasePersistencePolicy = $databasePersistencePolicy;
  }
  /**
   * @return self::DATABASE_PERSISTENCE_POLICY_*
   */
  public function getDatabasePersistencePolicy()
  {
    return $this->databasePersistencePolicy;
  }
  /**
   * Optional. The integration description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. True if variable masking feature should be turned on for this
   * version
   *
   * @param bool $enableVariableMasking
   */
  public function setEnableVariableMasking($enableVariableMasking)
  {
    $this->enableVariableMasking = $enableVariableMasking;
  }
  /**
   * @return bool
   */
  public function getEnableVariableMasking()
  {
    return $this->enableVariableMasking;
  }
  /**
   * Optional. Error Catch Task configuration for the integration. It's
   * optional.
   *
   * @param GoogleCloudIntegrationsV1alphaErrorCatcherConfig[] $errorCatcherConfigs
   */
  public function setErrorCatcherConfigs($errorCatcherConfigs)
  {
    $this->errorCatcherConfigs = $errorCatcherConfigs;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaErrorCatcherConfig[]
   */
  public function getErrorCatcherConfigs()
  {
    return $this->errorCatcherConfigs;
  }
  /**
   * Optional. Config Parameters that are expected to be passed to the
   * integration when an integration is published. This consists of all the
   * parameters that are expected to provide configuration in the integration
   * execution. This gives the user the ability to provide default values,
   * value, add information like connection url, project based configuration
   * value and also provide data types of each parameter.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationConfigParameter[] $integrationConfigParameters
   */
  public function setIntegrationConfigParameters($integrationConfigParameters)
  {
    $this->integrationConfigParameters = $integrationConfigParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationConfigParameter[]
   */
  public function getIntegrationConfigParameters()
  {
    return $this->integrationConfigParameters;
  }
  /**
   * Optional. Parameters that are expected to be passed to the integration when
   * an event is triggered. This consists of all the parameters that are
   * expected in the integration execution. This gives the user the ability to
   * provide default values, add information like PII and also provide data
   * types of each parameter.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationParameter[] $integrationParameters
   */
  public function setIntegrationParameters($integrationParameters)
  {
    $this->integrationParameters = $integrationParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationParameter[]
   */
  public function getIntegrationParameters()
  {
    return $this->integrationParameters;
  }
  /**
   * Optional. Parameters that are expected to be passed to the integration when
   * an event is triggered. This consists of all the parameters that are
   * expected in the integration execution. This gives the user the ability to
   * provide default values, add information like PII and also provide data
   * types of each parameter.
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoWorkflowParameters $integrationParametersInternal
   */
  public function setIntegrationParametersInternal(EnterpriseCrmFrontendsEventbusProtoWorkflowParameters $integrationParametersInternal)
  {
    $this->integrationParametersInternal = $integrationParametersInternal;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoWorkflowParameters
   */
  public function getIntegrationParametersInternal()
  {
    return $this->integrationParametersInternal;
  }
  /**
   * Optional. The last modifier's email address. Generated based on the End
   * User Credentials/LOAS role of the user making the call.
   *
   * @param string $lastModifierEmail
   */
  public function setLastModifierEmail($lastModifierEmail)
  {
    $this->lastModifierEmail = $lastModifierEmail;
  }
  /**
   * @return string
   */
  public function getLastModifierEmail()
  {
    return $this->lastModifierEmail;
  }
  /**
   * Optional. The edit lock holder's email address. Generated based on the End
   * User Credentials/LOAS role of the user making the call.
   *
   * @param string $lockHolder
   */
  public function setLockHolder($lockHolder)
  {
    $this->lockHolder = $lockHolder;
  }
  /**
   * @return string
   */
  public function getLockHolder()
  {
    return $this->lockHolder;
  }
  /**
   * Output only. Auto-generated primary key.
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
   * Optional. The origin that indicates where this integration is coming from.
   *
   * Accepted values: UNSPECIFIED, UI, PIPER_V2, PIPER_V3,
   * APPLICATION_IP_PROVISIONING, TEST_CASE
   *
   * @deprecated
   * @param self::ORIGIN_* $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @deprecated
   * @return self::ORIGIN_*
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Optional. The id of the template which was used to create this
   * integration_version.
   *
   * @param string $parentTemplateId
   */
  public function setParentTemplateId($parentTemplateId)
  {
    $this->parentTemplateId = $parentTemplateId;
  }
  /**
   * @return string
   */
  public function getParentTemplateId()
  {
    return $this->parentTemplateId;
  }
  /**
   * Optional. The run-as service account email, if set and auth config is not
   * configured, that will be used to generate auth token to be used in
   * Connector task, Rest caller task and Cloud function task.
   *
   * @param string $runAsServiceAccount
   */
  public function setRunAsServiceAccount($runAsServiceAccount)
  {
    $this->runAsServiceAccount = $runAsServiceAccount;
  }
  /**
   * @return string
   */
  public function getRunAsServiceAccount()
  {
    return $this->runAsServiceAccount;
  }
  /**
   * Output only. An increasing sequence that is set when a new snapshot is
   * created. The last created snapshot can be identified by [workflow_name,
   * org_id latest(snapshot_number)]. However, last created snapshot need not be
   * same as the HEAD. So users should always use "HEAD" tag to identify the
   * head.
   *
   * @param string $snapshotNumber
   */
  public function setSnapshotNumber($snapshotNumber)
  {
    $this->snapshotNumber = $snapshotNumber;
  }
  /**
   * @return string
   */
  public function getSnapshotNumber()
  {
    return $this->snapshotNumber;
  }
  /**
   * Output only. User should not set it as an input.
   *
   * Accepted values: INTEGRATION_STATE_UNSPECIFIED, DRAFT, ACTIVE, ARCHIVED,
   * SNAPSHOT
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
   * Output only. Generated by eventbus. User should not set it as an input.
   *
   * Accepted values: UNKNOWN, DRAFT, ACTIVE, ARCHIVED, SNAPSHOT
   *
   * @deprecated
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @deprecated
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Optional. Task configuration for the integration. It's optional, but the
   * integration doesn't do anything without task_configs.
   *
   * @param GoogleCloudIntegrationsV1alphaTaskConfig[] $taskConfigs
   */
  public function setTaskConfigs($taskConfigs)
  {
    $this->taskConfigs = $taskConfigs;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTaskConfig[]
   */
  public function getTaskConfigs()
  {
    return $this->taskConfigs;
  }
  /**
   * Optional. Task configuration for the integration. It's optional, but the
   * integration doesn't do anything without task_configs.
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoTaskConfig[] $taskConfigsInternal
   */
  public function setTaskConfigsInternal($taskConfigsInternal)
  {
    $this->taskConfigsInternal = $taskConfigsInternal;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoTaskConfig[]
   */
  public function getTaskConfigsInternal()
  {
    return $this->taskConfigsInternal;
  }
  /**
   * Optional. Contains a graph of tasks that will be executed before putting
   * the event in a terminal state (SUCCEEDED/FAILED/FATAL), regardless of
   * success or failure, similar to "finally" in code.
   *
   * @deprecated
   * @param EnterpriseCrmEventbusProtoTeardown $teardown
   */
  public function setTeardown(EnterpriseCrmEventbusProtoTeardown $teardown)
  {
    $this->teardown = $teardown;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmEventbusProtoTeardown
   */
  public function getTeardown()
  {
    return $this->teardown;
  }
  /**
   * Optional. Trigger configurations.
   *
   * @param GoogleCloudIntegrationsV1alphaTriggerConfig[] $triggerConfigs
   */
  public function setTriggerConfigs($triggerConfigs)
  {
    $this->triggerConfigs = $triggerConfigs;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTriggerConfig[]
   */
  public function getTriggerConfigs()
  {
    return $this->triggerConfigs;
  }
  /**
   * Optional. Trigger configurations.
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoTriggerConfig[] $triggerConfigsInternal
   */
  public function setTriggerConfigsInternal($triggerConfigsInternal)
  {
    $this->triggerConfigsInternal = $triggerConfigsInternal;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoTriggerConfig[]
   */
  public function getTriggerConfigsInternal()
  {
    return $this->triggerConfigsInternal;
  }
  /**
   * Output only. Auto-generated.
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
  /**
   * Optional. A user-defined label that annotates an integration version.
   * Typically, this is only set when the integration version is created.
   *
   * @param string $userLabel
   */
  public function setUserLabel($userLabel)
  {
    $this->userLabel = $userLabel;
  }
  /**
   * @return string
   */
  public function getUserLabel()
  {
    return $this->userLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaIntegrationVersion::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaIntegrationVersion');
