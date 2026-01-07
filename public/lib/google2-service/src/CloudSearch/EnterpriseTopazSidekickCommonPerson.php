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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickCommonPerson extends \Google\Model
{
  protected $birthdayType = EnterpriseTopazSidekickCommonPersonBirthday::class;
  protected $birthdayDataType = '';
  /**
   * Cell phone number.
   *
   * @var string
   */
  public $cellPhone;
  /**
   * The department the person works in (e.g. Engineering).
   *
   * @var string
   */
  public $department;
  /**
   * Desk location (e.g. US-MTV-PR55-5-5B1I).
   *
   * @var string
   */
  public $deskLocation;
  /**
   * Work desk phone number.
   *
   * @var string
   */
  public $deskPhone;
  /**
   * The full name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Email.
   *
   * @var string
   */
  public $email;
  /**
   * The last name.
   *
   * @var string
   */
  public $familyName;
  /**
   * The fully formatted address (e.g. 1255 Pear Avenue, Mountain View 94043,
   * United States).
   *
   * @var string
   */
  public $fullAddress;
  /**
   * This field is deprecated. The obfuscated_id should be used instead.
   *
   * @deprecated
   * @var string
   */
  public $gaiaId;
  /**
   * The first name.
   *
   * @var string
   */
  public $givenName;
  /**
   * The person's job title (e.g. Software Engineer).
   *
   * @var string
   */
  public $jobTitle;
  protected $managerType = EnterpriseTopazSidekickCommonPerson::class;
  protected $managerDataType = '';
  /**
   * The obfuscated GAIA ID.
   *
   * @var string
   */
  public $obfuscatedId;
  /**
   * The URL for the Focus profile picture.
   *
   * @var string
   */
  public $photoUrl;
  /**
   * The street address (e.g. 1255 Pear Avenue).
   *
   * @var string
   */
  public $streetAddress;

  /**
   * The birthday.
   *
   * @param EnterpriseTopazSidekickCommonPersonBirthday $birthday
   */
  public function setBirthday(EnterpriseTopazSidekickCommonPersonBirthday $birthday)
  {
    $this->birthday = $birthday;
  }
  /**
   * @return EnterpriseTopazSidekickCommonPersonBirthday
   */
  public function getBirthday()
  {
    return $this->birthday;
  }
  /**
   * Cell phone number.
   *
   * @param string $cellPhone
   */
  public function setCellPhone($cellPhone)
  {
    $this->cellPhone = $cellPhone;
  }
  /**
   * @return string
   */
  public function getCellPhone()
  {
    return $this->cellPhone;
  }
  /**
   * The department the person works in (e.g. Engineering).
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
   * Desk location (e.g. US-MTV-PR55-5-5B1I).
   *
   * @param string $deskLocation
   */
  public function setDeskLocation($deskLocation)
  {
    $this->deskLocation = $deskLocation;
  }
  /**
   * @return string
   */
  public function getDeskLocation()
  {
    return $this->deskLocation;
  }
  /**
   * Work desk phone number.
   *
   * @param string $deskPhone
   */
  public function setDeskPhone($deskPhone)
  {
    $this->deskPhone = $deskPhone;
  }
  /**
   * @return string
   */
  public function getDeskPhone()
  {
    return $this->deskPhone;
  }
  /**
   * The full name.
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
   * Email.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The last name.
   *
   * @param string $familyName
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;
  }
  /**
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }
  /**
   * The fully formatted address (e.g. 1255 Pear Avenue, Mountain View 94043,
   * United States).
   *
   * @param string $fullAddress
   */
  public function setFullAddress($fullAddress)
  {
    $this->fullAddress = $fullAddress;
  }
  /**
   * @return string
   */
  public function getFullAddress()
  {
    return $this->fullAddress;
  }
  /**
   * This field is deprecated. The obfuscated_id should be used instead.
   *
   * @deprecated
   * @param string $gaiaId
   */
  public function setGaiaId($gaiaId)
  {
    $this->gaiaId = $gaiaId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGaiaId()
  {
    return $this->gaiaId;
  }
  /**
   * The first name.
   *
   * @param string $givenName
   */
  public function setGivenName($givenName)
  {
    $this->givenName = $givenName;
  }
  /**
   * @return string
   */
  public function getGivenName()
  {
    return $this->givenName;
  }
  /**
   * The person's job title (e.g. Software Engineer).
   *
   * @param string $jobTitle
   */
  public function setJobTitle($jobTitle)
  {
    $this->jobTitle = $jobTitle;
  }
  /**
   * @return string
   */
  public function getJobTitle()
  {
    return $this->jobTitle;
  }
  /**
   * The manager.
   *
   * @param EnterpriseTopazSidekickCommonPerson $manager
   */
  public function setManager(EnterpriseTopazSidekickCommonPerson $manager)
  {
    $this->manager = $manager;
  }
  /**
   * @return EnterpriseTopazSidekickCommonPerson
   */
  public function getManager()
  {
    return $this->manager;
  }
  /**
   * The obfuscated GAIA ID.
   *
   * @param string $obfuscatedId
   */
  public function setObfuscatedId($obfuscatedId)
  {
    $this->obfuscatedId = $obfuscatedId;
  }
  /**
   * @return string
   */
  public function getObfuscatedId()
  {
    return $this->obfuscatedId;
  }
  /**
   * The URL for the Focus profile picture.
   *
   * @param string $photoUrl
   */
  public function setPhotoUrl($photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return string
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
  /**
   * The street address (e.g. 1255 Pear Avenue).
   *
   * @param string $streetAddress
   */
  public function setStreetAddress($streetAddress)
  {
    $this->streetAddress = $streetAddress;
  }
  /**
   * @return string
   */
  public function getStreetAddress()
  {
    return $this->streetAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickCommonPerson::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickCommonPerson');
