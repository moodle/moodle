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

namespace Google\Service\Books;

class FamilyInfoMembership extends \Google\Model
{
  /**
   * Restrictions on user buying and acquiring content.
   *
   * @var string
   */
  public $acquirePermission;
  /**
   * The age group of the user.
   *
   * @var string
   */
  public $ageGroup;
  /**
   * The maximum allowed maturity rating for the user.
   *
   * @var string
   */
  public $allowedMaturityRating;
  /**
   * @var bool
   */
  public $isInFamily;
  /**
   * The role of the user in the family.
   *
   * @var string
   */
  public $role;

  /**
   * Restrictions on user buying and acquiring content.
   *
   * @param string $acquirePermission
   */
  public function setAcquirePermission($acquirePermission)
  {
    $this->acquirePermission = $acquirePermission;
  }
  /**
   * @return string
   */
  public function getAcquirePermission()
  {
    return $this->acquirePermission;
  }
  /**
   * The age group of the user.
   *
   * @param string $ageGroup
   */
  public function setAgeGroup($ageGroup)
  {
    $this->ageGroup = $ageGroup;
  }
  /**
   * @return string
   */
  public function getAgeGroup()
  {
    return $this->ageGroup;
  }
  /**
   * The maximum allowed maturity rating for the user.
   *
   * @param string $allowedMaturityRating
   */
  public function setAllowedMaturityRating($allowedMaturityRating)
  {
    $this->allowedMaturityRating = $allowedMaturityRating;
  }
  /**
   * @return string
   */
  public function getAllowedMaturityRating()
  {
    return $this->allowedMaturityRating;
  }
  /**
   * @param bool $isInFamily
   */
  public function setIsInFamily($isInFamily)
  {
    $this->isInFamily = $isInFamily;
  }
  /**
   * @return bool
   */
  public function getIsInFamily()
  {
    return $this->isInFamily;
  }
  /**
   * The role of the user in the family.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FamilyInfoMembership::class, 'Google_Service_Books_FamilyInfoMembership');
