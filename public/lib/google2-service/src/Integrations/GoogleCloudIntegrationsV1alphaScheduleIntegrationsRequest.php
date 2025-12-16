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

class GoogleCloudIntegrationsV1alphaScheduleIntegrationsRequest extends \Google\Collection
{
  protected $collection_key = 'parameterEntries';
  protected $inputParametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $inputParametersDataType = 'map';
  protected $parameterEntriesType = EnterpriseCrmFrontendsEventbusProtoParameterEntry::class;
  protected $parameterEntriesDataType = 'array';
  protected $parametersType = EnterpriseCrmEventbusProtoEventParameters::class;
  protected $parametersDataType = '';
  /**
   * This is used to de-dup incoming request: if the duplicate request was
   * detected, the response from the previous execution is returned.
   *
   * @var string
   */
  public $requestId;
  /**
   * The time that the integration should be executed. If the time is less or
   * equal to the current time, the integration is executed immediately.
   *
   * @var string
   */
  public $scheduleTime;
  /**
   * Required. Matched against all {@link TriggerConfig}s across all
   * integrations. i.e. TriggerConfig.trigger_id.equals(trigger_id)
   *
   * @var string
   */
  public $triggerId;
  /**
   * Optional. This is a unique id provided by the method caller. If provided
   * this will be used as the execution_id when a new execution info is created.
   * This is a string representation of a UUID. Must have no more than 36
   * characters and contain only alphanumeric characters and hyphens.
   *
   * @var string
   */
  public $userGeneratedExecutionId;

  /**
   * Optional. Input parameters used by integration execution.
   *
   * @param GoogleCloudIntegrationsV1alphaValueType[] $inputParameters
   */
  public function setInputParameters($inputParameters)
  {
    $this->inputParameters = $inputParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaValueType[]
   */
  public function getInputParameters()
  {
    return $this->inputParameters;
  }
  /**
   * Parameters are a part of Event and can be used to communicate between
   * different tasks that are part of the same integration execution.
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoParameterEntry[] $parameterEntries
   */
  public function setParameterEntries($parameterEntries)
  {
    $this->parameterEntries = $parameterEntries;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoParameterEntry[]
   */
  public function getParameterEntries()
  {
    return $this->parameterEntries;
  }
  /**
   * Passed in as parameters to each integration execution.
   *
   * @deprecated
   * @param EnterpriseCrmEventbusProtoEventParameters $parameters
   */
  public function setParameters(EnterpriseCrmEventbusProtoEventParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmEventbusProtoEventParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * This is used to de-dup incoming request: if the duplicate request was
   * detected, the response from the previous execution is returned.
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
   * The time that the integration should be executed. If the time is less or
   * equal to the current time, the integration is executed immediately.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
  /**
   * Required. Matched against all {@link TriggerConfig}s across all
   * integrations. i.e. TriggerConfig.trigger_id.equals(trigger_id)
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
   * Optional. This is a unique id provided by the method caller. If provided
   * this will be used as the execution_id when a new execution info is created.
   * This is a string representation of a UUID. Must have no more than 36
   * characters and contain only alphanumeric characters and hyphens.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaScheduleIntegrationsRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaScheduleIntegrationsRequest');
