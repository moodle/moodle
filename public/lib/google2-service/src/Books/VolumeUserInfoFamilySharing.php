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

class VolumeUserInfoFamilySharing extends \Google\Model
{
  /**
   * The role of the user in the family.
   *
   * @var string
   */
  public $familyRole;
  /**
   * Whether or not this volume can be shared with the family by the user. This
   * includes sharing eligibility of both the volume and the user. If the value
   * is true, the user can initiate a family sharing action.
   *
   * @var bool
   */
  public $isSharingAllowed;
  /**
   * Whether or not sharing this volume is temporarily disabled due to issues
   * with the Family Wallet.
   *
   * @var bool
   */
  public $isSharingDisabledByFop;

  /**
   * The role of the user in the family.
   *
   * @param string $familyRole
   */
  public function setFamilyRole($familyRole)
  {
    $this->familyRole = $familyRole;
  }
  /**
   * @return string
   */
  public function getFamilyRole()
  {
    return $this->familyRole;
  }
  /**
   * Whether or not this volume can be shared with the family by the user. This
   * includes sharing eligibility of both the volume and the user. If the value
   * is true, the user can initiate a family sharing action.
   *
   * @param bool $isSharingAllowed
   */
  public function setIsSharingAllowed($isSharingAllowed)
  {
    $this->isSharingAllowed = $isSharingAllowed;
  }
  /**
   * @return bool
   */
  public function getIsSharingAllowed()
  {
    return $this->isSharingAllowed;
  }
  /**
   * Whether or not sharing this volume is temporarily disabled due to issues
   * with the Family Wallet.
   *
   * @param bool $isSharingDisabledByFop
   */
  public function setIsSharingDisabledByFop($isSharingDisabledByFop)
  {
    $this->isSharingDisabledByFop = $isSharingDisabledByFop;
  }
  /**
   * @return bool
   */
  public function getIsSharingDisabledByFop()
  {
    return $this->isSharingDisabledByFop;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeUserInfoFamilySharing::class, 'Google_Service_Books_VolumeUserInfoFamilySharing');
