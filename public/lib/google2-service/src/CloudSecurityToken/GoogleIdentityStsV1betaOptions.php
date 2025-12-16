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

namespace Google\Service\CloudSecurityToken;

class GoogleIdentityStsV1betaOptions extends \Google\Model
{
  protected $accessBoundaryType = GoogleIdentityStsV1betaAccessBoundary::class;
  protected $accessBoundaryDataType = '';
  /**
   * A Google project used for quota and billing purposes when the credential is
   * used to access Google APIs. The provided project overrides the project
   * bound to the credential. The value must be a project number or a project
   * ID. Example: `my-sample-project-191923`. The maximum length is 32
   * characters.
   *
   * @var string
   */
  public $userProject;

  /**
   * An access boundary that defines the upper bound of permissions the
   * credential may have. The value should be a JSON object of AccessBoundary.
   * The access boundary can include up to 10 rules. The size of the parameter
   * value should not exceed 2048 characters.
   *
   * @param GoogleIdentityStsV1betaAccessBoundary $accessBoundary
   */
  public function setAccessBoundary(GoogleIdentityStsV1betaAccessBoundary $accessBoundary)
  {
    $this->accessBoundary = $accessBoundary;
  }
  /**
   * @return GoogleIdentityStsV1betaAccessBoundary
   */
  public function getAccessBoundary()
  {
    return $this->accessBoundary;
  }
  /**
   * A Google project used for quota and billing purposes when the credential is
   * used to access Google APIs. The provided project overrides the project
   * bound to the credential. The value must be a project number or a project
   * ID. Example: `my-sample-project-191923`. The maximum length is 32
   * characters.
   *
   * @param string $userProject
   */
  public function setUserProject($userProject)
  {
    $this->userProject = $userProject;
  }
  /**
   * @return string
   */
  public function getUserProject()
  {
    return $this->userProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityStsV1betaOptions::class, 'Google_Service_CloudSecurityToken_GoogleIdentityStsV1betaOptions');
