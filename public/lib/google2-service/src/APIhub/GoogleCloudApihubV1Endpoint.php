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

class GoogleCloudApihubV1Endpoint extends \Google\Model
{
  protected $applicationIntegrationEndpointDetailsType = GoogleCloudApihubV1ApplicationIntegrationEndpointDetails::class;
  protected $applicationIntegrationEndpointDetailsDataType = '';

  /**
   * Required. The details of the Application Integration endpoint to be
   * triggered for curation.
   *
   * @param GoogleCloudApihubV1ApplicationIntegrationEndpointDetails $applicationIntegrationEndpointDetails
   */
  public function setApplicationIntegrationEndpointDetails(GoogleCloudApihubV1ApplicationIntegrationEndpointDetails $applicationIntegrationEndpointDetails)
  {
    $this->applicationIntegrationEndpointDetails = $applicationIntegrationEndpointDetails;
  }
  /**
   * @return GoogleCloudApihubV1ApplicationIntegrationEndpointDetails
   */
  public function getApplicationIntegrationEndpointDetails()
  {
    return $this->applicationIntegrationEndpointDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Endpoint::class, 'Google_Service_APIhub_GoogleCloudApihubV1Endpoint');
