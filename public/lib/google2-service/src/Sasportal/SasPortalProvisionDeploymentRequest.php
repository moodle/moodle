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

namespace Google\Service\Sasportal;

class SasPortalProvisionDeploymentRequest extends \Google\Model
{
  /**
   * Optional. If this field is set, and a new SAS Portal Deployment needs to be
   * created, its display name will be set to the value of this field.
   *
   * @var string
   */
  public $newDeploymentDisplayName;
  /**
   * Optional. If this field is set, and a new SAS Portal Organization needs to
   * be created, its display name will be set to the value of this field.
   *
   * @var string
   */
  public $newOrganizationDisplayName;
  /**
   * Optional. If this field is set then a new deployment will be created under
   * the organization specified by this id.
   *
   * @var string
   */
  public $organizationId;

  /**
   * Optional. If this field is set, and a new SAS Portal Deployment needs to be
   * created, its display name will be set to the value of this field.
   *
   * @param string $newDeploymentDisplayName
   */
  public function setNewDeploymentDisplayName($newDeploymentDisplayName)
  {
    $this->newDeploymentDisplayName = $newDeploymentDisplayName;
  }
  /**
   * @return string
   */
  public function getNewDeploymentDisplayName()
  {
    return $this->newDeploymentDisplayName;
  }
  /**
   * Optional. If this field is set, and a new SAS Portal Organization needs to
   * be created, its display name will be set to the value of this field.
   *
   * @param string $newOrganizationDisplayName
   */
  public function setNewOrganizationDisplayName($newOrganizationDisplayName)
  {
    $this->newOrganizationDisplayName = $newOrganizationDisplayName;
  }
  /**
   * @return string
   */
  public function getNewOrganizationDisplayName()
  {
    return $this->newOrganizationDisplayName;
  }
  /**
   * Optional. If this field is set then a new deployment will be created under
   * the organization specified by this id.
   *
   * @param string $organizationId
   */
  public function setOrganizationId($organizationId)
  {
    $this->organizationId = $organizationId;
  }
  /**
   * @return string
   */
  public function getOrganizationId()
  {
    return $this->organizationId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalProvisionDeploymentRequest::class, 'Google_Service_Sasportal_SasPortalProvisionDeploymentRequest');
