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

class GoogleCloudIntegrationsV1alphaEventParameter extends \Google\Model
{
  /**
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given fired event. These parameters must be predefined in the
   * integration definition.
   *
   * @var string
   */
  public $key;
  /**
   * True if this parameter should be masked in the logs
   *
   * @var bool
   */
  public $masked;
  protected $valueType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $valueDataType = '';

  /**
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given fired event. These parameters must be predefined in the
   * integration definition.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * True if this parameter should be masked in the logs
   *
   * @param bool $masked
   */
  public function setMasked($masked)
  {
    $this->masked = $masked;
  }
  /**
   * @return bool
   */
  public function getMasked()
  {
    return $this->masked;
  }
  /**
   * Values for the defined keys. Each value can either be string, int, double
   * or any proto message.
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
class_alias(GoogleCloudIntegrationsV1alphaEventParameter::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaEventParameter');
