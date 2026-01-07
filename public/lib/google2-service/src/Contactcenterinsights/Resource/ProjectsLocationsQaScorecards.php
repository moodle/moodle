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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListQaScorecardsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QaScorecard;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "qaScorecards" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $qaScorecards = $contactcenterinsightsService->projects_locations_qaScorecards;
 *  </code>
 */
class ProjectsLocationsQaScorecards extends \Google\Service\Resource
{
  /**
   * Create a QaScorecard. (qaScorecards.create)
   *
   * @param string $parent Required. The parent resource of the QaScorecard.
   * @param GoogleCloudContactcenterinsightsV1QaScorecard $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string qaScorecardId Optional. A unique ID for the new
   * QaScorecard. This ID will become the final component of the QaScorecard's
   * resource name. If no ID is specified, a server-generated ID will be used.
   * This value should be 4-64 characters and must match the regular expression
   * `^[a-z0-9-]{4,64}$`. Valid characters are `a-z-`.
   * @return GoogleCloudContactcenterinsightsV1QaScorecard
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1QaScorecard $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1QaScorecard::class);
  }
  /**
   * Deletes a QaScorecard. (qaScorecards.delete)
   *
   * @param string $name Required. The name of the QaScorecard to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, all of this QaScorecard's
   * child resources will also be deleted. Otherwise, the request will only
   * succeed if it has none.
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
   * Gets a QaScorecard. (qaScorecards.get)
   *
   * @param string $name Required. The name of the QaScorecard to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1QaScorecard
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1QaScorecard::class);
  }
  /**
   * Lists QaScorecards. (qaScorecards.listProjectsLocationsQaScorecards)
   *
   * @param string $parent Required. The parent resource of the scorecards.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of scorecards to return
   * in the response. If the value is zero, the service will select a default
   * size. A call might return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListQaScorecardsResponse`. This value indicates that this is a continuation
   * of a prior `ListQaScorecards` call and that the system should return the next
   * page of data.
   * @opt_param string qaScorecardSources Optional. The source of scorecards are
   * based on how those Scorecards were created, e.g., a customer-defined
   * scorecard, a predefined scorecard, etc. This field is used to retrieve
   * Scorecards of one or more sources.
   * @return GoogleCloudContactcenterinsightsV1ListQaScorecardsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsQaScorecards($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListQaScorecardsResponse::class);
  }
  /**
   * Updates a QaScorecard. (qaScorecards.patch)
   *
   * @param string $name Identifier. The scorecard name. Format:
   * projects/{project}/locations/{location}/qaScorecards/{qa_scorecard}
   * @param GoogleCloudContactcenterinsightsV1QaScorecard $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated. All
   * possible fields can be updated by passing `*`, or a subset of the following
   * updateable fields can be provided: * `description` * `display_name`
   * @return GoogleCloudContactcenterinsightsV1QaScorecard
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1QaScorecard $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1QaScorecard::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsQaScorecards::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsQaScorecards');
