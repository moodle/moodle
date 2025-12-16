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

class EmailAddress extends \Google\Model
{
  /**
   * If the value of type is custom, this property contains the custom type
   * string.
   *
   * @var string
   */
  public $customType;
  /**
   * The email address.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * The URL to send email.
   *
   * @var string
   */
  public $emailUrl;
  /**
   * Indicates if this is the user's primary email. Only one entry can be marked
   * as primary.
   *
   * @var bool
   */
  public $primary;
  /**
   * The type of the email account. Acceptable values are: "custom", "home",
   * "other", "work".
   *
   * @var string
   */
  public $type;

  /**
   * If the value of type is custom, this property contains the custom type
   * string.
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
   * The email address.
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
   * The URL to send email.
   *
   * @param string $emailUrl
   */
  public function setEmailUrl($emailUrl)
  {
    $this->emailUrl = $emailUrl;
  }
  /**
   * @return string
   */
  public function getEmailUrl()
  {
    return $this->emailUrl;
  }
  /**
   * Indicates if this is the user's primary email. Only one entry can be marked
   * as primary.
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
   * The type of the email account. Acceptable values are: "custom", "home",
   * "other", "work".
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
class_alias(EmailAddress::class, 'Google_Service_CloudSearch_EmailAddress');
