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

namespace Google\Service\Playcustomapp;

class Organization extends \Google\Model
{
  /**
   * Required. ID of the organization.
   *
   * @var string
   */
  public $organizationId;
  /**
   * Optional. A human-readable name of the organization, to help recognize the
   * organization.
   *
   * @var string
   */
  public $organizationName;

  /**
   * Required. ID of the organization.
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
  /**
   * Optional. A human-readable name of the organization, to help recognize the
   * organization.
   *
   * @param string $organizationName
   */
  public function setOrganizationName($organizationName)
  {
    $this->organizationName = $organizationName;
  }
  /**
   * @return string
   */
  public function getOrganizationName()
  {
    return $this->organizationName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Organization::class, 'Google_Service_Playcustomapp_Organization');
