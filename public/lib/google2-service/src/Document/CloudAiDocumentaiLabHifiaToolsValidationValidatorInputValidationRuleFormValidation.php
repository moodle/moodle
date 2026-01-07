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

class CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation extends \Google\Model
{
  public const VALIDATION_OPERATOR_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  public const VALIDATION_OPERATOR_OPERATION_TYPE_EQ = 'OPERATION_TYPE_EQ';
  public const VALIDATION_OPERATOR_OPERATION_TYPE_NE = 'OPERATION_TYPE_NE';
  public const VALIDATION_OPERATOR_OPERATION_TYPE_LT = 'OPERATION_TYPE_LT';
  public const VALIDATION_OPERATOR_OPERATION_TYPE_LE = 'OPERATION_TYPE_LE';
  public const VALIDATION_OPERATOR_OPERATION_TYPE_GT = 'OPERATION_TYPE_GT';
  public const VALIDATION_OPERATOR_OPERATION_TYPE_GE = 'OPERATION_TYPE_GE';
  protected $leftOperandType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation::class;
  protected $leftOperandDataType = '';
  protected $rightOperandType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation::class;
  protected $rightOperandDataType = '';
  /**
   * The relational operator to be applied to the operands.
   *
   * @var string
   */
  public $validationOperator;

  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation $leftOperand
   */
  public function setLeftOperand(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation $leftOperand)
  {
    $this->leftOperand = $leftOperand;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation
   */
  public function getLeftOperand()
  {
    return $this->leftOperand;
  }
  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation $rightOperand
   */
  public function setRightOperand(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation $rightOperand)
  {
    $this->rightOperand = $rightOperand;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation
   */
  public function getRightOperand()
  {
    return $this->rightOperand;
  }
  /**
   * The relational operator to be applied to the operands.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, OPERATION_TYPE_EQ,
   * OPERATION_TYPE_NE, OPERATION_TYPE_LT, OPERATION_TYPE_LE, OPERATION_TYPE_GT,
   * OPERATION_TYPE_GE
   *
   * @param self::VALIDATION_OPERATOR_* $validationOperator
   */
  public function setValidationOperator($validationOperator)
  {
    $this->validationOperator = $validationOperator;
  }
  /**
   * @return self::VALIDATION_OPERATOR_*
   */
  public function getValidationOperator()
  {
    return $this->validationOperator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation::class, 'Google_Service_Document_CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation');
