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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1AnswerQueryRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1AnswerQueryResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListServingConfigsResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1RecommendRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1RecommendResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1SearchRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1SearchResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ServingConfig;
use Google\Service\DiscoveryEngine\GoogleProtobufEmpty;

/**
 * The "servingConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $servingConfigs = $discoveryengineService->projects_locations_collections_dataStores_servingConfigs;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresServingConfigs extends \Google\Service\Resource
{
  /**
   * Answer query method. (servingConfigs.answer)
   *
   * @param string $servingConfig Required. The resource name of the Search
   * serving config, such as `projects/locations/global/collections/default_collec
   * tion/engines/servingConfigs/default_serving_config`, or `projects/locations/g
   * lobal/collections/default_collection/dataStores/servingConfigs/default_servin
   * g_config`. This field is used to identify the serving configuration name, set
   * of models used to make the search.
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1AnswerQueryResponse
   * @throws \Google\Service\Exception
   */
  public function answer($servingConfig, GoogleCloudDiscoveryengineV1AnswerQueryRequest $postBody, $optParams = [])
  {
    $params = ['servingConfig' => $servingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('answer', [$params], GoogleCloudDiscoveryengineV1AnswerQueryResponse::class);
  }
  /**
   * Deletes a ServingConfig. Returns a NOT_FOUND error if the ServingConfig does
   * not exist. (servingConfigs.delete)
   *
   * @param string $name Required. The resource name of the ServingConfig to
   * delete. Format: `projects/{project}/locations/{location}/collections/{collect
   * ion}/engines/{engine}/servingConfigs/{serving_config_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets a ServingConfig. Returns a NotFound error if the ServingConfig does not
   * exist. (servingConfigs.get)
   *
   * @param string $name Required. The resource name of the ServingConfig to get.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/eng
   * ines/{engine}/servingConfigs/{serving_config_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1ServingConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1ServingConfig::class);
  }
  /**
   * Lists all ServingConfigs linked to this dataStore.
   * (servingConfigs.listProjectsLocationsCollectionsDataStoresServingConfigs)
   *
   * @param string $parent Required. Full resource name of the parent resource.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/eng
   * ines/{engine}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of results to return. If
   * unspecified, defaults to 100. If a value greater than 100 is provided, at
   * most 100 results are returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListServingConfigs` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudDiscoveryengineV1ListServingConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsDataStoresServingConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListServingConfigsResponse::class);
  }
  /**
   * Updates a ServingConfig. Returns a NOT_FOUND error if the ServingConfig does
   * not exist. (servingConfigs.patch)
   *
   * @param string $name Immutable. Fully qualified name `projects/{project}/locat
   * ions/{location}/collections/{collection_id}/engines/{engine_id}/servingConfig
   * s/{serving_config_id}`
   * @param GoogleCloudDiscoveryengineV1ServingConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided
   * ServingConfig to update. The following are NOT supported: *
   * ServingConfig.name If not set, all supported fields are updated.
   * @return GoogleCloudDiscoveryengineV1ServingConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1ServingConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1ServingConfig::class);
  }
  /**
   * Makes a recommendation, which requires a contextual user event.
   * (servingConfigs.recommend)
   *
   * @param string $servingConfig Required. Full resource name of a ServingConfig:
   * `projects/locations/global/collections/engines/servingConfigs`, or
   * `projects/locations/global/collections/dataStores/servingConfigs` One default
   * serving config is created along with your recommendation engine creation. The
   * engine ID is used as the ID of the default serving config. For example, for
   * Engine `projects/locations/global/collections/engines/my-engine`, you can use
   * `projects/locations/global/collections/engines/my-engine/servingConfigs/my-
   * engine` for your RecommendationService.Recommend requests.
   * @param GoogleCloudDiscoveryengineV1RecommendRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1RecommendResponse
   * @throws \Google\Service\Exception
   */
  public function recommend($servingConfig, GoogleCloudDiscoveryengineV1RecommendRequest $postBody, $optParams = [])
  {
    $params = ['servingConfig' => $servingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('recommend', [$params], GoogleCloudDiscoveryengineV1RecommendResponse::class);
  }
  /**
   * Performs a search. (servingConfigs.search)
   *
   * @param string $servingConfig Required. The resource name of the Search
   * serving config, such as `projects/locations/global/collections/default_collec
   * tion/engines/servingConfigs/default_serving_config`, or `projects/locations/g
   * lobal/collections/default_collection/dataStores/default_data_store/servingCon
   * figs/default_serving_config`. This field is used to identify the serving
   * configuration name, set of models used to make the search.
   * @param GoogleCloudDiscoveryengineV1SearchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1SearchResponse
   * @throws \Google\Service\Exception
   */
  public function search($servingConfig, GoogleCloudDiscoveryengineV1SearchRequest $postBody, $optParams = [])
  {
    $params = ['servingConfig' => $servingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleCloudDiscoveryengineV1SearchResponse::class);
  }
  /**
   * Performs a search. Similar to the SearchService.Search method, but a lite
   * version that allows API key for authentication, where OAuth and IAM checks
   * are not required. Only public website search is supported by this method. If
   * data stores and engines not associated with public website search are
   * specified, a `FAILED_PRECONDITION` error is returned. This method can be used
   * for easy onboarding without having to implement an authentication backend.
   * However, it is strongly recommended to use SearchService.Search instead with
   * required OAuth and IAM checks to provide better data security.
   * (servingConfigs.searchLite)
   *
   * @param string $servingConfig Required. The resource name of the Search
   * serving config, such as `projects/locations/global/collections/default_collec
   * tion/engines/servingConfigs/default_serving_config`, or `projects/locations/g
   * lobal/collections/default_collection/dataStores/default_data_store/servingCon
   * figs/default_serving_config`. This field is used to identify the serving
   * configuration name, set of models used to make the search.
   * @param GoogleCloudDiscoveryengineV1SearchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1SearchResponse
   * @throws \Google\Service\Exception
   */
  public function searchLite($servingConfig, GoogleCloudDiscoveryengineV1SearchRequest $postBody, $optParams = [])
  {
    $params = ['servingConfig' => $servingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchLite', [$params], GoogleCloudDiscoveryengineV1SearchResponse::class);
  }
  /**
   * Answer query method (streaming). It takes one AnswerQueryRequest and returns
   * multiple AnswerQueryResponse messages in a stream.
   * (servingConfigs.streamAnswer)
   *
   * @param string $servingConfig Required. The resource name of the Search
   * serving config, such as `projects/locations/global/collections/default_collec
   * tion/engines/servingConfigs/default_serving_config`, or `projects/locations/g
   * lobal/collections/default_collection/dataStores/servingConfigs/default_servin
   * g_config`. This field is used to identify the serving configuration name, set
   * of models used to make the search.
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1AnswerQueryResponse
   * @throws \Google\Service\Exception
   */
  public function streamAnswer($servingConfig, GoogleCloudDiscoveryengineV1AnswerQueryRequest $postBody, $optParams = [])
  {
    $params = ['servingConfig' => $servingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('streamAnswer', [$params], GoogleCloudDiscoveryengineV1AnswerQueryResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresServingConfigs::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresServingConfigs');
