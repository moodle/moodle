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

class GoogleInternalCloudCrmEventbusV3PostToQueueWithTriggerIdRequest extends \Google\Model
{
  /**
   * Unspecified
   */
  public const PRIORITY_UNSPCIFIED = 'UNSPCIFIED';
  /**
   * Frequent partial and occasional full unavailability is expected and not
   * pageable. * Requests to this band will be shed before all other requests. *
   * This is the default for async calls sent from batch jobs.
   */
  public const PRIORITY_SHEDDABLE = 'SHEDDABLE';
  /**
   * Partial unavailability is expected and is not necessarily pageable. *
   * Requests to this band will be shed before any critical traffic. * This is
   * the default for async calls sent from production jobs.
   */
  public const PRIORITY_SHEDDABLE_PLUS = 'SHEDDABLE_PLUS';
  /**
   * Any outage is a pageable event. * During a production outage requests in
   * this band will only be shed before CRITICAL_PLUS. * This is the default for
   * sync calls sent from production jobs.
   */
  public const PRIORITY_CRITICAL = 'CRITICAL';
  /**
   * Any outage is a pageable event. * The guideline is for < 10% of requests to
   * a service to be in this band. * During a production outage requests in this
   * band will be prioritized above all others. * Opt-in to CRITICAL_PLUS when
   * your workflow triggers by human.
   */
  public const PRIORITY_CRITICAL_PLUS = 'CRITICAL_PLUS';
  /**
   * Optional. If the client id is provided, then the combination of trigger id
   * and client id is matched across all the workflows. If the client id is not
   * provided, then workflows with matching trigger id are executed for each
   * client id in the {@link TriggerConfig}. For Api Trigger, the client id is
   * required and will be validated against the allowed clients.
   *
   * @var string
   */
  public $clientId;
  /**
   * Optional. Flag to determine whether clients would suppress a warning when
   * no ACTIVE workflows are not found. If this flag is set to be true, an error
   * will not be thrown if the requested trigger_id or client_id is not found in
   * any ACTIVE workflow. Otherwise, the error is always thrown. The flag is set
   * to be false by default.
   *
   * @var bool
   */
  public $ignoreErrorIfNoActiveWorkflow;
  protected $parametersType = EnterpriseCrmEventbusProtoEventParameters::class;
  protected $parametersDataType = '';
  /**
   * The request priority this request should be processed at. For internal
   * users:
   *
   * @var string
   */
  public $priority;
  /**
   * Optional. This is a field to see the quota retry count for integration
   * execution
   *
   * @var int
   */
  public $quotaRetryCount;
  /**
   * Optional. This is used to de-dup incoming request: if the duplicate request
   * was detected, the response from the previous execution is returned. Must
   * have no more than 36 characters and contain only alphanumeric characters
   * and hyphens.
   *
   * @var string
   */
  public $requestId;
  /**
   * This field is only required when using Admin Access. The resource name of
   * target, or the parent resource name. For example:
   * "projects/locations/integrations"
   *
   * @var string
   */
  public $resourceName;
  /**
   * Optional. Time in milliseconds since epoch when the given event would be
   * scheduled.
   *
   * @var string
   */
  public $scheduledTime;
  /**
   * Optional. Sets test mode in {@link
   * enterprise/crm/eventbus/event_message.proto}.
   *
   * @var bool
   */
  public $testMode;
  /**
   * Matched against all {@link TriggerConfig}s across all workflows. i.e.
   * TriggerConfig.trigger_id.equals(trigger_id) Required.
   *
   * @var string
   */
  public $triggerId;
  /**
   * This is a unique id provided by the method caller. If provided this will be
   * used as the execution_id when a new execution info is created. This is a
   * string representation of a UUID. Must have no more than 36 characters and
   * contain only alphanumeric characters and hyphens.
   *
   * @var string
   */
  public $userGeneratedExecutionId;
  /**
   * Optional. If provided, the workflow_name is used to filter all the matched
   * workflows having same trigger_id+client_id. A combination of trigger_id,
   * client_id and workflow_name identifies a unique workflow.
   *
   * @var string
   */
  public $workflowName;

  /**
   * Optional. If the client id is provided, then the combination of trigger id
   * and client id is matched across all the workflows. If the client id is not
   * provided, then workflows with matching trigger id are executed for each
   * client id in the {@link TriggerConfig}. For Api Trigger, the client id is
   * required and will be validated against the allowed clients.
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
   * Optional. Flag to determine whether clients would suppress a warning when
   * no ACTIVE workflows are not found. If this flag is set to be true, an error
   * will not be thrown if the requested trigger_id or client_id is not found in
   * any ACTIVE workflow. Otherwise, the error is always thrown. The flag is set
   * to be false by default.
   *
   * @param bool $ignoreErrorIfNoActiveWorkflow
   */
  public function setIgnoreErrorIfNoActiveWorkflow($ignoreErrorIfNoActiveWorkflow)
  {
    $this->ignoreErrorIfNoActiveWorkflow = $ignoreErrorIfNoActiveWorkflow;
  }
  /**
   * @return bool
   */
  public function getIgnoreErrorIfNoActiveWorkflow()
  {
    return $this->ignoreErrorIfNoActiveWorkflow;
  }
  /**
   * Passed in as parameters to each workflow execution. Optional.
   *
   * @param EnterpriseCrmEventbusProtoEventParameters $parameters
   */
  public function setParameters(EnterpriseCrmEventbusProtoEventParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The request priority this request should be processed at. For internal
   * users:
   *
   * Accepted values: UNSPCIFIED, SHEDDABLE, SHEDDABLE_PLUS, CRITICAL,
   * CRITICAL_PLUS
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Optional. This is a field to see the quota retry count for integration
   * execution
   *
   * @param int $quotaRetryCount
   */
  public function setQuotaRetryCount($quotaRetryCount)
  {
    $this->quotaRetryCount = $quotaRetryCount;
  }
  /**
   * @return int
   */
  public function getQuotaRetryCount()
  {
    return $this->quotaRetryCount;
  }
  /**
   * Optional. This is used to de-dup incoming request: if the duplicate request
   * was detected, the response from the previous execution is returned. Must
   * have no more than 36 characters and contain only alphanumeric characters
   * and hyphens.
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
   * This field is only required when using Admin Access. The resource name of
   * target, or the parent resource name. For example:
   * "projects/locations/integrations"
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Optional. Time in milliseconds since epoch when the given event would be
   * scheduled.
   *
   * @param string $scheduledTime
   */
  public function setScheduledTime($scheduledTime)
  {
    $this->scheduledTime = $scheduledTime;
  }
  /**
   * @return string
   */
  public function getScheduledTime()
  {
    return $this->scheduledTime;
  }
  /**
   * Optional. Sets test mode in {@link
   * enterprise/crm/eventbus/event_message.proto}.
   *
   * @param bool $testMode
   */
  public function setTestMode($testMode)
  {
    $this->testMode = $testMode;
  }
  /**
   * @return bool
   */
  public function getTestMode()
  {
    return $this->testMode;
  }
  /**
   * Matched against all {@link TriggerConfig}s across all workflows. i.e.
   * TriggerConfig.trigger_id.equals(trigger_id) Required.
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
   * This is a unique id provided by the method caller. If provided this will be
   * used as the execution_id when a new execution info is created. This is a
   * string representation of a UUID. Must have no more than 36 characters and
   * contain only alphanumeric characters and hyphens.
   *
   * @param string $userGeneratedExecutionId
   */
  public function setUserGeneratedExecutionId($userGeneratedExecutionId)
  {
    $this->userGeneratedExecutionId = $userGeneratedExecutionId;
  }
  /**
   * @return string
   */
  public function getUserGeneratedExecutionId()
  {
    return $this->userGeneratedExecutionId;
  }
  /**
   * Optional. If provided, the workflow_name is used to filter all the matched
   * workflows having same trigger_id+client_id. A combination of trigger_id,
   * client_id and workflow_name identifies a unique workflow.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleInternalCloudCrmEventbusV3PostToQueueWithTriggerIdRequest::class, 'Google_Service_Integrations_GoogleInternalCloudCrmEventbusV3PostToQueueWithTriggerIdRequest');
