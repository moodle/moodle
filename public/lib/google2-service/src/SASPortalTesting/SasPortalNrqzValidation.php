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

namespace Google\Service\SASPortalTesting;

class SasPortalNrqzValidation extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Draft state.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * Final state.
   */
  public const STATE_FINAL = 'FINAL';
  /**
   * Validation case ID.
   *
   * @var string
   */
  public $caseId;
  /**
   * CPI who signed the validation.
   *
   * @var string
   */
  public $cpiId;
  /**
   * Device latitude that's associated with the validation.
   *
   * @var 
   */
  public $latitude;
  /**
   * Device longitude that's associated with the validation.
   *
   * @var 
   */
  public $longitude;
  /**
   * State of the NRQZ validation info.
   *
   * @var string
   */
  public $state;

  /**
   * Validation case ID.
   *
   * @param string $caseId
   */
  public function setCaseId($caseId)
  {
    $this->caseId = $caseId;
  }
  /**
   * @return string
   */
  public function getCaseId()
  {
    return $this->caseId;
  }
  /**
   * CPI who signed the validation.
   *
   * @param string $cpiId
   */
  public function setCpiId($cpiId)
  {
    $this->cpiId = $cpiId;
  }
  /**
   * @return string
   */
  public function getCpiId()
  {
    return $this->cpiId;
  }
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }
  public function getLatitude()
  {
    return $this->latitude;
  }
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }
  public function getLongitude()
  {
    return $this->longitude;
  }
  /**
   * State of the NRQZ validation info.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, FINAL
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalNrqzValidation::class, 'Google_Service_SASPortalTesting_SasPortalNrqzValidation');
