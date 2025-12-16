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

namespace Google\Service\RapidMigrationAssessment\Resource;

use Google\Service\RapidMigrationAssessment\Annotation;
use Google\Service\RapidMigrationAssessment\Operation;

/**
 * The "annotations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $rapidmigrationassessmentService = new Google\Service\RapidMigrationAssessment(...);
 *   $annotations = $rapidmigrationassessmentService->projects_locations_annotations;
 *  </code>
 */
class ProjectsLocationsAnnotations extends \Google\Service\Resource
{
  /**
   * Creates an Annotation (annotations.create)
   *
   * @param string $parent Required. Name of the parent (project+location).
   * @param Annotation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Annotation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Gets details of a single Annotation. (annotations.get)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   * @return Annotation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Annotation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAnnotations::class, 'Google_Service_RapidMigrationAssessment_Resource_ProjectsLocationsAnnotations');
