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

class GoogleCloudIntegrationsV1alphaApiTriggerResource extends \Google\Collection
{
  protected $collection_key = 'triggerId';
  /**
   * Required. Integration where the API is published
   *
   * @var string
   */
  public $integrationResource;
  /**
   * Required. Trigger Id of the API trigger(s) in the integration
   *
   * @var string[]
   */
  public $triggerId;

  /**
   * Required. Integration where the API is published
   *
   * @param string $integrationResource
   */
  public function setIntegrationResource($integrationResource)
  {
    $this->integrationResource = $integrationResource;
  }
  /**
   * @return string
   */
  public function getIntegrationResource()
  {
    return $this->integrationResource;
  }
  /**
   * Required. Trigger Id of the API trigger(s) in the integration
   *
   * @param string[] $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string[]
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaApiTriggerResource::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaApiTriggerResource');
