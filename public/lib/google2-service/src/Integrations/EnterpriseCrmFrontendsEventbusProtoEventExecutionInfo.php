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

class EnterpriseCrmFrontendsEventbusProtoEventExecutionInfo extends \Google\Collection
{
  public const POST_METHOD_UNSPECIFIED = 'UNSPECIFIED';
  public const POST_METHOD_POST = 'POST';
  public const POST_METHOD_POST_TO_QUEUE = 'POST_TO_QUEUE';
  public const POST_METHOD_SCHEDULE = 'SCHEDULE';
  public const POST_METHOD_POST_BY_EVENT_CONFIG_ID = 'POST_BY_EVENT_CONFIG_ID';
  public const POST_METHOD_POST_WITH_EVENT_DETAILS = 'POST_WITH_EVENT_DETAILS';
  public const PRODUCT_UNSPECIFIED_PRODUCT = 'UNSPECIFIED_PRODUCT';
  public const PRODUCT_IP = 'IP';
  public const PRODUCT_APIGEE = 'APIGEE';
  public const PRODUCT_SECURITY = 'SECURITY';
  protected $collection_key = 'errors';
  /**
   * The event data user sends as request.
   *
   * @var string
   */
  public $clientId;
  protected $cloudLoggingDetailsType = EnterpriseCrmEventbusProtoCloudLoggingDetails::class;
  protected $cloudLoggingDetailsDataType = '';
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $createTime;
  protected $errorCodeType = CrmlogErrorCode::class;
  protected $errorCodeDataType = '';
  protected $errorsType = EnterpriseCrmEventbusProtoErrorDetail::class;
  protected $errorsDataType = 'array';
  protected $eventExecutionDetailsType = EnterpriseCrmFrontendsEventbusProtoEventExecutionDetails::class;
  protected $eventExecutionDetailsDataType = '';
  /**
   * Auto-generated primary key.
   *
   * @var string
   */
  public $eventExecutionInfoId;
  protected $executionTraceInfoType = EnterpriseCrmEventbusProtoExecutionTraceInfo::class;
  protected $executionTraceInfoDataType = '';
  /**
   * User-defined label that annotates the executed integration version.
   *
   * @var string
   */
  public $integrationVersionUserLabel;
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * The ways user posts this event.
   *
   * @var string
   */
  public $postMethod;
  /**
   * Which Google product the execution_info belongs to. If not set, the
   * execution_info belongs to Integration Platform by default.
   *
   * @var string
   */
  public $product;
  protected $replayInfoType = EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo::class;
  protected $replayInfoDataType = '';
  /**
   * Optional. This is used to de-dup incoming request.
   *
   * @var string
   */
  public $requestId;
  protected $requestParamsType = EnterpriseCrmFrontendsEventbusProtoEventParameters::class;
  protected $requestParamsDataType = '';
  protected $responseParamsType = EnterpriseCrmFrontendsEventbusProtoEventParameters::class;
  protected $responseParamsDataType = '';
  /**
   * Workflow snapshot number.
   *
   * @var string
   */
  public $snapshotNumber;
  /**
   * Tenant this event is created. Used to reschedule the event to correct
   * tenant.
   *
   * @var string
   */
  public $tenant;
  /**
   * The trigger id of the workflow trigger config. If both trigger_id and
   * client_id is present, the workflow is executed from the start tasks
   * provided by the matching trigger config otherwise it is executed from the
   * default start tasks.
   *
   * @var string
   */
  public $triggerId;
  /**
   * Required. Pointer to the workflow it is executing.
   *
   * @var string
   */
  public $workflowId;
  /**
   * Name of the workflow.
   *
   * @var string
   */
  public $workflowName;
  /**
   * Time interval in seconds to schedule retry of workflow in manifold when
   * workflow is already running
   *
   * @var string
   */
  public $workflowRetryBackoffIntervalSeconds;

