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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponse;

/**
 * The "branches" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $branches = $discoveryengineService->projects_locations_collections_dataStores_branches;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresBranches extends \Google\Service\Resource
{
  /**
   * Gets index freshness metadata for Documents. Supported for website search
   * only. (branches.batchGetDocumentsMetadata)
   *
   * @param string $parent Required. The parent branch resource name, such as `pro
   * jects/{project}/locations/{location}/collections/{collection}/dataStores/{dat
   * a_store}/branches/{branch}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string matcher.fhirMatcher.fhirResources Required. The FHIR
   * resources to match by. Format: projects/{project}/locations/{location}/datase
   * ts/{dataset}/fhirStores/{fhir_store}/fhir/{resource_type}/{fhir_resource_id}
   * @opt_param string matcher.urisMatcher.uris The exact URIs to match by.
   * @return GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponse
   * @throws \Google\Service\Exception
   */
  public function batchGetDocumentsMetadata($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('batchGetDocumentsMetadata', [$params], GoogleCloudDiscoveryengineV1BatchGetDocumentsMetadataResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresBranches::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresBranches');
