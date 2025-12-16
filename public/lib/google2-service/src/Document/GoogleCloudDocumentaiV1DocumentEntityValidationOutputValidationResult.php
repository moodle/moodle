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

class GoogleCloudDocumentaiV1DocumentEntityValidationOutputValidationResult extends \Google\Model
{
  /**
   * The validation result type is unspecified.
   */
  public const VALIDATION_RESULT_TYPE_VALIDATION_RESULT_TYPE_UNSPECIFIED = 'VALIDATION_RESULT_TYPE_UNSPECIFIED';
  /**
   * The validation is valid.
   */
  public const VALIDATION_RESULT_TYPE_VALIDATION_RESULT_TYPE_VALID = 'VALIDATION_RESULT_TYPE_VALID';
  /**
   * The validation is invalid.
   */
  public const VALIDATION_RESULT_TYPE_VALIDATION_RESULT_TYPE_INVALID = 'VALIDATION_RESULT_TYPE_INVALID';
  /**
   * The validation is skipped.
   */
  public const VALIDATION_RESULT_TYPE_VALIDATION_RESULT_TYPE_SKIPPED = 'VALIDATION_RESULT_TYPE_SKIPPED';
  /**
   * The validation is not applicable.
   */
  public const VALIDATION_RESULT_TYPE_VALIDATION_RESULT_TYPE_NOT_APPLICABLE = 'VALIDATION_RESULT_TYPE_NOT_APPLICABLE';
  /**
   * Optional. The name of the rule resource that is used for validation.
   * Format: `projects/{project}/locations/{location}/rules/{rule}`
   *
   * @var string
   */
  public $rule;
  /**
   * The description of the validation rule.
   *
   * @var string
   */
  public $ruleDescription;
  /**
   * The display name of the validation rule.
   *
   * @var string
   */
  public $ruleName;
  /**
   * The detailed information of the running the validation process using the
   * entity from the document based on the validation rule.
   *
   * @var string
   */
  public $validationDetails;
  /**
   * The result of the validation rule.
   *
   * @var string
   */
  public $validationResultType;

  /**
   * Optional. The name of the rule resource that is used for validation.
   * Format: `projects/{project}/locations/{location}/rules/{rule}`
   *
   * @param string $rule
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return string
   */
  public function getRule()
  {
    return $this->rule;
  }
  /**
   * The description of the validation rule.
   *
   * @param string $ruleDescription
   */
  public function setRuleDescription($ruleDescription)
  {
    $this->ruleDescription = $ruleDescription;
  }
  /**
   * @return string
   */
  public function getRuleDescription()
  {
    return $this->ruleDescription;
  }
  /**
   * The display name of the validation rule.
   *
   * @param string $ruleName
   */
  public function setRuleName($ruleName)
  {
    $this->ruleName = $ruleName;
  }
  /**
   * @return string
   */
  public function getRuleName()
  {
    return $this->ruleName;
  }
  /**
   * The detailed information of the running the validation process using the
   * entity from the document based on the validation rule.
   *
   * @param string $validationDetails
   */
  public function setValidationDetails($validationDetails)
  {
    $this->validationDetails = $validationDetails;
  }
  /**
   * @return string
   */
  public function getValidationDetails()
  {
    return $this->validationDetails;
  }
  /**
   * The result of the validation rule.
   *
   * Accepted values: VALIDATION_RESULT_TYPE_UNSPECIFIED,
   * VALIDATION_RESULT_TYPE_VALID, VALIDATION_RESULT_TYPE_INVALID,
   * VALIDATION_RESULT_TYPE_SKIPPED, VALIDATION_RESULT_TYPE_NOT_APPLICABLE
   *
   * @param self::VALIDATION_RESULT_TYPE_* $validationResultType
   */
  public function setValidationResultType($validationResultType)
  {
    $this->validationResultType = $validationResultType;
  }
  /**
   * @return self::VALIDATION_RESULT_TYPE_*
   */
  public function getValidationResultType()
  {
    return $this->validationResultType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentEntityValidationOutputValidationResult::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentEntityValidationOutputValidationResult');
