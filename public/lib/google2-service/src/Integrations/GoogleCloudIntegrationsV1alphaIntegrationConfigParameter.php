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

class GoogleCloudIntegrationsV1alphaIntegrationConfigParameter extends \Google\Model
{
  protected $parameterType = GoogleCloudIntegrationsV1alphaIntegrationParameter::class;
  protected $parameterDataType = '';
  protected $valueType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $valueDataType = '';

  /**
   * Optional. Integration Parameter to provide the default value, data type and
   * attributes required for the Integration config variables.
   *
   * @param GoogleCloudIntegrationsV1alphaIntegrationParameter $parameter
   */
  public function setParameter(GoogleCloudIntegrationsV1alphaIntegrationParameter $parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaIntegrationParameter
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * Values for the defined keys. Each value can either be string, int, double
   * or any proto message or a serialized object.
   *
   * @param GoogleCloudIntegrationsV1alphaValueType $value
   */
  public function setValue(GoogleCloudIntegrationsV1alphaValueType $value)
  {
    $this->value = $value;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaValueType
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaIntegrationConfigParameter::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaIntegrationConfigParameter');
