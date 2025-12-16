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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1UpdateEndpointLongRunningRequest extends \Google\Model
{
  protected $endpointType = GoogleCloudAiplatformV1Endpoint::class;
  protected $endpointDataType = '';

  /**
   * Required. The Endpoint which replaces the resource on the server. Currently
   * we only support updating the `client_connection_config` field, all the
   * other fields' update will be blocked.
   *
   * @param GoogleCloudAiplatformV1Endpoint $endpoint
   */
  public function setEndpoint(GoogleCloudAiplatformV1Endpoint $endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return GoogleCloudAiplatformV1Endpoint
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UpdateEndpointLongRunningRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UpdateEndpointLongRunningRequest');
