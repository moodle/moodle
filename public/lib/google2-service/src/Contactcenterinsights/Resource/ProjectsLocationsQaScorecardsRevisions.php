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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1DeployQaScorecardRevisionRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListQaScorecardRevisionsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QaScorecardRevision;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1TuneQaScorecardRevisionRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1UndeployQaScorecardRevisionRequest;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $revisions = $contactcenterinsightsService->projects_locations_qaScorecards_revisions;
 *  </code>
 */
class ProjectsLocationsQaScorecardsRevisions extends \Google\Service\Resource
{
  /**
   * Creates a QaScorecardRevision. (revisions.create)
   *
   * @param string $parent Required. The parent resource of the
   * QaScorecardRevision.
   * @param GoogleCloudContactcenterinsightsV1QaScorecardRevision $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string qaScorecardRevisionId Optional. A unique ID for the new
   * QaScorecardRevision. This ID will become the final component of the
   * QaScorecardRevision's resource name. If no ID is specified, a server-
   * generated ID will be used. This value should be 4-64 characters and must
   * match the regular expression `^[a-z0-9-]{4,64}$`. Valid characters are
   * `a-z-`.
   * @return GoogleCloudContactcenterinsightsV1QaScorecardRevision
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1QaScorecardRevision $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1QaScorecardRevision::class);
  }
  /**
   * Deletes a QaScorecardRevision. (revisions.delete)
   *
   * @param string $name Required. The name of the QaScorecardRevision to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, all of this
   * QaScorecardRevision's child resources will also be deleted. Otherwise, the
   * request will only succeed if it has none.
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
   * Deploy a QaScorecardRevision. (revisions.deploy)
   *
   * @param string $name Required. The name of the QaScorecardRevision to deploy.
   * @param GoogleCloudContactcenterinsightsV1DeployQaScorecardRevisionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1QaScorecardRevision
   * @throws \Google\Service\Exception
   */
  public function deploy($name, GoogleCloudContactcenterinsightsV1DeployQaScorecardRevisionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deploy', [$params], GoogleCloudContactcenterinsightsV1QaScorecardRevision::class);
  }
  /**
   * Gets a QaScorecardRevision. (revisions.get)
   *
   * @param string $name Required. The name of the QaScorecardRevision to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1QaScorecardRevision
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1QaScorecardRevision::class);
  }
  /**
   * Lists all revisions under the parent QaScorecard.
   * (revisions.listProjectsLocationsQaScorecardsRevisions)
   *
   * @param string $parent Required. The parent resource of the scorecard
   * revisions. To list all revisions of all scorecards, substitute the
   * QaScorecard ID with a '-' character.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to reduce results to a specific
   * subset. Useful for querying scorecard revisions with specific properties.
   * @opt_param int pageSize Optional. The maximum number of scorecard revisions
   * to return in the response. If the value is zero, the service will select a
   * default size. A call might return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListQaScorecardRevisionsResponse`. This value indicates that this is a
   * continuation of a prior `ListQaScorecardRevisions` call and that the system
   * should return the next page of data.
   * @opt_param string qaScorecardSources Optional. The source of scorecards are
   * based on how those Scorecards were created, e.g., a customer-defined
   * scorecard, a predefined scorecard, etc. This field is used to retrieve
   * Scorecards Revisions from Scorecards of one or more sources.
   * @return GoogleCloudContactcenterinsightsV1ListQaScorecardRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsQaScorecardsRevisions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListQaScorecardRevisionsResponse::class);
  }
  /**
   * Fine tune one or more QaModels. (revisions.tuneQaScorecardRevision)
   *
   * @param string $parent Required. The parent resource for new fine tuning job
   * instance.
   * @param GoogleCloudContactcenterinsightsV1TuneQaScorecardRevisionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function tuneQaScorecardRevision($parent, GoogleCloudContactcenterinsightsV1TuneQaScorecardRevisionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('tuneQaScorecardRevision', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Undeploy a QaScorecardRevision. (revisions.undeploy)
   *
   * @param string $name Required. The name of the QaScorecardRevision to
   * undeploy.
   * @param GoogleCloudContactcenterinsightsV1UndeployQaScorecardRevisionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1QaScorecardRevision
   * @throws \Google\Service\Exception
   */
  public function undeploy($name, GoogleCloudContactcenterinsightsV1UndeployQaScorecardRevisionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undeploy', [$params], GoogleCloudContactcenterinsightsV1QaScorecardRevision::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsQaScorecardsRevisions::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsQaScorecardsRevisions');
