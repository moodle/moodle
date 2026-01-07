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

namespace Google\Service\FirebaseManagement;

class ProjectInfo extends \Google\Model
{
  /**
   * The user-assigned display name of the Google Cloud `Project`, for example:
   * `My App`.
   *
   * @var string
   */
  public $displayName;
  /**
   * **DEPRECATED** _Instead, use product-specific REST APIs to work with the
   * location of each resource in a Project. This field may not be populated,
   * especially for newly provisioned projects after October 30, 2024._ The ID
   * of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location). The location is one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region). Not all
   * Projects will have this field populated. If it is not populated, it means
   * that the Project does not yet have a location for default Google Cloud
   * resources.
   *
   * @var string
   */
  public $locationId;
  /**
   * The resource name of the Google Cloud `Project` to which Firebase resources
   * can be added, in the format: projects/PROJECT_IDENTIFIER Refer to the
   * `FirebaseProject` [`name`](../projects#FirebaseProject.FIELDS.name) field
   * for details about PROJECT_IDENTIFIER values.
   *
   * @var string
   */
  public $project;

  /**
   * The user-assigned display name of the Google Cloud `Project`, for example:
   * `My App`.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * **DEPRECATED** _Instead, use product-specific REST APIs to work with the
   * location of each resource in a Project. This field may not be populated,
   * especially for newly provisioned projects after October 30, 2024._ The ID
   * of the Project's ["location for default Google Cloud
   * resources"](https://firebase.google.com/docs/projects/locations#default-
   * cloud-location). The location is one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region). Not all
   * Projects will have this field populated. If it is not populated, it means
   * that the Project does not yet have a location for default Google Cloud
   * resources.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * The resource name of the Google Cloud `Project` to which Firebase resources
   * can be added, in the format: projects/PROJECT_IDENTIFIER Refer to the
   * `FirebaseProject` [`name`](../projects#FirebaseProject.FIELDS.name) field
   * for details about PROJECT_IDENTIFIER values.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectInfo::class, 'Google_Service_FirebaseManagement_ProjectInfo');
