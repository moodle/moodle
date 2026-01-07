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

class GoogleCloudIntegrationsV1alphaExecuteIntegrationsRequest extends \Google\Collection
{
  protected $collection_key = 'parameterEntries';
  /**
   * Optional. Flag to determine how to should propagate errors. If this flag is
   * set to be true, it will not throw an exception. Instead, it will return a
   * {@link ExecuteIntegrationsResponse} with an execution id and error messages
   * as PostWithTriggerIdExecutionException in {@link EventParameters}. The flag
   * is set to be false by default.
   *
   * @var bool
   */
  public $doNotPropagateError;
  /**
   * Optional. The id of the ON_HOLD execution to be resumed.
   *
   * @var string
   */
  public $executionId;
  protected $inputParametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $inputParametersDataType = 'map';
  protected $parameterEntriesType = EnterpriseCrmFrontendsEventbusProtoParameterEntry::class;
  protected $parameterEntriesDataType = 'array';
  protected $parametersType = EnterpriseCrmFrontendsEventbusProtoEventParameters::class;
  protected $parametersDataType = '';
  /**
   * Optional. This is used to de-dup incoming request: if the duplicate request
   * was detected, the response from the previous execution is returned.
   *
   * @var string
   */
  public $requestId;
  /**
   * Required. Matched against all {@link TriggerConfig}s across all
   * integrations. i.e. TriggerConfig.trigger_id.equals(trigger_id). The
   * trigger_id is in the format of `api_trigger/TRIGGER_NAME`.
   *
   * @var string
   */
  public $triggerId;

  /**
   * Optional. Flag to determine how to should propagate errors. If this flag is
   * set to be true, it will not throw an exception. Instead, it will return a
   * {@link ExecuteIntegrationsResponse} with an execution id and error messages
   * as PostWithTriggerIdExecutionException in {@link EventParameters}. The flag
   * is set to be false by default.
   *
   * @param bool $doNotPropagateError
   */
  public function setDoNotPropagateError($doNotPropagateError)
  {
    $this->doNotPropagateError = $doNotPropagateError;
  }
  /**
   * @return bool
   */
  public function getDoNotPropagateError()
  {
    return $this->doNotPropagateError;
  }
  /**
   * Optional. The id of the ON_HOLD execution to be resumed.
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
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
   * Optional. Parameters are a part of Event and can be used to communicate
   * between different tasks that are part of the same integration execution.
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
   * Optional. Passed in as parameters to each integration execution. Redacted
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoEventParameters $parameters
   */
  public function setParameters(EnterpriseCrmFrontendsEventbusProtoEventParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoEventParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. This is used to de-dup incoming request: if the duplicate request
   * was detected, the response from the previous execution is returned.
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
   * Required. Matched against all {@link TriggerConfig}s across all
   * integrations. i.e. TriggerConfig.trigger_id.equals(trigger_id). The
   * trigger_id is in the format of `api_trigger/TRIGGER_NAME`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaExecuteIntegrationsRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaExecuteIntegrationsRequest');
