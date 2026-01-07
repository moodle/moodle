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
use Google\Service\CloudDataplex\GoogleCloudDataplexV1ListEntriesResponse;

/**
 * The "entries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataplexService = new Google\Service\CloudDataplex(...);
 *   $entries = $dataplexService->projects_locations_entryGroups_entries;
 *  </code>
 */
class ProjectsLocationsEntryGroupsEntries extends \Google\Service\Resource
{
  /**
   * Creates an Entry. (entries.create)
   *
   * @param string $parent Required. The resource name of the parent Entry Group:
   * projects/{project}/locations/{location}/entryGroups/{entry_group}.
   * @param GoogleCloudDataplexV1Entry $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string entryId Required. Entry identifier. It has to be unique
   * within an Entry Group.Entries corresponding to Google Cloud resources use an
   * Entry ID format based on full resource names
   * (https://cloud.google.com/apis/design/resource_names#full_resource_name). The
   * format is a full resource name of the resource without the prefix double
   * slashes in the API service name part of the full resource name. This allows
   * retrieval of entries using their associated resource name.For example, if the
   * full resource name of a resource is
   * //library.googleapis.com/shelves/shelf1/books/book2, then the suggested
   * entry_id is library.googleapis.com/shelves/shelf1/books/book2.It is also
   * suggested to follow the same convention for entries corresponding to
   * resources from providers or systems other than Google Cloud.The maximum size
   * of the field is 4000 characters.
   * @return GoogleCloudDataplexV1Entry
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDataplexV1Entry $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDataplexV1Entry::class);
  }
  /**
   * Deletes an Entry. (entries.delete)
   *
   * @param string $name Required. The resource name of the Entry: projects/{proje
   * ct}/locations/{location}/entryGroups/{entry_group}/entries/{entry}.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDataplexV1Entry
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleCloudDataplexV1Entry::class);
  }
  /**
   * Gets an Entry. Caution: The Vertex AI, Bigtable, Spanner, Pub/Sub, Dataform,
   * and Dataproc Metastore metadata that is stored in Dataplex Universal Catalog
   * is changing. For more information, see Changes to metadata stored in Dataplex
   * Universal Catalog (https://cloud.google.com/dataplex/docs/metadata-changes).
   * (entries.get)
   *
   * @param string $name Required. The resource name of the Entry: projects/{proje
   * ct}/locations/{location}/entryGroups/{entry_group}/entries/{entry}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string aspectTypes Optional. Limits the aspects returned to the
   * provided aspect types. It only works for CUSTOM view.
   * @opt_param string paths Optional. Limits the aspects returned to those
   * associated with the provided paths within the Entry. It only works for CUSTOM
   * view.
   * @opt_param string view Optional. View to control which parts of an entry the
   * service should return.
   * @return GoogleCloudDataplexV1Entry
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDataplexV1Entry::class);
  }
  /**
   * Lists Entries within an EntryGroup. Caution: The Vertex AI, Bigtable,
   * Spanner, Pub/Sub, Dataform, and Dataproc Metastore metadata that is stored in
   * Dataplex Universal Catalog is changing. For more information, see Changes to
   * metadata stored in Dataplex Universal Catalog
   * (https://cloud.google.com/dataplex/docs/metadata-changes).
   * (entries.listProjectsLocationsEntryGroupsEntries)
   *
   * @param string $parent Required. The resource name of the parent Entry Group:
   * projects/{project}/locations/{location}/entryGroups/{entry_group}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter on the entries to return. Filters
   * are case-sensitive. You can filter the request by the following fields:
   * entry_type entry_source.display_name parent_entryThe comparison operators are
   * =, !=, <, >, <=, >=. The service compares strings according to lexical
   * order.You can use the logical operators AND, OR, NOT in the filter.You can
   * use Wildcard "*", but for entry_type and parent_entry you need to provide the
   * full project id or number.You cannot use parent_entry in conjunction with
   * other fields.Example filter expressions:
   * "entry_source.display_name=AnExampleDisplayName"
   * "entry_type=projects/example-project/locations/global/entryTypes/example-
   * entry_type" "entry_type=projects/example-project/locations/us/entryTypes/a*
   * OR entry_type=projects/another-project/locations" "NOT
   * entry_source.display_name=AnotherExampleDisplayName"
   * "parent_entry=projects/example-project/locations/us/entryGroups/example-
   * entry-group/entries/example-entry"
   * @opt_param int pageSize Optional. Number of items to return per page. If
   * there are remaining results, the service returns a next_page_token. If
   * unspecified, the service returns at most 10 Entries. The maximum value is
   * 100; values above 100 will be coerced to 100.
   * @opt_param string pageToken Optional. Page token received from a previous
   * ListEntries call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudDataplexV1ListEntriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEntryGroupsEntries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDataplexV1ListEntriesResponse::class);
  }
  /**
   * Updates an Entry. (entries.patch)
   *
   * @param string $name Identifier. The relative resource name of the entry, in
   * the format projects/{project_id_or_number}/locations/{location_id}/entryGroup
   * s/{entry_group_id}/entries/{entry_id}.
   * @param GoogleCloudDataplexV1Entry $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true and the entry doesn't
   * exist, the service will create it.
   * @opt_param string aspectKeys Optional. The map keys of the Aspects which the
   * service should modify. It supports the following syntaxes: - matches an
   * aspect of the given type and empty path. @path - matches an aspect of the
   * given type and specified path. For example, to attach an aspect to a field
   * that is specified by the schema aspect, the path should have the format
   * Schema.. @* - matches aspects of the given type for all paths. *@path -
   * matches aspects of all types on the given path.The service will not remove
   * existing aspects matching the syntax unless delete_missing_aspects is set to
   * true.If this field is left empty, the service treats it as specifying exactly
   * those Aspects present in the request.
   * @opt_param bool deleteMissingAspects Optional. If set to true and the
   * aspect_keys specify aspect ranges, the service deletes any existing aspects
   * from that range that weren't provided in the request.
   * @opt_param string updateMask Optional. Mask of fields to update. To update
   * Aspects, the update_mask must contain the value "aspects".If the update_mask
   * is empty, the service will update all modifiable fields present in the
   * request.
   * @return GoogleCloudDataplexV1Entry
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDataplexV1Entry $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDataplexV1Entry::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEntryGroupsEntries::class, 'Google_Service_CloudDataplex_Resource_ProjectsLocationsEntryGroupsEntries');
