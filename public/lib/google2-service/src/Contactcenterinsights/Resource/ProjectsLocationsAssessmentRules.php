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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1AssessmentRule;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAssessmentRulesResponse;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "assessmentRules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $assessmentRules = $contactcenterinsightsService->projects_locations_assessmentRules;
 *  </code>
 */
class ProjectsLocationsAssessmentRules extends \Google\Service\Resource
{
  /**
   * Creates an assessment rule. (assessmentRules.create)
   *
   * @param string $parent Required. The parent resource of the assessment rule.
   * Required. The location to create a assessment rule for. Format:
   * `projects//locations/` or `projects//locations/`
   * @param GoogleCloudContactcenterinsightsV1AssessmentRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string assessmentRuleId Optional. A unique ID for the new
   * AssessmentRule. This ID will become the final component of the
   * AssessmentRule's resource name. If no ID is specified, a server-generated ID
   * will be used. This value should be 4-64 characters and must match the regular
   * expression `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`.
   * @return GoogleCloudContactcenterinsightsV1AssessmentRule
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1AssessmentRule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1AssessmentRule::class);
  }
  /**
   * Deletes an assessment rule. (assessmentRules.delete)
   *
   * @param string $name Required. The name of the assessment rule to delete.
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
   * Get an assessment rule. (assessmentRules.get)
   *
   * @param string $name Required. The name of the assessment rule to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1AssessmentRule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1AssessmentRule::class);
  }
  /**
   * Lists assessment rules.
   * (assessmentRules.listProjectsLocationsAssessmentRules)
   *
   * @param string $parent Required. The parent resource of the assessment rules.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of assessment rule to
   * return in the response. If this value is zero, the service will select a
   * default size. A call may return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAssessmentRulesResponse`; indicates that this is a continuation of a
   * prior `ListAssessmentRules` call and the system should return the next page
   * of data.
   * @return GoogleCloudContactcenterinsightsV1ListAssessmentRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAssessmentRules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListAssessmentRulesResponse::class);
  }
  /**
   * Updates an assessment rule. (assessmentRules.patch)
   *
   * @param string $name Identifier. The resource name of the assessment rule.
   * Format:
   * projects/{project}/locations/{location}/assessmentRules/{assessment_rule}
   * @param GoogleCloudContactcenterinsightsV1AssessmentRule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to be updated. If
   * the update_mask is not provided, the update will be applied to all fields.
   * @return GoogleCloudContactcenterinsightsV1AssessmentRule
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1AssessmentRule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1AssessmentRule::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAssessmentRules::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsAssessmentRules');
