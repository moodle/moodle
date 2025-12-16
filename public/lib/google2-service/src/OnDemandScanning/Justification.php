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

namespace Google\Service\OnDemandScanning;

class Justification extends \Google\Model
{
  /**
   * JUSTIFICATION_TYPE_UNSPECIFIED.
   */
  public const JUSTIFICATION_TYPE_JUSTIFICATION_TYPE_UNSPECIFIED = 'JUSTIFICATION_TYPE_UNSPECIFIED';
  /**
   * The vulnerable component is not present in the product.
   */
  public const JUSTIFICATION_TYPE_COMPONENT_NOT_PRESENT = 'COMPONENT_NOT_PRESENT';
  /**
   * The vulnerable code is not present. Typically this case occurs when source
   * code is configured or built in a way that excludes the vulnerable code.
   */
  public const JUSTIFICATION_TYPE_VULNERABLE_CODE_NOT_PRESENT = 'VULNERABLE_CODE_NOT_PRESENT';
  /**
   * The vulnerable code can not be executed. Typically this case occurs when
   * the product includes the vulnerable code but does not call or use the
   * vulnerable code.
   */
  public const JUSTIFICATION_TYPE_VULNERABLE_CODE_NOT_IN_EXECUTE_PATH = 'VULNERABLE_CODE_NOT_IN_EXECUTE_PATH';
  /**
   * The vulnerable code cannot be controlled by an attacker to exploit the
   * vulnerability.
   */
  public const JUSTIFICATION_TYPE_VULNERABLE_CODE_CANNOT_BE_CONTROLLED_BY_ADVERSARY = 'VULNERABLE_CODE_CANNOT_BE_CONTROLLED_BY_ADVERSARY';
  /**
   * The product includes built-in protections or features that prevent
   * exploitation of the vulnerability. These built-in protections cannot be
   * subverted by the attacker and cannot be configured or disabled by the user.
   * These mitigations completely prevent exploitation based on known attack
   * vectors.
   */
  public const JUSTIFICATION_TYPE_INLINE_MITIGATIONS_ALREADY_EXIST = 'INLINE_MITIGATIONS_ALREADY_EXIST';
  /**
   * Additional details on why this justification was chosen.
   *
   * @var string
   */
  public $details;
  /**
   * The justification type for this vulnerability.
   *
   * @var string
   */
  public $justificationType;

  /**
   * Additional details on why this justification was chosen.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The justification type for this vulnerability.
   *
   * Accepted values: JUSTIFICATION_TYPE_UNSPECIFIED, COMPONENT_NOT_PRESENT,
   * VULNERABLE_CODE_NOT_PRESENT, VULNERABLE_CODE_NOT_IN_EXECUTE_PATH,
   * VULNERABLE_CODE_CANNOT_BE_CONTROLLED_BY_ADVERSARY,
   * INLINE_MITIGATIONS_ALREADY_EXIST
   *
   * @param self::JUSTIFICATION_TYPE_* $justificationType
   */
  public function setJustificationType($justificationType)
  {
    $this->justificationType = $justificationType;
  }
  /**
   * @return self::JUSTIFICATION_TYPE_*
   */
  public function getJustificationType()
  {
    return $this->justificationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Justification::class, 'Google_Service_OnDemandScanning_Justification');
