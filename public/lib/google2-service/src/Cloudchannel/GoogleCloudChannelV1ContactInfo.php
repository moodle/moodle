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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1ContactInfo extends \Google\Model
{
  /**
   * Output only. The customer account contact's display name, formatted as a
   * combination of the customer's first and last name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The customer account's contact email. Required for entitlements that create
   * admin.google.com accounts, and serves as the customer's username for those
   * accounts. Use this email to invite Team customers.
   *
   * @var string
   */
  public $email;
  /**
   * The customer account contact's first name. Optional for Team customers.
   *
   * @var string
   */
  public $firstName;
  /**
   * The customer account contact's last name. Optional for Team customers.
   *
   * @var string
   */
  public $lastName;
  /**
   * The customer account's contact phone number.
   *
   * @var string
   */
  public $phone;
  /**
   * Optional. The customer account contact's job title.
   *
   * @var string
   */
  public $title;

  /**
   * Output only. The customer account contact's display name, formatted as a
   * combination of the customer's first and last name.
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
   * The customer account's contact email. Required for entitlements that create
   * admin.google.com accounts, and serves as the customer's username for those
   * accounts. Use this email to invite Team customers.
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
   * The customer account contact's first name. Optional for Team customers.
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
   * The customer account contact's last name. Optional for Team customers.
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
   * The customer account's contact phone number.
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
   * Optional. The customer account contact's job title.
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
class_alias(GoogleCloudChannelV1ContactInfo::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ContactInfo');
