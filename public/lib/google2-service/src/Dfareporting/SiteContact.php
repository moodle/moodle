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

namespace Google\Service\Dfareporting;

class SiteContact extends \Google\Model
{
  public const CONTACT_TYPE_SALES_PERSON = 'SALES_PERSON';
  public const CONTACT_TYPE_TRAFFICKER = 'TRAFFICKER';
  /**
   * Address of this site contact.
   *
   * @var string
   */
  public $address;
  /**
   * Site contact type.
   *
   * @var string
   */
  public $contactType;
  /**
   * Email address of this site contact. This is a required field.
   *
   * @var string
   */
  public $email;
  /**
   * First name of this site contact.
   *
   * @var string
   */
  public $firstName;
  /**
   * ID of this site contact. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Last name of this site contact.
   *
   * @var string
   */
  public $lastName;
  /**
   * Primary phone number of this site contact.
   *
   * @var string
   */
  public $phone;
  /**
   * Title or designation of this site contact.
   *
   * @var string
   */
  public $title;

  /**
   * Address of this site contact.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Site contact type.
   *
   * Accepted values: SALES_PERSON, TRAFFICKER
   *
   * @param self::CONTACT_TYPE_* $contactType
   */
  public function setContactType($contactType)
  {
    $this->contactType = $contactType;
  }
  /**
   * @return self::CONTACT_TYPE_*
   */
  public function getContactType()
  {
    return $this->contactType;
  }
  /**
   * Email address of this site contact. This is a required field.
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
   * First name of this site contact.
   *
   * @param string $firstName
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }
  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }
  /**
   * ID of this site contact. This is a read-only, auto-generated field.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Last name of this site contact.
   *
   * @param string $lastName
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }
  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }
  /**
   * Primary phone number of this site contact.
   *
   * @param string $phone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
  }
  /**
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }
  /**
   * Title or designation of this site contact.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SiteContact::class, 'Google_Service_Dfareporting_SiteContact');
