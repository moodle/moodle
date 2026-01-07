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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadWorkloadOptions extends \Google\Model
{
  /**
   * KAJ Enrollment type is unspecified
   */
  public const KAJ_ENROLLMENT_TYPE_KAJ_ENROLLMENT_TYPE_UNSPECIFIED = 'KAJ_ENROLLMENT_TYPE_UNSPECIFIED';
  /**
   * KAT sets External, Hardware, and Software key feature logging only to TRUE.
   */
  public const KAJ_ENROLLMENT_TYPE_KEY_ACCESS_TRANSPARENCY_OFF = 'KEY_ACCESS_TRANSPARENCY_OFF';
  /**
   * Optional. Specifies type of KAJ Enrollment if provided.
   *
   * @var string
   */
  public $kajEnrollmentType;

  /**
   * Optional. Specifies type of KAJ Enrollment if provided.
   *
   * Accepted values: KAJ_ENROLLMENT_TYPE_UNSPECIFIED,
   * KEY_ACCESS_TRANSPARENCY_OFF
   *
   * @param self::KAJ_ENROLLMENT_TYPE_* $kajEnrollmentType
   */
  public function setKajEnrollmentType($kajEnrollmentType)
  {
    $this->kajEnrollmentType = $kajEnrollmentType;
  }
  /**
   * @return self::KAJ_ENROLLMENT_TYPE_*
   */
  public function getKajEnrollmentType()
  {
    return $this->kajEnrollmentType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadWorkloadOptions::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadWorkloadOptions');
