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

namespace Google\Service\Sheets;

class PersonProperties extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const DISPLAY_FORMAT_DISPLAY_FORMAT_UNSPECIFIED = 'DISPLAY_FORMAT_UNSPECIFIED';
  /**
   * Default display format.
   */
  public const DISPLAY_FORMAT_DEFAULT = 'DEFAULT';
  /**
   * Last name, first name display format.
   */
  public const DISPLAY_FORMAT_LAST_NAME_COMMA_FIRST_NAME = 'LAST_NAME_COMMA_FIRST_NAME';
  /**
   * Email display format.
   */
  public const DISPLAY_FORMAT_EMAIL = 'EMAIL';
  /**
   * Optional. The display format of the person chip. If not set, the default
   * display format is used.
   *
   * @var string
   */
  public $displayFormat;
  /**
   * Required. The email address linked to this person. This field is always
   * present.
   *
   * @var string
   */
  public $email;

  /**
   * Optional. The display format of the person chip. If not set, the default
   * display format is used.
   *
   * Accepted values: DISPLAY_FORMAT_UNSPECIFIED, DEFAULT,
   * LAST_NAME_COMMA_FIRST_NAME, EMAIL
   *
   * @param self::DISPLAY_FORMAT_* $displayFormat
   */
  public function setDisplayFormat($displayFormat)
  {
    $this->displayFormat = $displayFormat;
  }
  /**
   * @return self::DISPLAY_FORMAT_*
   */
  public function getDisplayFormat()
  {
    return $this->displayFormat;
  }
  /**
   * Required. The email address linked to this person. This field is always
   * present.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersonProperties::class, 'Google_Service_Sheets_PersonProperties');
