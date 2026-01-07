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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGetClientResponse;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $locations = $integrationsService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Generate OpenAPI spec for the requested integrations and api triggers
   * (locations.generateOpenApiSpec)
   *
   * @param string $name Required. Project and location from which the
   * integrations should be fetched. Format:
   * projects/{project}/location/{location}
   * @param GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecResponse
   * @throws \Google\Service\Exception
   */
  public function generateOpenApiSpec($name, GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateOpenApiSpec', [$params], GoogleCloudIntegrationsV1alphaGenerateOpenApiSpecResponse::class);
  }
  /**
   * Gets the client configuration for the given project and location resource
   * name (locations.getClients)
   *
   * @param string $parent Required. Required: The ID of the GCP Project to be
   * provisioned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaGetClientResponse
   * @throws \Google\Service\Exception
   */
  public function getClients($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('getClients', [$params], GoogleCloudIntegrationsV1alphaGetClientResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Integrations_Resource_ProjectsLocations');
