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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1CompleteQueryResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1DataStore;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListDataStoresResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1SiteSearchEngine;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1TrainCustomModelRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "dataStores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $dataStores = $discoveryengineService->projects_locations_collections_dataStores;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStores extends \Google\Service\Resource
{
  /**
   * Completes the specified user input with keyword suggestions.
   * (dataStores.completeQuery)
   *
   * @param string $dataStore Required. The parent data store resource name for
   * which the completion is performed, such as `projects/locations/global/collect
   * ions/default_collection/dataStores/default_data_store`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool includeTailSuggestions Indicates if tail suggestions should
   * be returned if there are no suggestions that match the full query. Even if
   * set to true, if there are suggestions that match the full query, those are
   * returned and no tail suggestions are returned.
   * @opt_param string query Required. The typeahead input used to fetch
   * suggestions. Maximum length is 128 characters.
   * @opt_param string queryModel Specifies the autocomplete data model. This
   * overrides any model specified in the Configuration > Autocomplete section of
   * the Cloud console. Currently supported values: * `document` - Using
   * suggestions generated from user-imported documents. * `search-history` -
   * Using suggestions generated from the past history of SearchService.Search API
   * calls. Do not use it when there is no traffic for Search API. * `user-event`
   * - Using suggestions generated from user-imported search events. * `document-
   * completable` - Using suggestions taken directly from user-imported document
   * fields marked as completable. Default values: * `document` is the default
   * model for regular dataStores. * `search-history` is the default model for
   * site search dataStores.
   * @opt_param string userPseudoId Optional. A unique identifier for tracking
   * visitors. For example, this could be implemented with an HTTP cookie, which
   * should be able to uniquely identify a visitor on a single device. This unique
   * identifier should not change if the visitor logs in or out of the website.
   * This field should NOT have a fixed value such as `unknown_visitor`. This
   * should be the same identifier as UserEvent.user_pseudo_id and
   * SearchRequest.user_pseudo_id. The field must be a UTF-8 encoded string with a
   * length limit of 128 characters. Otherwise, an `INVALID_ARGUMENT` error is
   * returned.
   * @return GoogleCloudDiscoveryengineV1CompleteQueryResponse
   * @throws \Google\Service\Exception
   */
  public function completeQuery($dataStore, $optParams = [])
  {
    $params = ['dataStore' => $dataStore];
    $params = array_merge($params, $optParams);
    return $this->call('completeQuery', [$params], GoogleCloudDiscoveryengineV1CompleteQueryResponse::class);
  }
  /**
   * Creates a DataStore. DataStore is for storing Documents. To serve these
   * documents for Search, or Recommendation use case, an Engine needs to be
   * created separately. (dataStores.create)
   *
   * @param string $parent Required. The parent resource name, such as
   * `projects/{project}/locations/{location}/collections/{collection}`.
   * @param GoogleCloudDiscoveryengineV1DataStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string cmekConfigName Resource name of the CmekConfig to use for
   * protecting this DataStore.
   * @opt_param bool createAdvancedSiteSearch A boolean flag indicating whether
   * user want to directly create an advanced data store for site search. If the
   * data store is not configured as site search (GENERIC vertical and
   * PUBLIC_WEBSITE content_config), this flag will be ignored.
   * @opt_param string dataStoreId Required. The ID to use for the DataStore,
   * which will become the final component of the DataStore's resource name. This
   * field must conform to [RFC-1034](https://tools.ietf.org/html/rfc1034)
   * standard with a length limit of 63 characters. Otherwise, an INVALID_ARGUMENT
   * error is returned.
   * @opt_param bool disableCmek DataStore without CMEK protections. If a default
   * CmekConfig is set for the project, setting this field will override the
   * default CmekConfig as well.
   * @opt_param bool skipDefaultSchemaCreation A boolean flag indicating whether
   * to skip the default schema creation for the data store. Only enable this flag
   * if you are certain that the default schema is incompatible with your use
   * case. If set to true, you must manually create a schema for the data store
   * before any documents can be ingested. This flag cannot be specified if
   * `data_store.starting_schema` is specified.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1DataStore $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a DataStore. (dataStores.delete)
   *
   * @param string $name Required. Full resource name of DataStore, such as `proje
   * cts/{project}/locations/{location}/collections/{collection_id}/dataStores/{da
   * ta_store_id}`. If the caller does not have permission to delete the
   * DataStore, regardless of whether or not it exists, a PERMISSION_DENIED error
   * is returned. If the DataStore to delete does not exist, a NOT_FOUND error is
   * returned.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a DataStore. (dataStores.get)
   *
   * @param string $name Required. Full resource name of DataStore, such as `proje
   * cts/{project}/locations/{location}/collections/{collection_id}/dataStores/{da
   * ta_store_id}`. If the caller does not have permission to access the
   * DataStore, regardless of whether or not it exists, a PERMISSION_DENIED error
   * is returned. If the requested DataStore does not exist, a NOT_FOUND error is
   * returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1DataStore
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1DataStore::class);
  }
  /**
   * Gets the SiteSearchEngine. (dataStores.getSiteSearchEngine)
   *
   * @param string $name Required. Resource name of SiteSearchEngine, such as `pro
   * jects/{project}/locations/{location}/collections/{collection}/dataStores/{dat
   * a_store}/siteSearchEngine`. If the caller does not have permission to access
   * the [SiteSearchEngine], regardless of whether or not it exists, a
   * PERMISSION_DENIED error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1SiteSearchEngine
   * @throws \Google\Service\Exception
   */
  public function getSiteSearchEngine($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSiteSearchEngine', [$params], GoogleCloudDiscoveryengineV1SiteSearchEngine::class);
  }
  /**
   * Lists all the DataStores associated with the project.
   * (dataStores.listProjectsLocationsCollectionsDataStores)
   *
   * @param string $parent Required. The parent branch resource name, such as
   * `projects/{project}/locations/{location}/collections/{collection_id}`. If the
   * caller does not have permission to list DataStores under this location,
   * regardless of whether or not this data store exists, a PERMISSION_DENIED
   * error is returned.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter by solution type . For example: `filter =
   * 'solution_type:SOLUTION_TYPE_SEARCH'`
   * @opt_param int pageSize Maximum number of DataStores to return. If
   * unspecified, defaults to 10. The maximum allowed value is 50. Values above 50
   * will be coerced to 50. If this field is negative, an INVALID_ARGUMENT is
   * returned.
   * @opt_param string pageToken A page token
   * ListDataStoresResponse.next_page_token, received from a previous
   * DataStoreService.ListDataStores call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * DataStoreService.ListDataStores must match the call that provided the page
   * token. Otherwise, an INVALID_ARGUMENT error is returned.
   * @return GoogleCloudDiscoveryengineV1ListDataStoresResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsDataStores($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListDataStoresResponse::class);
  }
  /**
   * Updates a DataStore (dataStores.patch)
   *
   * @param string $name Immutable. Identifier. The full resource name of the data
   * store. Format: `projects/{project}/locations/{location}/collections/{collecti
   * on_id}/dataStores/{data_store_id}`. This field must be a UTF-8 encoded string
   * with a length limit of 1024 characters.
   * @param GoogleCloudDiscoveryengineV1DataStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided DataStore
   * to update. If an unsupported or unknown field is provided, an
   * INVALID_ARGUMENT error is returned.
   * @return GoogleCloudDiscoveryengineV1DataStore
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1DataStore $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1DataStore::class);
  }
  /**
   * Trains a custom model. (dataStores.trainCustomModel)
   *
   * @param string $dataStore Required. The resource name of the Data Store, such
   * as `projects/locations/global/collections/default_collection/dataStores/defau
   * lt_data_store`. This field is used to identify the data store where to train
   * the models.
   * @param GoogleCloudDiscoveryengineV1TrainCustomModelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function trainCustomModel($dataStore, GoogleCloudDiscoveryengineV1TrainCustomModelRequest $postBody, $optParams = [])
  {
    $params = ['dataStore' => $dataStore, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('trainCustomModel', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStores::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStores');
