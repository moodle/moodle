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

namespace Google\Service\Walletobjects;

class IssuerContactInfo extends \Google\Collection
{
  protected $collection_key = 'alertsEmails';
  /**
   * Email addresses which will receive alerts.
   *
   * @var string[]
   */
  public $alertsEmails;
  /**
   * The primary contact email address.
   *
   * @var string
   */
  public $email;
  /**
   * The primary contact name.
   *
   * @var string
   */
  public $name;
  /**
   * The primary contact phone number.
   *
   * @var string
   */
  public $phone;

  /**
   * Email addresses which will receive alerts.
   *
   * @param string[] $alertsEmails
   */
  public function setAlertsEmails($alertsEmails)
  {
    $this->alertsEmails = $alertsEmails;
  }
  /**
   * @return string[]
   */
  public function getAlertsEmails()
  {
    return $this->alertsEmails;
  }
  /**
   * The primary contact email address.
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
   * The primary contact name.
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
   * The primary contact phone number.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IssuerContactInfo::class, 'Google_Service_Walletobjects_IssuerContactInfo');
