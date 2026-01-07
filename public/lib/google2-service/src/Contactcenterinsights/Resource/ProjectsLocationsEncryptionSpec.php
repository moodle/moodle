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

namespace Google\Service\Contactcenterinsights\Resource;

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1InitializeEncryptionSpecRequest;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;

/**
 * The "encryptionSpec" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $encryptionSpec = $contactcenterinsightsService->projects_locations_encryptionSpec;
 *  </code>
 */
class ProjectsLocationsEncryptionSpec extends \Google\Service\Resource
{
  /**
   * Initializes a location-level encryption key specification. An error will
   * result if the location has resources already created before the
   * initialization. After the encryption specification is initialized at a
   * location, it is immutable and all newly created resources under the location
   * will be encrypted with the existing specification.
   * (encryptionSpec.initialize)
   *
   * @param string $name Immutable. The resource name of the encryption key
   * specification resource. Format:
   * projects/{project}/locations/{location}/encryptionSpec
   * @param GoogleCloudContactcenterinsightsV1InitializeEncryptionSpecRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function initialize($name, GoogleCloudContactcenterinsightsV1InitializeEncryptionSpecRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('initialize', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEncryptionSpec::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsEncryptionSpec');
