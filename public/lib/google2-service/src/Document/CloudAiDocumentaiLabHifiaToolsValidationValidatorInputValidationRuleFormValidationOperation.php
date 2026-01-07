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

class CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation extends \Google\Collection
{
  public const OPERATION_TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  public const OPERATION_TYPE_OPERATION_TYPE_SUM = 'OPERATION_TYPE_SUM';
  public const OPERATION_TYPE_OPERATION_TYPE_SUB = 'OPERATION_TYPE_SUB';
  public const OPERATION_TYPE_OPERATION_TYPE_MUL = 'OPERATION_TYPE_MUL';
  public const OPERATION_TYPE_OPERATION_TYPE_DIV = 'OPERATION_TYPE_DIV';
  public const OPERATION_TYPE_OPERATION_TYPE_MAX = 'OPERATION_TYPE_MAX';
  public const OPERATION_TYPE_OPERATION_TYPE_MIN = 'OPERATION_TYPE_MIN';
  public const OPERATION_TYPE_OPERATION_TYPE_ABS = 'OPERATION_TYPE_ABS';
  public const OPERATION_TYPE_OPERATION_TYPE_UNIQUE = 'OPERATION_TYPE_UNIQUE';
  public const OPERATION_TYPE_OPERATION_TYPE_COUNT = 'OPERATION_TYPE_COUNT';
  protected $collection_key = 'operations';
  protected $constantsType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleConstant::class;
  protected $constantsDataType = 'array';
  protected $fieldsType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField::class;
  protected $fieldsDataType = 'array';
  /**
   * The operation type to be applied to all the operands.
   *
   * @var string
   */
  public $operationType;
  protected $operationsType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation::class;
  protected $operationsDataType = 'array';

  /**
   * A list of constants to be used as operands.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleConstant[] $constants
   */
  public function setConstants($constants)
  {
    $this->constants = $constants;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleConstant[]
   */
  public function getConstants()
  {
    return $this->constants;
  }
  /**
   * A list of fields to be used as operands.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The operation type to be applied to all the operands.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, OPERATION_TYPE_SUM,
   * OPERATION_TYPE_SUB, OPERATION_TYPE_MUL, OPERATION_TYPE_DIV,
   * OPERATION_TYPE_MAX, OPERATION_TYPE_MIN, OPERATION_TYPE_ABS,
   * OPERATION_TYPE_UNIQUE, OPERATION_TYPE_COUNT
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * A list of recursive operations to be used as operands.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation[] $operations
   */
  public function setOperations($operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation[]
   */
  public function getOperations()
  {
    return $this->operations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation::class, 'Google_Service_Document_CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidationOperation');
