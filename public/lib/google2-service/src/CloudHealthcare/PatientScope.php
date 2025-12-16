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

namespace Google\Service\CloudHealthcare;

class PatientScope extends \Google\Collection
{
  protected $collection_key = 'patientIds';
  /**
   * Optional. The list of patient IDs whose Consent resources will be enforced.
   * At most 10,000 patients can be specified. An empty list is equivalent to
   * all patients (meaning the entire FHIR store).
   *
   * @var string[]
   */
  public $patientIds;

  /**
   * Optional. The list of patient IDs whose Consent resources will be enforced.
   * At most 10,000 patients can be specified. An empty list is equivalent to
   * all patients (meaning the entire FHIR store).
   *
   * @param string[] $patientIds
   */
  public function setPatientIds($patientIds)
  {
    $this->patientIds = $patientIds;
  }
  /**
   * @return string[]
   */
  public function getPatientIds()
  {
    return $this->patientIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PatientScope::class, 'Google_Service_CloudHealthcare_PatientScope');
