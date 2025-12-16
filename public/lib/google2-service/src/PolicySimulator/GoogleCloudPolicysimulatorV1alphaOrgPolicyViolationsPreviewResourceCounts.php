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

class GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts extends \Google\Model
{
  /**
   * @var int
   */
  public $compliant;
  /**
   * @var int
   */
  public $errors;
  /**
   * @var int
   */
  public $noncompliant;
  /**
   * @var int
   */
  public $scanned;
  /**
   * @var int
   */
  public $unenforced;

  /**
   * @param int
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
   * @param int
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
   * @param int
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
   * @param int
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
   * @param int
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
class_alias(GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1alphaOrgPolicyViolationsPreviewResourceCounts');
