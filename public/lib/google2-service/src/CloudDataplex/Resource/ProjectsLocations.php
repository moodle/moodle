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

namespace Google\Service\CloudDataplex\Resource;

use Google\Service\CloudDataplex\GoogleCloudDataplexV1Entry;
use Google\Service\CloudDataplex\GoogleCloudDataplexV1SearchEntriesResponse;
use Google\Service\CloudDataplex\GoogleCloudLocationListLocationsResponse;
use Google\Service\CloudDataplex\GoogleCloudLocationLocation;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $locations = $dataplexService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Gets information about a location. (locations.get)
   *
   * @param string $name Resource name for the location.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudLocationLocation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudLocationLocation::class);
  }
  /**
   * Lists information about the supported locations for this service.
   * (locations.listProjectsLocations)
   *
   * @param string $name The resource that owns the locations collection, if
   * applicable.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string extraLocationTypes Optional. Do not use this field. It is
   * unsupported and is ignored unless explicitly documented otherwise. This is
   * primarily for internal usage.
   * @opt_param string filter A filter to narrow down results to a preferred
   * subset. The filtering language accepts strings like "displayName=tokyo", and
   * is documented in more detail in AIP-160 (https://google.aip.dev/160).
   * @opt_param int pageSize The maximum number of results to return. If not set,
   * the service selects a default.
   * @opt_param string pageToken A page token received from the next_page_token
   * field in the response. Send that page token to receive the subsequent page.
   * @return GoogleCloudLocationListLocationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudLocationListLocationsResponse::class);
  }
  /**
   * Looks up an entry by name using the permission on the source system. Caution:
   * The Vertex AI, Bigtable, Spanner, Pub/Sub, Dataform, and Dataproc Metastore
   * metadata that is stored in Dataplex Universal Catalog is changing. For more
   * information, see Changes to metadata stored in Dataplex Universal Catalog
   * (https://cloud.google.com/dataplex/docs/metadata-changes).
   * (locations.lookupEntry)
   *
   * @param string $name Required. The project to which the request should be
   * attributed in the following form: projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string aspectTypes Optional. Limits the aspects returned to the
   * provided aspect types. It only works for CUSTOM view.
   * @opt_param string entry Required. The resource name of the Entry: projects/{p
   * roject}/locations/{location}/entryGroups/{entry_group}/entries/{entry}.
   * @opt_param string paths Optional. Limits the aspects returned to those
   * associated with the provided paths within the Entry. It only works for CUSTOM
   * view.
   * @opt_param string view Optional. View to control which parts of an entry the
   * service should return.
   * @return GoogleCloudDataplexV1Entry
   * @throws \Google\Service\Exception
   */
  public function lookupEntry($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('lookupEntry', [$params], GoogleCloudDataplexV1Entry::class);
  }
  /**
   * Searches for Entries matching the given query and scope.
   * (locations.searchEntries)
   *
   * @param string $name Required. The project to which the request should be
   * attributed in the following form: projects/{project}/locations/global.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Optional. Specifies the ordering of results.
   * Supported values are: relevance last_modified_timestamp
   * last_modified_timestamp asc
   * @opt_param int pageSize Optional. Number of results in the search page. If
   * <=0, then defaults to 10. Max limit for page_size is 1000. Throws an invalid
   * argument for page_size > 1000.
   * @opt_param string pageToken Optional. Page token received from a previous
   * SearchEntries call. Provide this to retrieve the subsequent page.
   * @opt_param string query Required. The query against which entries in scope
   * should be matched. The query syntax is defined in Search syntax for Dataplex
   * Universal Catalog (https://cloud.google.com/dataplex/docs/search-syntax).
   * @opt_param string scope Optional. The scope under which the search should be
   * operating. It must either be organizations/ or projects/. If it is
   * unspecified, it defaults to the organization where the project provided in
   * name is located.
   * @opt_param bool semanticSearch Optional. Specifies whether the search should
   * understand the meaning and intent behind the query, rather than just matching
   * keywords.
   * @return GoogleCloudDataplexV1SearchEntriesResponse
   * @throws \Google\Service\Exception
   */
  public function searchEntries($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('searchEntries', [$params], GoogleCloudDataplexV1SearchEntriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocations');
