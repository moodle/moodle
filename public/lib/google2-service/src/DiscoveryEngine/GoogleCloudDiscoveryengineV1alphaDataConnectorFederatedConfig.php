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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig extends \Google\Model
{
  /**
   * Optional. Any additional parameters needed for FEDERATED.
   *
   * @var array[]
   */
  public $additionalParams;
  /**
   * Optional. Any authentication parameters specific to FEDERATED connectors.
   *
   * @var array[]
   */
  public $authParams;
  /**
   * Optional. Any authentication parameters specific to FEDERATED connectors in
   * json string format.
   *
   * @var string
   */
  public $jsonAuthParams;

  /**
   * Optional. Any additional parameters needed for FEDERATED.
   *
   * @param array[] $additionalParams
   */
  public function setAdditionalParams($additionalParams)
  {
    $this->additionalParams = $additionalParams;
  }
  /**
   * @return array[]
   */
  public function getAdditionalParams()
  {
    return $this->additionalParams;
  }
  /**
   * Optional. Any authentication parameters specific to FEDERATED connectors.
   *
   * @param array[] $authParams
   */
  public function setAuthParams($authParams)
  {
    $this->authParams = $authParams;
  }
  /**
   * @return array[]
   */
  public function getAuthParams()
  {
    return $this->authParams;
  }
  /**
   * Optional. Any authentication parameters specific to FEDERATED connectors in
   * json string format.
   *
   * @param string $jsonAuthParams
   */
  public function setJsonAuthParams($jsonAuthParams)
  {
    $this->jsonAuthParams = $jsonAuthParams;
  }
  /**
   * @return string
   */
  public function getJsonAuthParams()
  {
    return $this->jsonAuthParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDataConnectorFederatedConfig');
