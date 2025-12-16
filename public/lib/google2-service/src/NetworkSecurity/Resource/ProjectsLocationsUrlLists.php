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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\ListUrlListsResponse;
use Google\Service\NetworkSecurity\Operation;
use Google\Service\NetworkSecurity\UrlList;

/**
 * The "urlLists" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $urlLists = $networksecurityService->projects_locations_urlLists;
 *  </code>
 */
class ProjectsLocationsUrlLists extends \Google\Service\Resource
{
  /**
   * Creates a new UrlList in a given project and location. (urlLists.create)
   *
   * @param string $parent Required. The parent resource of the UrlList. Must be
   * in the format `projects/locations/{location}`.
   * @param UrlList $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string urlListId Required. Short name of the UrlList resource to
   * be created. This value should be 1-63 characters long, containing only
   * letters, numbers, hyphens, and underscores, and should not start with a
   * number. E.g. "url_list".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, UrlList $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single UrlList. (urlLists.delete)
   *
   * @param string $name Required. A name of the UrlList to delete. Must be in the
   * format `projects/locations/{location}/urlLists`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of a single UrlList. (urlLists.get)
   *
   * @param string $name Required. A name of the UrlList to get. Must be in the
   * format `projects/locations/{location}/urlLists`.
   * @param array $optParams Optional parameters.
   * @return UrlList
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], UrlList::class);
  }
  /**
   * Lists UrlLists in a given project and location.
   * (urlLists.listProjectsLocationsUrlLists)
   *
   * @param string $parent Required. The project and location from which the
   * UrlLists should be listed, specified in the format
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of UrlLists to return per call.
   * @opt_param string pageToken The value returned by the last
   * `ListUrlListsResponse` Indicates that this is a continuation of a prior
   * `ListUrlLists` call, and that the system should return the next page of data.
   * @return ListUrlListsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsUrlLists($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListUrlListsResponse::class);
  }
  /**
   * Updates the parameters of a single UrlList. (urlLists.patch)
   *
   * @param string $name Required. Name of the resource provided by the user. Name
   * is of the form projects/{project}/locations/{location}/urlLists/{url_list}
   * url_list should match the pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   * @param UrlList $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the UrlList resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, UrlList $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsUrlLists::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsUrlLists');
