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

namespace Google\Service\ParameterManager;

class RenderParameterVersionResponse extends \Google\Model
{
  /**
   * Output only. Resource identifier of a ParameterVersion in the format
   * `projects/locations/parameters/versions`.
   *
   * @var string
   */
  public $parameterVersion;
  protected $payloadType = ParameterVersionPayload::class;
  protected $payloadDataType = '';
  /**
   * Output only. Server generated rendered version of the user provided payload
   * data (ParameterVersionPayload) which has substitutions of all (if any)
   * references to a SecretManager SecretVersion resources. This substitution
   * only works for a Parameter which is in JSON or YAML format.
   *
   * @var string
   */
  public $renderedPayload;

  /**
   * Output only. Resource identifier of a ParameterVersion in the format
   * `projects/locations/parameters/versions`.
   *
   * @param string $parameterVersion
   */
  public function setParameterVersion($parameterVersion)
  {
    $this->parameterVersion = $parameterVersion;
  }
  /**
   * @return string
   */
  public function getParameterVersion()
  {
    return $this->parameterVersion;
  }
  /**
   * Payload content of a ParameterVersion resource.
   *
   * @param ParameterVersionPayload $payload
   */
  public function setPayload(ParameterVersionPayload $payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return ParameterVersionPayload
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Output only. Server generated rendered version of the user provided payload
   * data (ParameterVersionPayload) which has substitutions of all (if any)
   * references to a SecretManager SecretVersion resources. This substitution
   * only works for a Parameter which is in JSON or YAML format.
   *
   * @param string $renderedPayload
   */
  public function setRenderedPayload($renderedPayload)
  {
    $this->renderedPayload = $renderedPayload;
  }
  /**
   * @return string
   */
  public function getRenderedPayload()
  {
    return $this->renderedPayload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenderParameterVersionResponse::class, 'Google_Service_ParameterManager_RenderParameterVersionResponse');
