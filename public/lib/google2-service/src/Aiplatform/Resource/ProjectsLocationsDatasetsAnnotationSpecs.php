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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1AnnotationSpec;

/**
 * The "annotationSpecs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $annotationSpecs = $aiplatformService->projects_locations_datasets_annotationSpecs;
 *  </code>
 */
class ProjectsLocationsDatasetsAnnotationSpecs extends \Google\Service\Resource
{
  /**
   * Gets an AnnotationSpec. (annotationSpecs.get)
   *
   * @param string $name Required. The name of the AnnotationSpec resource.
   * Format: `projects/{project}/locations/{location}/datasets/{dataset}/annotatio
   * nSpecs/{annotation_spec}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1AnnotationSpec
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1AnnotationSpec::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsAnnotationSpecs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsDatasetsAnnotationSpecs');
