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

namespace Google\Service\Directory;

class UserOrganization extends \Google\Model
{
  /**
   * The cost center of the users department.
   *
   * @var string
   */
  public $costCenter;
  /**
   * Custom type.
   *
   * @var string
   */
  public $customType;
  /**
   * Department within the organization.
   *
   * @var string
   */
  public $department;
  /**
   * Description of the organization.
   *
   * @var string
   */
  public $description;
  /**
   * The domain to which the organization belongs to.
   *
   * @var string
   */
  public $domain;
  /**
   * The full-time equivalent millipercent within the organization (100000 =
   * 100%).
   *
   * @var int
   */
  public $fullTimeEquivalent;
  /**
   * Location of the organization. This need not be fully qualified address.
   *
   * @var string
   */
  public $location;
  /**
   * Name of the organization
   *
   * @var string
   */
  public $name;
  /**
   * If it user's primary organization.
   *
   * @var bool
   */
  public $primary;
  /**
   * Symbol of the organization.
   *
   * @var string
   */
  public $symbol;
  /**
   * Title (designation) of the user in the organization.
   *
   * @var string
   */
  public $title;
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example organization could be of school work etc. In addition to the
   * standard type an entry can have a custom type and can give it any name.
   * Such types should have the CUSTOM value as type and also have a CustomType
   * value.
   *
   * @var string
   */
  public $type;

  /**
   * The cost center of the users department.
   *
   * @param string $costCenter
   */
  public function setCostCenter($costCenter)
  {
    $this->costCenter = $costCenter;
  }
  /**
   * @return string
   */
  public function getCostCenter()
  {
    return $this->costCenter;
  }
  /**
   * Custom type.
   *
   * @param string $customType
   */
  public function setCustomType($customType)
  {
    $this->customType = $customType;
  }
  /**
   * @return string
   */
  public function getCustomType()
  {
    return $this->customType;
  }
  /**
   * Department within the organization.
   *
   * @param string $department
   */
  public function setDepartment($department)
  {
    $this->department = $department;
  }
  /**
   * @return string
   */
  public function getDepartment()
  {
    return $this->department;
  }
  /**
   * Description of the organization.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The domain to which the organization belongs to.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The full-time equivalent millipercent within the organization (100000 =
   * 100%).
   *
   * @param int $fullTimeEquivalent
   */
  public function setFullTimeEquivalent($fullTimeEquivalent)
  {
    $this->fullTimeEquivalent = $fullTimeEquivalent;
  }
  /**
   * @return int
   */
  public function getFullTimeEquivalent()
  {
    return $this->fullTimeEquivalent;
  }
  /**
   * Location of the organization. This need not be fully qualified address.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Name of the organization
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
   * If it user's primary organization.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Symbol of the organization.
   *
   * @param string $symbol
   */
  public function setSymbol($symbol)
  {
    $this->symbol = $symbol;
  }
  /**
   * @return string
   */
  public function getSymbol()
  {
    return $this->symbol;
  }
  /**
   * Title (designation) of the user in the organization.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example organization could be of school work etc. In addition to the
   * standard type an entry can have a custom type and can give it any name.
   * Such types should have the CUSTOM value as type and also have a CustomType
   * value.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserOrganization::class, 'Google_Service_Directory_UserOrganization');
