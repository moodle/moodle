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

namespace Google\Service\Document\Resource;

use Google\Service\Document\GoogleCloudDocumentaiV1Evaluation;
use Google\Service\Document\GoogleCloudDocumentaiV1ListEvaluationsResponse;

/**
 * The "evaluations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $documentaiService = new Google\Service\Document(...);
 *   $evaluations = $documentaiService->projects_locations_processors_processorVersions_evaluations;
 *  </code>
 */
class ProjectsLocationsProcessorsProcessorVersionsEvaluations extends \Google\Service\Resource
{
  /**
   * Retrieves a specific evaluation. (evaluations.get)
   *
   * @param string $name Required. The resource name of the Evaluation to get. `pr
   * ojects/{project}/locations/{location}/processors/{processor}/processorVersion
   * s/{processorVersion}/evaluations/{evaluation}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDocumentaiV1Evaluation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDocumentaiV1Evaluation::class);
  }
  /**
   * Retrieves a set of evaluations for a given processor version.
   * (evaluations.listProjectsLocationsProcessorsProcessorVersionsEvaluations)
   *
   * @param string $parent Required. The resource name of the ProcessorVersion to
   * list evaluations for. `projects/{project}/locations/{location}/processors/{pr
   * ocessor}/processorVersions/{processorVersion}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The standard list page size. If unspecified, at most
   * `5` evaluations are returned. The maximum value is `100`. Values above `100`
   * are coerced to `100`.
   * @opt_param string pageToken A page token, received from a previous
   * `ListEvaluations` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudDocumentaiV1ListEvaluationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProcessorsProcessorVersionsEvaluations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDocumentaiV1ListEvaluationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProcessorsProcessorVersionsEvaluations::class, 'Google_Service_Document_Resource_ProjectsLocationsProcessorsProcessorVersionsEvaluations');
