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

namespace Google\Service\AndroidManagement;

class ContactInfo extends \Google\Model
{
  /**
   * Email address for a point of contact, which will be used to send important
   * announcements related to managed Google Play.
   *
   * @var string
   */
  public $contactEmail;
  /**
   * The email of the data protection officer. The email is validated but not
   * verified.
   *
   * @var string
   */
  public $dataProtectionOfficerEmail;
  /**
   * The name of the data protection officer.
   *
   * @var string
   */
  public $dataProtectionOfficerName;
  /**
   * The phone number of the data protection officer The phone number is
   * validated but not verified.
   *
   * @var string
   */
  public $dataProtectionOfficerPhone;
  /**
   * The email of the EU representative. The email is validated but not
   * verified.
   *
   * @var string
   */
  public $euRepresentativeEmail;
  /**
   * The name of the EU representative.
   *
   * @var string
   */
  public $euRepresentativeName;
  /**
   * The phone number of the EU representative. The phone number is validated
   * but not verified.
   *
   * @var string
   */
  public $euRepresentativePhone;

  /**
   * Email address for a point of contact, which will be used to send important
   * announcements related to managed Google Play.
   *
   * @param string $contactEmail
   */
  public function setContactEmail($contactEmail)
  {
    $this->contactEmail = $contactEmail;
  }
  /**
   * @return string
   */
  public function getContactEmail()
  {
    return $this->contactEmail;
  }
  /**
   * The email of the data protection officer. The email is validated but not
   * verified.
   *
   * @param string $dataProtectionOfficerEmail
   */
  public function setDataProtectionOfficerEmail($dataProtectionOfficerEmail)
  {
    $this->dataProtectionOfficerEmail = $dataProtectionOfficerEmail;
  }
  /**
   * @return string
   */
  public function getDataProtectionOfficerEmail()
  {
    return $this->dataProtectionOfficerEmail;
  }
  /**
   * The name of the data protection officer.
   *
   * @param string $dataProtectionOfficerName
   */
  public function setDataProtectionOfficerName($dataProtectionOfficerName)
  {
    $this->dataProtectionOfficerName = $dataProtectionOfficerName;
  }
  /**
   * @return string
   */
  public function getDataProtectionOfficerName()
  {
    return $this->dataProtectionOfficerName;
  }
  /**
   * The phone number of the data protection officer The phone number is
   * validated but not verified.
   *
   * @param string $dataProtectionOfficerPhone
   */
  public function setDataProtectionOfficerPhone($dataProtectionOfficerPhone)
  {
    $this->dataProtectionOfficerPhone = $dataProtectionOfficerPhone;
  }
  /**
   * @return string
   */
  public function getDataProtectionOfficerPhone()
  {
    return $this->dataProtectionOfficerPhone;
  }
  /**
   * The email of the EU representative. The email is validated but not
   * verified.
   *
   * @param string $euRepresentativeEmail
   */
  public function setEuRepresentativeEmail($euRepresentativeEmail)
  {
    $this->euRepresentativeEmail = $euRepresentativeEmail;
  }
  /**
   * @return string
   */
  public function getEuRepresentativeEmail()
  {
    return $this->euRepresentativeEmail;
  }
  /**
   * The name of the EU representative.
   *
   * @param string $euRepresentativeName
   */
  public function setEuRepresentativeName($euRepresentativeName)
  {
    $this->euRepresentativeName = $euRepresentativeName;
  }
  /**
   * @return string
   */
  public function getEuRepresentativeName()
  {
    return $this->euRepresentativeName;
  }
  /**
   * The phone number of the EU representative. The phone number is validated
   * but not verified.
   *
   * @param string $euRepresentativePhone
   */
  public function setEuRepresentativePhone($euRepresentativePhone)
  {
    $this->euRepresentativePhone = $euRepresentativePhone;
  }
  /**
   * @return string
   */
  public function getEuRepresentativePhone()
  {
    return $this->euRepresentativePhone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactInfo::class, 'Google_Service_AndroidManagement_ContactInfo');
