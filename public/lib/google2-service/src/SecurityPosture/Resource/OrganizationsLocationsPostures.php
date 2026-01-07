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

namespace Google\Service\SecurityPosture\Resource;

use Google\Service\SecurityPosture\ExtractPostureRequest;
use Google\Service\SecurityPosture\ListPostureRevisionsResponse;
use Google\Service\SecurityPosture\ListPosturesResponse;
use Google\Service\SecurityPosture\Operation;
use Google\Service\SecurityPosture\Posture;

/**
 * The "postures" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitypostureService = new Google\Service\SecurityPosture(...);
 *   $postures = $securitypostureService->organizations_locations_postures;
 *  </code>
 */
class OrganizationsLocationsPostures extends \Google\Service\Resource
{
  /**
   * Creates a new Posture. (postures.create)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param Posture $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string postureId Required. An identifier for the posture.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Posture $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes all revisions of a Posture. You can only delete a posture if none of
   * its revisions are deployed. (postures.delete)
   *
   * @param string $name Required. The name of the Posture, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. An opaque identifier for the current version
   * of the posture. If you provide this value, then it must match the existing
   * value. If the values don't match, then the request fails with an ABORTED
   * error. If you omit this value, then the posture is deleted regardless of its
   * current `etag` value.
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
   * Extracts existing policies from an organization, folder, or project, and
   * applies them to another organization, folder, or project as a Posture. If the
   * other organization, folder, or project already has a posture, then the result
   * of the long-running operation is an ALREADY_EXISTS error. (postures.extract)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param ExtractPostureRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function extract($parent, ExtractPostureRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('extract', [$params], Operation::class);
  }
  /**
   * Gets a single revision of a Posture. (postures.get)
   *
   * @param string $name Required. The name of the Posture, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string revisionId Optional. The posture revision to retrieve. If
   * not specified, the most recently updated revision is retrieved.
   * @return Posture
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Posture::class);
  }
  /**
   * Lists the most recent revisions of all Posture resources in a specified
   * organization and location. (postures.listOrganizationsLocationsPostures)
   *
   * @param string $parent Required. The parent resource name, in the format
   * `organizations/{organization}/locations/global`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to apply to the list of postures,
   * in the format defined in [AIP-160: Filtering](https://google.aip.dev/160).
   * @opt_param int pageSize The maximum number of postures to return. The default
   * value is `500`. If you exceed the maximum value of `1000`, then the service
   * uses the maximum value.
   * @opt_param string pageToken A pagination token returned from a previous
   * request to list postures. Provide this token to retrieve the next page of
   * results.
   * @return ListPosturesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsPostures($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPosturesResponse::class);
  }
  /**
   * Lists all revisions of a single Posture. (postures.listRevisions)
   *
   * @param string $name Required. The name of the Posture, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of posture revisions to
   * return. The default value is `500`. If you exceed the maximum value of
   * `1000`, then the service uses the maximum value.
   * @opt_param string pageToken Optional. A pagination token from a previous
   * request to list posture revisions. Provide this token to retrieve the next
   * page of results.
   * @return ListPostureRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listRevisions($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('listRevisions', [$params], ListPostureRevisionsResponse::class);
  }
  /**
   * Updates a revision of an existing Posture. If the posture revision that you
   * update is currently deployed, then a new revision of the posture is created.
   * To prevent concurrent updates from overwriting each other, always follow the
   * read-modify-write pattern when you update a posture: 1. Call GetPosture to
   * get the current version of the posture. 2. Update the fields in the posture
   * as needed. 3. Call UpdatePosture to update the posture. Ensure that your
   * request includes the `etag` value from the GetPosture response.
   * **Important:** If you omit the `etag` when you call UpdatePosture, then the
   * updated posture unconditionally overwrites the existing posture.
   * (postures.patch)
   *
   * @param string $name Required. Identifier. The name of the posture, in the
   * format `organizations/{organization}/locations/global/postures/{posture_id}`.
   * @param Posture $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string revisionId Required. The revision ID of the posture to
   * update. If the posture revision that you update is currently deployed, then a
   * new revision of the posture is created.
   * @opt_param string updateMask Required. The fields in the Posture to update.
   * You can update only the following fields: * Posture.description *
   * Posture.policy_sets * Posture.state
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Posture $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsPostures::class, 'Google_Service_SecurityPosture_Resource_OrganizationsLocationsPostures');
