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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1AppealAssessmentRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1Assessment;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1FinalizeAssessmentRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAssessmentsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1PublishAssessmentRequest;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "assessments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $assessments = $contactcenterinsightsService->projects_locations_conversations_assessments;
 *  </code>
 */
class ProjectsLocationsConversationsAssessments extends \Google\Service\Resource
{
  /**
   * Appeal an Assessment. (assessments.appeal)
   *
   * @param string $name Required. The name of the assessment to appeal.
   * @param GoogleCloudContactcenterinsightsV1AppealAssessmentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Assessment
   * @throws \Google\Service\Exception
   */
  public function appeal($name, GoogleCloudContactcenterinsightsV1AppealAssessmentRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('appeal', [$params], GoogleCloudContactcenterinsightsV1Assessment::class);
  }
  /**
   * Create Assessment. (assessments.create)
   *
   * @param string $parent Required. The parent resource of the assessment.
   * @param GoogleCloudContactcenterinsightsV1Assessment $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Assessment
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1Assessment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1Assessment::class);
  }
  /**
   * Delete an Assessment. (assessments.delete)
   *
   * @param string $name Required. The name of the assessment to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, all of this assessment's
   * notes will also be deleted. Otherwise, the request will only succeed if it
   * has no notes.
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
   * Finalize an Assessment. (assessments.finalize)
   *
   * @param string $name Required. The name of the assessment to finalize.
   * @param GoogleCloudContactcenterinsightsV1FinalizeAssessmentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Assessment
   * @throws \Google\Service\Exception
   */
  public function finalize($name, GoogleCloudContactcenterinsightsV1FinalizeAssessmentRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('finalize', [$params], GoogleCloudContactcenterinsightsV1Assessment::class);
  }
  /**
   * Get Assessment. (assessments.get)
   *
   * @param string $name Required. The name of the assessment to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Assessment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1Assessment::class);
  }
  /**
   * List Assessments. (assessments.listProjectsLocationsConversationsAssessments)
   *
   * @param string $parent Required. The parent resource of the assessments. To
   * list all assessments in a location, substitute the conversation ID with a '-'
   * character.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to reduce results to a specific
   * subset. Supported filters include: * `state` - The state of the assessment *
   * `agent_info.agent_id` - The ID of the agent the assessment is for
   * @opt_param int pageSize The maximum number of assessments to list. If zero,
   * the service will select a default size. A call may return fewer objects than
   * requested. A non-empty `next_page_token` in the response indicates that more
   * data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAssessmentRulesResponse`; indicates that this is a continuation of a
   * prior `ListAssessmentRules` call and the system should return the next page
   * of data.
   * @return GoogleCloudContactcenterinsightsV1ListAssessmentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConversationsAssessments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListAssessmentsResponse::class);
  }
  /**
   * Publish an Assessment. (assessments.publish)
   *
   * @param string $name Required. The name of the assessment to publish.
   * @param GoogleCloudContactcenterinsightsV1PublishAssessmentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Assessment
   * @throws \Google\Service\Exception
   */
  public function publish($name, GoogleCloudContactcenterinsightsV1PublishAssessmentRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params], GoogleCloudContactcenterinsightsV1Assessment::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConversationsAssessments::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsConversationsAssessments');
