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

namespace Google\Service\CloudRetail\Resource;

use Google\Service\CloudRetail\GoogleCloudRetailV2BatchUpdateGenerativeQuestionConfigsRequest;
use Google\Service\CloudRetail\GoogleCloudRetailV2BatchUpdateGenerativeQuestionConfigsResponse;

/**
 * The "generativeQuestion" collection of methods.
 * Typical usage is:
 *  <code>
 *   $retailService = new Google\Service\CloudRetail(...);
 *   $generativeQuestion = $retailService->projects_locations_catalogs_generativeQuestion;
 *  </code>
 */
class ProjectsLocationsCatalogsGenerativeQuestion extends \Google\Service\Resource
{
  /**
   * Allows management of multiple questions. (generativeQuestion.batchUpdate)
   *
   * @param string $parent Optional. Resource name of the parent catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   * @param GoogleCloudRetailV2BatchUpdateGenerativeQuestionConfigsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRetailV2BatchUpdateGenerativeQuestionConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($parent, GoogleCloudRetailV2BatchUpdateGenerativeQuestionConfigsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], GoogleCloudRetailV2BatchUpdateGenerativeQuestionConfigsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCatalogsGenerativeQuestion::class, 'Google_Service_CloudRetail_Resource_ProjectsLocationsCatalogsGenerativeQuestion');
