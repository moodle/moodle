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

namespace Google\Service\DisplayVideo;

class GenderTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when gender is not specified in this version. This enum is a
   * place holder for default value and does not represent a real gender option.
   */
  public const GENDER_GENDER_UNSPECIFIED = 'GENDER_UNSPECIFIED';
  /**
   * The audience gender is male.
   */
  public const GENDER_GENDER_MALE = 'GENDER_MALE';
  /**
   * The audience gender is female.
   */
  public const GENDER_GENDER_FEMALE = 'GENDER_FEMALE';
  /**
   * The audience gender is unknown.
   */
  public const GENDER_GENDER_UNKNOWN = 'GENDER_UNKNOWN';
  /**
   * Output only. The gender of an audience.
   *
   * @var string
   */
  public $gender;

  /**
   * Output only. The gender of an audience.
   *
   * Accepted values: GENDER_UNSPECIFIED, GENDER_MALE, GENDER_FEMALE,
   * GENDER_UNKNOWN
   *
   * @param self::GENDER_* $gender
   */
  public function setGender($gender)
  {
    $this->gender = $gender;
  }
  /**
   * @return self::GENDER_*
   */
  public function getGender()
  {
    return $this->gender;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenderTargetingOptionDetails::class, 'Google_Service_DisplayVideo_GenderTargetingOptionDetails');
