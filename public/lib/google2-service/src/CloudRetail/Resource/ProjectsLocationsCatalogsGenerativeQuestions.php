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

use Google\Service\CloudRetail\GoogleCloudRetailV2ListGenerativeQuestionConfigsResponse;

/**
 * The "generativeQuestions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $retailService = new Google\Service\CloudRetail(...);
 *   $generativeQuestions = $retailService->projects_locations_catalogs_generativeQuestions;
 *  </code>
 */
class ProjectsLocationsCatalogsGenerativeQuestions extends \Google\Service\Resource
{
  /**
   * Returns all questions for a given catalog.
   * (generativeQuestions.listProjectsLocationsCatalogsGenerativeQuestions)
   *
   * @param string $parent Required. Resource name of the parent catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRetailV2ListGenerativeQuestionConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCatalogsGenerativeQuestions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRetailV2ListGenerativeQuestionConfigsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCatalogsGenerativeQuestions::class, 'Google_Service_CloudRetail_Resource_ProjectsLocationsCatalogsGenerativeQuestions');
