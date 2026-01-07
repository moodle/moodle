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

namespace Google\Service\Document;

class CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule extends \Google\Model
{
  public const ALIGNMENT_TYPE_ALIGNMENT_TYPE_UNSPECIFIED = 'ALIGNMENT_TYPE_UNSPECIFIED';
  public const ALIGNMENT_TYPE_ALIGNMENT_TYPE_HORIZONTAL = 'ALIGNMENT_TYPE_HORIZONTAL';
  public const ALIGNMENT_TYPE_ALIGNMENT_TYPE_VERTICAL = 'ALIGNMENT_TYPE_VERTICAL';
  /**
   * @var string
   */
  public $alignmentType;
  /**
   * The tolerance to use when comparing coordinates.
   *
   * @var float
   */
  public $tolerance;

  /**
   * @param self::ALIGNMENT_TYPE_* $alignmentType
   */
  public function setAlignmentType($alignmentType)
  {
    $this->alignmentType = $alignmentType;
  }
  /**
   * @return self::ALIGNMENT_TYPE_*
   */
  public function getAlignmentType()
  {
    return $this->alignmentType;
  }
  /**
   * The tolerance to use when comparing coordinates.
   *
   * @param float $tolerance
   */
  public function setTolerance($tolerance)
  {
    $this->tolerance = $tolerance;
  }
  /**
   * @return float
   */
  public function getTolerance()
  {
    return $this->tolerance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule::class, 'Google_Service_Document_CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule');
