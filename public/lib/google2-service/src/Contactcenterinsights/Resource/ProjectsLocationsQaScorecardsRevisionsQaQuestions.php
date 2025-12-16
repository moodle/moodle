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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListQaQuestionsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QaQuestion;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "qaQuestions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $qaQuestions = $contactcenterinsightsService->projects_locations_qaScorecards_revisions_qaQuestions;
 *  </code>
 */
class ProjectsLocationsQaScorecardsRevisionsQaQuestions extends \Google\Service\Resource
{
  /**
   * Create a QaQuestion. (qaQuestions.create)
   *
   * @param string $parent Required. The parent resource of the QaQuestion.
   * @param GoogleCloudContactcenterinsightsV1QaQuestion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string qaQuestionId Optional. A unique ID for the new question.
   * This ID will become the final component of the question's resource name. If
   * no ID is specified, a server-generated ID will be used. This value should be
   * 4-64 characters and must match the regular expression `^[a-z0-9-]{4,64}$`.
   * Valid characters are `a-z-`.
   * @return GoogleCloudContactcenterinsightsV1QaQuestion
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1QaQuestion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1QaQuestion::class);
  }
  /**
   * Deletes a QaQuestion. (qaQuestions.delete)
   *
   * @param string $name Required. The name of the QaQuestion to delete.
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
   * Gets a QaQuestion. (qaQuestions.get)
   *
   * @param string $name Required. The name of the QaQuestion to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1QaQuestion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1QaQuestion::class);
  }
  /**
   * Lists QaQuestions.
   * (qaQuestions.listProjectsLocationsQaScorecardsRevisionsQaQuestions)
   *
   * @param string $parent Required. The parent resource of the questions.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of questions to return
   * in the response. If the value is zero, the service will select a default
   * size. A call might return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListQaQuestionsResponse`. This value indicates that this is a continuation
   * of a prior `ListQaQuestions` call and that the system should return the next
   * page of data.
   * @return GoogleCloudContactcenterinsightsV1ListQaQuestionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsQaScorecardsRevisionsQaQuestions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListQaQuestionsResponse::class);
  }
  /**
   * Updates a QaQuestion. (qaQuestions.patch)
   *
   * @param string $name Identifier. The resource name of the question. Format: pr
   * ojects/{project}/locations/{location}/qaScorecards/{qa_scorecard}/revisions/{
   * revision}/qaQuestions/{qa_question}
   * @param GoogleCloudContactcenterinsightsV1QaQuestion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated. All
   * possible fields can be updated by passing `*`, or a subset of the following
   * updateable fields can be provided: * `abbreviation` * `answer_choices` *
   * `answer_instructions` * `order` * `question_body` * `tags`
   * @return GoogleCloudContactcenterinsightsV1QaQuestion
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1QaQuestion $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1QaQuestion::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsQaScorecardsRevisionsQaQuestions::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsQaScorecardsRevisionsQaQuestions');
