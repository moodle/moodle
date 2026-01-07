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

class GoogleCloudIntegrationsV1alphaTestIntegrationsResponse extends \Google\Collection
{
  protected $collection_key = 'parameterEntries';
  protected $eventParametersType = EnterpriseCrmFrontendsEventbusProtoEventParameters::class;
  protected $eventParametersDataType = '';
  /**
   * Is true if any execution in the integration failed. False otherwise.
   *
   * @var bool
   */
  public $executionFailed;
  /**
   * The id of the execution corresponding to this run of integration.
   *
   * @var string
   */
  public $executionId;
  protected $parameterEntriesType = EnterpriseCrmFrontendsEventbusProtoParameterEntry::class;
  protected $parameterEntriesDataType = 'array';
  protected $parametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $parametersDataType = 'map';

  /**
   * Details for the integration that were executed.
   *
   * @deprecated
   * @param EnterpriseCrmFrontendsEventbusProtoEventParameters $eventParameters
   */
  public function setEventParameters(EnterpriseCrmFrontendsEventbusProtoEventParameters $eventParameters)
  {
    $this->eventParameters = $eventParameters;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmFrontendsEventbusProtoEventParameters
   */
  public function getEventParameters()
  {
    return $this->eventParameters;
  }
  /**
   * Is true if any execution in the integration failed. False otherwise.
   *
   * @param bool $executionFailed
   */
  public function setExecutionFailed($executionFailed)
  {
    $this->executionFailed = $executionFailed;
  }
  /**
   * @return bool
   */
  public function getExecutionFailed()
  {
    return $this->executionFailed;
  }
  /**
   * The id of the execution corresponding to this run of integration.
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
   * Optional. Parameters are a part of Event and can be used to communicate
   * between different tasks that are part of the same integration execution.
   *
   * @param GoogleCloudIntegrationsV1alphaValueType[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaValueType[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaTestIntegrationsResponse::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTestIntegrationsResponse');
