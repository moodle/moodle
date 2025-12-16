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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ApplicationIntegrationEndpointDetails extends \Google\Model
{
  /**
   * Required. The API trigger ID of the Application Integration workflow.
   *
   * @var string
   */
  public $triggerId;
  /**
   * Required. The endpoint URI should be a valid REST URI for triggering an
   * Application Integration. Format: `https://integrations.googleapis.com/v1/{n
   * ame=projects/locations/integrations}:execute` or `https://{location}-
   * integrations.googleapis.com/v1/{name=projects/locations/integrations}:execu
   * te`
   *
   * @var string
   */
  public $uri;

  /**
   * Required. The API trigger ID of the Application Integration workflow.
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
   * Required. The endpoint URI should be a valid REST URI for triggering an
   * Application Integration. Format: `https://integrations.googleapis.com/v1/{n
   * ame=projects/locations/integrations}:execute` or `https://{location}-
   * integrations.googleapis.com/v1/{name=projects/locations/integrations}:execu
   * te`
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ApplicationIntegrationEndpointDetails::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApplicationIntegrationEndpointDetails');
