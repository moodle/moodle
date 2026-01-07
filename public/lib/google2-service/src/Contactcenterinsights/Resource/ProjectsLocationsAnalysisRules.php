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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1AnalysisRule;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAnalysisRulesResponse;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "analysisRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $analysisRules = $contactcenterinsightsService->projects_locations_analysisRules;
 *  </code>
 */
class ProjectsLocationsAnalysisRules extends \Google\Service\Resource
{
  /**
   * Creates a analysis rule. (analysisRules.create)
   *
   * @param string $parent Required. The parent resource of the analysis rule.
   * Required. The location to create a analysis rule for. Format:
   * `projects//locations/` or `projects//locations/`
   * @param GoogleCloudContactcenterinsightsV1AnalysisRule $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1AnalysisRule
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1AnalysisRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1AnalysisRule::class);
  }
  /**
   * Deletes a analysis rule. (analysisRules.delete)
   *
   * @param string $name Required. The name of the analysis rule to delete.
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
   * Get a analysis rule. (analysisRules.get)
   *
   * @param string $name Required. The name of the AnalysisRule to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1AnalysisRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1AnalysisRule::class);
  }
  /**
   * Lists analysis rules. (analysisRules.listProjectsLocationsAnalysisRules)
   *
   * @param string $parent Required. The parent resource of the analysis rules.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of analysis rule to
   * return in the response. If this value is zero, the service will select a
   * default size. A call may return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAnalysisRulesResponse`; indicates that this is a continuation of a prior
   * `ListAnalysisRules` call and the system should return the next page of data.
   * @return GoogleCloudContactcenterinsightsV1ListAnalysisRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAnalysisRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListAnalysisRulesResponse::class);
  }
  /**
   * Updates a analysis rule. (analysisRules.patch)
   *
   * @param string $name Identifier. The resource name of the analysis rule.
   * Format: projects/{project}/locations/{location}/analysisRules/{analysis_rule}
   * @param GoogleCloudContactcenterinsightsV1AnalysisRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to be updated. If
   * the update_mask is not provided, the update will be applied to all fields.
   * @return GoogleCloudContactcenterinsightsV1AnalysisRule
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1AnalysisRule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1AnalysisRule::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAnalysisRules::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsAnalysisRules');
