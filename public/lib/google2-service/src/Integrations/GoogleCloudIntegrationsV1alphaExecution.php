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

class GoogleCloudIntegrationsV1alphaExecution extends \Google\Collection
{
  /**
   * Default value.
   */
  public const EXECUTION_METHOD_EXECUTION_METHOD_UNSPECIFIED = 'EXECUTION_METHOD_UNSPECIFIED';
  /**
   * Sync post.
   */
  public const EXECUTION_METHOD_POST = 'POST';
  /**
   * Async post.
   */
  public const EXECUTION_METHOD_POST_TO_QUEUE = 'POST_TO_QUEUE';
  /**
   * Async post with schedule time.
   */
  public const EXECUTION_METHOD_SCHEDULE = 'SCHEDULE';
  /**
   * Default.
   */
  public const INTEGRATION_VERSION_STATE_INTEGRATION_STATE_UNSPECIFIED = 'INTEGRATION_STATE_UNSPECIFIED';
  /**
   * Draft.
   */
  public const INTEGRATION_VERSION_STATE_DRAFT = 'DRAFT';
  /**
   * Active.
   */
  public const INTEGRATION_VERSION_STATE_ACTIVE = 'ACTIVE';
  /**
   * Archived.
   */
  public const INTEGRATION_VERSION_STATE_ARCHIVED = 'ARCHIVED';
  /**
   * Snapshot.
   */
  public const INTEGRATION_VERSION_STATE_SNAPSHOT = 'SNAPSHOT';
  protected $collection_key = 'responseParams';
  /**
   * Optional. Cloud KMS resource name for the CMEK encryption key.
   *
   * @var string
   */
  public $cloudKmsKey;
  protected $cloudLoggingDetailsType = GoogleCloudIntegrationsV1alphaCloudLoggingDetails::class;
  protected $cloudLoggingDetailsDataType = '';
  /**
   * Output only. Created time of the execution.
   *
   * @var string
   */
  public $createTime;
  protected $directSubExecutionsType = GoogleCloudIntegrationsV1alphaExecution::class;
  protected $directSubExecutionsDataType = 'array';
  protected $eventExecutionDetailsType = EnterpriseCrmEventbusProtoEventExecutionDetails::class;
  protected $eventExecutionDetailsDataType = '';
  protected $executionDetailsType = GoogleCloudIntegrationsV1alphaExecutionDetails::class;
  protected $executionDetailsDataType = '';
  /**
   * The ways user posts this event.
   *
   * @var string
   */
  public $executionMethod;
  /**
   * Output only. State of the integration version
   *
   * @var string
   */
  public $integrationVersionState;
  /**
   * Auto-generated primary key.
   *
   * @var string
   */
  public $name;
  protected $replayInfoType = GoogleCloudIntegrationsV1alphaExecutionReplayInfo::class;
  protected $replayInfoDataType = '';
  protected $requestParametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $requestParametersDataType = 'map';
  protected $requestParamsType = EnterpriseCrmFrontendsEventbusProtoParameterEntry::class;
  protected $requestParamsDataType = 'array';
  protected $responseParametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $responseParametersDataType = 'map';
  protected $responseParamsType = EnterpriseCrmFrontendsEventbusProtoParameterEntry::class;
  protected $responseParamsDataType = 'array';
  /**
   * Output only. An increasing sequence that is set when a new snapshot is
   * created
   *
   * @var string
   */
  public $snapshotNumber;
  /**
   * The trigger id of the integration trigger config. If both trigger_id and
   * client_id is present, the integration is executed from the start tasks
   * provided by the matching trigger config otherwise it is executed from the
   * default start tasks.
   *
   * @var string
   */
  public $triggerId;
  /**
   * Output only. Last modified time of the execution.
   *
   * @var string
   */
  public $updateTime;

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
   * Cloud Logging details for the integration version
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
   * Output only. Created time of the execution.
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
   * Direct sub executions of the following Execution.
   *
   * @param GoogleCloudIntegrationsV1alphaExecution[] $directSubExecutions
   */
  public function setDirectSubExecutions($directSubExecutions)
  {
    $this->directSubExecutions = $directSubExecutions;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaExecution[]
   */
  public function getDirectSubExecutions()
  {
    return $this->directSubExecutions;
  }
  /**
   * The execution info about this event.
   *
   * @deprecated
   * @param EnterpriseCrmEventbusProtoEventExecutionDetails $eventExecutionDetails
   */
  public function setEventExecutionDetails(EnterpriseCrmEventbusProtoEventExecutionDetails $eventExecutionDetails)
  {
    $this->eventExecutionDetails = $eventExecutionDetails;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmEventbusProtoEventExecutionDetails
   */
  public function getEventExecutionDetails()
  {
    return $this->eventExecutionDetails;
  }
  /**
   * Detailed info of this execution.
   *
   * @param GoogleCloudIntegrationsV1alphaExecutionDetails $executionDetails
   */
  public function setExecutionDetails(GoogleCloudIntegrationsV1alphaExecutionDetails $executionDetails)
  {
    $this->executionDetails = $executionDetails;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaExecutionDetails
   */
  public function getExecutionDetails()
  {
    return $this->executionDetails;
  }
  /**
   * The ways user posts this event.
   *
   * Accepted values: EXECUTION_METHOD_UNSPECIFIED, POST, POST_TO_QUEUE,
   * SCHEDULE
   *
   * @param self::EXECUTION_METHOD_* $executionMethod
   */
  public function setExecutionMethod($executionMethod)
  {
    $this->executionMethod = $executionMethod;
  }
  /**
   * @return self::EXECUTION_METHOD_*
   */
  public function getExecutionMethod()
  {
    return $this->executionMethod;
  }
  /**
   * Output only. State of the integration version
   *
   * Accepted values: INTEGRATION_STATE_UNSPECIFIED, DRAFT, ACTIVE, ARCHIVED,
   * SNAPSHOT
   *
   * @param self::INTEGRATION_VERSION_STATE_* $integrationVersionState
   */
  public function setIntegrationVersionState($integrationVersionState)
  {
    $this->integrationVersionState = $integrationVersionState;
  }
  /**
   * @return self::INTEGRATION_VERSION_STATE_*
   */
  public function getIntegrationVersionState()
  {
    return $this->integrationVersionState;
  }
  /**
   * Auto-generated primary key.
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
   * Output only. Replay info for the execution
   *
   * @param GoogleCloudIntegrationsV1alphaExecutionReplayInfo $replayInfo
   */
  public function setReplayInfo(GoogleCloudIntegrationsV1alphaExecutionReplayInfo $replayInfo)
  {
    $this->replayInfo = $replayInfo;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaExecutionReplayInfo
   */
  public function getReplayInfo()
  {
    return $this->replayInfo;
  }
  /**
   * Event parameters come in as part of the request.
   *
   * @param GoogleCloudIntegrationsV1alphaValueType[] $requestParameters
   */
  public function setRequestParameters($requestParameters)
  {
    $this->requestParameters = $requestParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaValueType[]
   */
  public function getRequestParameters()
  {
    return $this->requestParameters;
  }
  /**
   * Event parameters come in as part of the request.
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoParameterEntry[] $requestParams
   */
  public function setRequestParams($requestParams)
  {
    $this->requestParams = $requestParams;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoParameterEntry[]
   */
  public function getRequestParams()
  {
    return $this->requestParams;
  }
  /**
   * Event parameters returned as part of the response. In the case of error,
   * the `ErrorInfo` field is returned in the following format: { "ErrorInfo": {
   * "message": String, "code": Number } }
   *
   * @param GoogleCloudIntegrationsV1alphaValueType[] $responseParameters
   */
  public function setResponseParameters($responseParameters)
  {
    $this->responseParameters = $responseParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaValueType[]
   */
  public function getResponseParameters()
  {
    return $this->responseParameters;
  }
  /**
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoParameterEntry[] $responseParams
   */
  public function setResponseParams($responseParams)
  {
    $this->responseParams = $responseParams;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoParameterEntry[]
   */
  public function getResponseParams()
  {
    return $this->responseParams;
  }
  /**
   * Output only. An increasing sequence that is set when a new snapshot is
   * created
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
   * The trigger id of the integration trigger config. If both trigger_id and
   * client_id is present, the integration is executed from the start tasks
   * provided by the matching trigger config otherwise it is executed from the
   * default start tasks.
   *
   * @param string $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
  /**
   * Output only. Last modified time of the execution.
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
class_alias(GoogleCloudIntegrationsV1alphaExecution::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaExecution');