  /**
   * The event data user sends as request.
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
   * Cloud Logging details for execution info
   *
   * @param EnterpriseCrmEventbusProtoCloudLoggingDetails $cloudLoggingDetails
   */
  public function setCloudLoggingDetails(EnterpriseCrmEventbusProtoCloudLoggingDetails $cloudLoggingDetails)
  {
    $this->cloudLoggingDetails = $cloudLoggingDetails;
  }
  /**
   * @return EnterpriseCrmEventbusProtoCloudLoggingDetails
   */
  public function getCloudLoggingDetails()
  {
    return $this->cloudLoggingDetails;
  }
  /**
   * Auto-generated.
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
   * Final error-code if event failed.
   *
   * @param CrmlogErrorCode $errorCode
   */
  public function setErrorCode(CrmlogErrorCode $errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return CrmlogErrorCode
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Errors, warnings, and informationals associated with the workflow/task. The
   * order in which the errors were added by the workflow/task is maintained.
   *
   * @param EnterpriseCrmEventbusProtoErrorDetail[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return EnterpriseCrmEventbusProtoErrorDetail[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The execution info about this event.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoEventExecutionDetails $eventExecutionDetails
   */
  public function setEventExecutionDetails(EnterpriseCrmFrontendsEventbusProtoEventExecutionDetails $eventExecutionDetails)
  {
    $this->eventExecutionDetails = $eventExecutionDetails;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoEventExecutionDetails
   */
  public function getEventExecutionDetails()
  {
    return $this->eventExecutionDetails;
  }
  /**
   * Auto-generated primary key.
   *
   * @param string $eventExecutionInfoId
   */
  public function setEventExecutionInfoId($eventExecutionInfoId)
  {
    $this->eventExecutionInfoId = $eventExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getEventExecutionInfoId()
  {
    return $this->eventExecutionInfoId;
  }
  /**
   * Execution trace info to aggregate parent-child executions.
   *
   * @param EnterpriseCrmEventbusProtoExecutionTraceInfo $executionTraceInfo
   */
  public function setExecutionTraceInfo(EnterpriseCrmEventbusProtoExecutionTraceInfo $executionTraceInfo)
  {
    $this->executionTraceInfo = $executionTraceInfo;
  }
  /**
   * @return EnterpriseCrmEventbusProtoExecutionTraceInfo
   */
  public function getExecutionTraceInfo()
  {
    return $this->executionTraceInfo;
  }
  /**
   * User-defined label that annotates the executed integration version.
   *
   * @param string $integrationVersionUserLabel
   */
  public function setIntegrationVersionUserLabel($integrationVersionUserLabel)
  {
    $this->integrationVersionUserLabel = $integrationVersionUserLabel;
  }
  /**
   * @return string
   */
  public function getIntegrationVersionUserLabel()
  {
    return $this->integrationVersionUserLabel;
  }
  /**
   * Auto-generated.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * The ways user posts this event.
   *
   * Accepted values: UNSPECIFIED, POST, POST_TO_QUEUE, SCHEDULE,
   * POST_BY_EVENT_CONFIG_ID, POST_WITH_EVENT_DETAILS
   *
   * @param self::POST_METHOD_* $postMethod
   */
  public function setPostMethod($postMethod)
  {
    $this->postMethod = $postMethod;
  }
  /**
   * @return self::POST_METHOD_*
   */
  public function getPostMethod()
  {
    return $this->postMethod;
  }
  /**
   * Which Google product the execution_info belongs to. If not set, the
   * execution_info belongs to Integration Platform by default.
   *
   * Accepted values: UNSPECIFIED_PRODUCT, IP, APIGEE, SECURITY
   *
   * @param self::PRODUCT_* $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return self::PRODUCT_*
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Replay info for the execution
   *
   * @param EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo $replayInfo
   */
  public function setReplayInfo(EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo $replayInfo)
  {
    $this->replayInfo = $replayInfo;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo
   */
  public function getReplayInfo()
  {
    return $this->replayInfo;
  }
  /**
   * Optional. This is used to de-dup incoming request.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Event parameters come in as part of the request.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoEventParameters $requestParams
   */
  public function setRequestParams(EnterpriseCrmFrontendsEventbusProtoEventParameters $requestParams)
  {
    $this->requestParams = $requestParams;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoEventParameters
   */
  public function getRequestParams()
  {
    return $this->requestParams;
  }
  /**
   * Event parameters come out as part of the response.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoEventParameters $responseParams
   */
  public function setResponseParams(EnterpriseCrmFrontendsEventbusProtoEventParameters $responseParams)
  {
    $this->responseParams = $responseParams;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoEventParameters
   */
  public function getResponseParams()
  {
    return $this->responseParams;
  }
  /**
   * Workflow snapshot number.
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
   * Tenant this event is created. Used to reschedule the event to correct
   * tenant.
   *
   * @param string $tenant
   */
  public function setTenant($tenant)
  {
    $this->tenant = $tenant;
  }
  /**
   * @return string
   */
  public function getTenant()
  {
    return $this->tenant;
  }
  /**
   * The trigger id of the workflow trigger config. If both trigger_id and
   * client_id is present, the workflow is executed from the start tasks
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
   * Required. Pointer to the workflow it is executing.
   *
   * @param string $workflowId
   */
  public function setWorkflowId($workflowId)
  {
    $this->workflowId = $workflowId;
  }
  /**
   * @return string
   */
  public function getWorkflowId()
  {
    return $this->workflowId;
  }
  /**
   * Name of the workflow.
   *
   * @param string $workflowName
   */
  public function setWorkflowName($workflowName)
  {
    $this->workflowName = $workflowName;
  }
  /**
   * @return string
   */
  public function getWorkflowName()
  {
    return $this->workflowName;
  }
  /**
   * Time interval in seconds to schedule retry of workflow in manifold when
   * workflow is already running
   *
   * @param string $workflowRetryBackoffIntervalSeconds
   */
  public function setWorkflowRetryBackoffIntervalSeconds($workflowRetryBackoffIntervalSeconds)
  {
    $this->workflowRetryBackoffIntervalSeconds = $workflowRetryBackoffIntervalSeconds;
  }
  /**
   * @return string
   */
  public function getWorkflowRetryBackoffIntervalSeconds()
  {
    return $this->workflowRetryBackoffIntervalSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoEventExecutionInfo::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoEventExecutionInfo');
