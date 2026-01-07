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

namespace Google\Service\CivicInfo;

class CivicinfoSchemaV2ElectionOfficial extends \Google\Model
{
  /**
   * The email address of the election official.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * The fax number of the election official.
   *
   * @var string
   */
  public $faxNumber;
  /**
   * The full name of the election official.
   *
   * @var string
   */
  public $name;
  /**
   * The office phone number of the election official.
   *
   * @var string
   */
  public $officePhoneNumber;
  /**
   * The title of the election official.
   *
   * @var string
   */
  public $title;

  /**
   * The email address of the election official.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * The fax number of the election official.
   *
   * @param string $faxNumber
   */
  public function setFaxNumber($faxNumber)
  {
    $this->faxNumber = $faxNumber;
  }
  /**
   * @return string
   */
  public function getFaxNumber()
  {
    return $this->faxNumber;
  }
  /**
   * The full name of the election official.
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
   * The office phone number of the election official.
   *
   * @param string $officePhoneNumber
   */
  public function setOfficePhoneNumber($officePhoneNumber)
  {
    $this->officePhoneNumber = $officePhoneNumber;
  }
  /**
   * @return string
   */
  public function getOfficePhoneNumber()
  {
    return $this->officePhoneNumber;
  }
  /**
   * The title of the election official.
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
class_alias(CivicinfoSchemaV2ElectionOfficial::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2ElectionOfficial');
