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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaFirebaseLink extends \Google\Model
{
  /**
   * Output only. Time when this FirebaseLink was originally created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Example format: properties/1234/firebaseLinks/5678
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. Firebase project resource name. When creating a FirebaseLink,
   * you may provide this resource name using either a project number or project
   * ID. Once this resource has been created, returned FirebaseLinks will always
   * have a project_name that contains a project number. Format:
   * 'projects/{project number}' Example: 'projects/1234'
   *
   * @var string
   */
  public $project;

  /**
   * Output only. Time when this FirebaseLink was originally created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Example format: properties/1234/firebaseLinks/5678
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Immutable. Firebase project resource name. When creating a FirebaseLink,
   * you may provide this resource name using either a project number or project
   * ID. Once this resource has been created, returned FirebaseLinks will always
   * have a project_name that contains a project number. Format:
   * 'projects/{project number}' Example: 'projects/1234'
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
class_alias(GoogleAnalyticsAdminV1betaFirebaseLink::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaFirebaseLink');
