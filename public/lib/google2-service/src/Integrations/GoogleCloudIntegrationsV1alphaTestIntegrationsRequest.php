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

class GoogleCloudIntegrationsV1alphaTestIntegrationsRequest extends \Google\Model
{
  /**
   * Required. This is used to identify the client on whose behalf the event
   * will be executed.
   *
   * @var string
   */
  public $clientId;
  /**
   * Optional. Config parameters used during integration execution.
   *
   * @var array[]
   */
  public $configParameters;
  /**
   * Optional. custom deadline of the rpc
   *
   * @var string
   */
  public $deadlineSecondsTime;
  protected $inputParametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $inputParametersDataType = 'map';
  protected $integrationVersionType = GoogleCloudIntegrationsV1alphaIntegrationVersion::class;
  protected $integrationVersionDataType = '';
  protected $parametersType = EnterpriseCrmFrontendsEventbusProtoEventParameters::class;
  protected $parametersDataType = '';
  /**
   * Optional. Can be specified in the event request, otherwise false (default).
   * If true, enables tasks with condition "test_mode = true". If false,
   * disables tasks with condition "test_mode = true" if global test mode (set
   * by platform) is also false {@link EventBusConfig}.
   *
   * @var bool
   */
  public $testMode;
  /**
   * Required. The trigger id of the integration trigger config. If both
   * trigger_id and client_id is present, the integration is executed from the
   * start tasks provided by the matching trigger config otherwise it is
   * executed from the default start tasks.
   *
   * @var string
   */
  public $triggerId;

  /**
   * Required. This is used to identify the client on whose behalf the event
   * will be executed.
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
   * Optional. Config parameters used during integration execution.
   *
   * @param array[] $configParameters
   */
  public function setConfigParameters($configParameters)
  {
    $this->configParameters = $configParameters;
  }
  /**
   * @return array[]
   */
  public function getConfigParameters()
  {
    return $this->configParameters;
  }
  /**
   * Optional. custom deadline of the rpc
   *
   * @param string $deadlineSecondsTime
   */
  public function setDeadlineSecondsTime($deadlineSecondsTime)
  {
    $this->deadlineSecondsTime = $deadlineSecondsTime;
  }
  /**
   * @return string
   */
  public function getDeadlineSecondsTime()
  {
    return $this->deadlineSecondsTime;
  }
  /**
   * Optional. Input parameters used during integration execution.
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
   * Required. integration config to execute the workflow
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersion $integrationVersion
   */
  public function setIntegrationVersion(GoogleCloudIntegrationsV1alphaIntegrationVersion $integrationVersion)
  {
    $this->integrationVersion = $integrationVersion;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersion
   */
  public function getIntegrationVersion()
  {
    return $this->integrationVersion;
  }
  /**
   * Optional. Passed in as parameters to each integration execution.
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
   * Optional. Can be specified in the event request, otherwise false (default).
   * If true, enables tasks with condition "test_mode = true". If false,
   * disables tasks with condition "test_mode = true" if global test mode (set
   * by platform) is also false {@link EventBusConfig}.
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
   * Required. The trigger id of the integration trigger config. If both
   * trigger_id and client_id is present, the integration is executed from the
   * start tasks provided by the matching trigger config otherwise it is
   * executed from the default start tasks.
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
class_alias(GoogleCloudIntegrationsV1alphaTestIntegrationsRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTestIntegrationsRequest');
