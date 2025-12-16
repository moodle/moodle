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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreviewResourceCounts extends \Google\Model
{
  /**
   * Output only. Number of scanned resources with zero violations.
   *
   * @var int
   */
  public $compliant;
  /**
   * Output only. Number of resources that returned an error when scanned.
   *
   * @var int
   */
  public $errors;
  /**
   * Output only. Number of scanned resources with at least one violation.
   *
   * @var int
   */
  public $noncompliant;
  /**
   * Output only. Number of resources checked for compliance. Must equal:
   * unenforced + noncompliant + compliant + error
   *
   * @var int
   */
  public $scanned;
  /**
   * Output only. Number of resources where the constraint was not enforced,
   * i.e. the Policy set `enforced: false` for that resource.
   *
   * @var int
   */
  public $unenforced;

  /**
   * Output only. Number of scanned resources with zero violations.
   *
   * @param int $compliant
   */
  public function setCompliant($compliant)
  {
    $this->compliant = $compliant;
  }
  /**
   * @return int
   */
  public function getCompliant()
  {
    return $this->compliant;
  }
  /**
   * Output only. Number of resources that returned an error when scanned.
   *
   * @param int $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return int
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Number of scanned resources with at least one violation.
   *
   * @param int $noncompliant
   */
  public function setNoncompliant($noncompliant)
  {
    $this->noncompliant = $noncompliant;
  }
  /**
   * @return int
   */
  public function getNoncompliant()
  {
    return $this->noncompliant;
  }
  /**
   * Output only. Number of resources checked for compliance. Must equal:
   * unenforced + noncompliant + compliant + error
   *
   * @param int $scanned
   */
  public function setScanned($scanned)
  {
    $this->scanned = $scanned;
  }
  /**
   * @return int
   */
  public function getScanned()
  {
    return $this->scanned;
  }
  /**
   * Output only. Number of resources where the constraint was not enforced,
   * i.e. the Policy set `enforced: false` for that resource.
   *
   * @param int $unenforced
   */
  public function setUnenforced($unenforced)
  {
    $this->unenforced = $unenforced;
  }
  /**
   * @return int
   */
  public function getUnenforced()
  {
    return $this->unenforced;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreviewResourceCounts::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1OrgPolicyViolationsPreviewResourceCounts');
