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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListQaQuestionTagsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QaQuestionTag;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;

/**
 * The "qaQuestionTags" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $qaQuestionTags = $contactcenterinsightsService->projects_locations_qaQuestionTags;
 *  </code>
 */
class ProjectsLocationsQaQuestionTags extends \Google\Service\Resource
{
  /**
   * Creates a QaQuestionTag. (qaQuestionTags.create)
   *
   * @param string $parent Required. The parent resource of the QaQuestionTag.
   * @param GoogleCloudContactcenterinsightsV1QaQuestionTag $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string qaQuestionTagId Optional. A unique ID for the new
   * QaQuestionTag. This ID will become the final component of the QaQuestionTag's
   * resource name. If no ID is specified, a server-generated ID will be used.
   * This value should be 4-64 characters and must match the regular expression
   * `^[a-z0-9-]{4,64}$`. Valid characters are `a-z-`.
   * @return GoogleCloudContactcenterinsightsV1QaQuestionTag
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1QaQuestionTag $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1QaQuestionTag::class);
  }
  /**
   * Deletes a QaQuestionTag. (qaQuestionTags.delete)
   *
   * @param string $name Required. The name of the QaQuestionTag to delete.
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
   * Gets a QaQuestionTag. (qaQuestionTags.get)
   *
   * @param string $name Required. The name of the QaQuestionTag to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1QaQuestionTag
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1QaQuestionTag::class);
  }
  /**
   * Lists the question tags. (qaQuestionTags.listProjectsLocationsQaQuestionTags)
   *
   * @param string $parent Required. The parent resource of the QaQuestionTags.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to reduce results to a specific
   * subset. Supports conjunctions (ie. AND operators). Supported fields include
   * the following: * `project_id` - id of the project to list tags for *
   * `qa_scorecard_id` - id of the scorecard to list tags for * `revision_id` - id
   * of the scorecard revision to list tags for` * `qa_question_id - id of the
   * question to list tags for`
   * @return GoogleCloudContactcenterinsightsV1ListQaQuestionTagsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsQaQuestionTags($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListQaQuestionTagsResponse::class);
  }
  /**
   * Updates a QaQuestionTag. (qaQuestionTags.patch)
   *
   * @param string $name Identifier. Resource name for the QaQuestionTag Format
   * projects/{project}/locations/{location}/qaQuestionTags/{qa_question_tag} In
   * the above format, the last segment, i.e., qa_question_tag, is a server-
   * generated ID corresponding to the tag resource.
   * @param GoogleCloudContactcenterinsightsV1QaQuestionTag $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to be updated. All
   * possible fields can be updated by passing `*`, or a subset of the following
   * updateable fields can be provided: * `qa_question_tag_name` - the name of the
   * tag * `qa_question_ids` - the list of questions the tag applies to
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1QaQuestionTag $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsQaQuestionTags::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsQaQuestionTags');
