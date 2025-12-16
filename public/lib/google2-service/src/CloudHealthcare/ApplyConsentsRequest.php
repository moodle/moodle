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

class ApplyConsentsRequest extends \Google\Model
{
  protected $patientScopeType = PatientScope::class;
  protected $patientScopeDataType = '';
  protected $timeRangeType = TimeRange::class;
  protected $timeRangeDataType = '';
  /**
   * Optional. If true, the method only validates Consent resources to make sure
   * they are supported. When the operation completes, ApplyConsentsResponse is
   * returned where `consent_apply_success` and `consent_apply_failure` indicate
   * supported and unsupported (or invalid) Consent resources, respectively.
   * Otherwise, the method propagates the aggregate consensual information to
   * the patient's resources. Upon success, `affected_resources` in the
   * ApplyConsentsResponse indicates the number of resources that may have
   * consensual access changed.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Optional. Scope down to a list of patients.
   *
   * @param PatientScope $patientScope
   */
  public function setPatientScope(PatientScope $patientScope)
  {
    $this->patientScope = $patientScope;
  }
  /**
   * @return PatientScope
   */
  public function getPatientScope()
  {
    return $this->patientScope;
  }
  /**
   * Optional. Scope down to patients whose most recent consent changes are in
   * the time range. Can only be used with a versioning store (i.e. when
   * disable_resource_versioning is set to false).
   *
   * @param TimeRange $timeRange
   */
  public function setTimeRange(TimeRange $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return TimeRange
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
  /**
   * Optional. If true, the method only validates Consent resources to make sure
   * they are supported. When the operation completes, ApplyConsentsResponse is
   * returned where `consent_apply_success` and `consent_apply_failure` indicate
   * supported and unsupported (or invalid) Consent resources, respectively.
   * Otherwise, the method propagates the aggregate consensual information to
   * the patient's resources. Upon success, `affected_resources` in the
   * ApplyConsentsResponse indicates the number of resources that may have
   * consensual access changed.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplyConsentsRequest::class, 'Google_Service_CloudHealthcare_ApplyConsentsRequest');
