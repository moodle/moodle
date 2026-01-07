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

namespace Google\Service\Bigquery;

class Argument extends \Google\Model
{
  /**
   * Default value.
   */
  public const ARGUMENT_KIND_ARGUMENT_KIND_UNSPECIFIED = 'ARGUMENT_KIND_UNSPECIFIED';
  /**
   * The argument is a variable with fully specified type, which can be a struct
   * or an array, but not a table.
   */
  public const ARGUMENT_KIND_FIXED_TYPE = 'FIXED_TYPE';
  /**
   * The argument is any type, including struct or array, but not a table.
   */
  public const ARGUMENT_KIND_ANY_TYPE = 'ANY_TYPE';
  /**
   * Default value.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * The argument is input-only.
   */
  public const MODE_IN = 'IN';
  /**
   * The argument is output-only.
   */
  public const MODE_OUT = 'OUT';
  /**
   * The argument is both an input and an output.
   */
  public const MODE_INOUT = 'INOUT';
  /**
   * Optional. Defaults to FIXED_TYPE.
   *
   * @var string
   */
  public $argumentKind;
  protected $dataTypeType = StandardSqlDataType::class;
  protected $dataTypeDataType = '';
  /**
   * Optional. Whether the argument is an aggregate function parameter. Must be
   * Unset for routine types other than AGGREGATE_FUNCTION. For
   * AGGREGATE_FUNCTION, if set to false, it is equivalent to adding "NOT
   * AGGREGATE" clause in DDL; Otherwise, it is equivalent to omitting "NOT
   * AGGREGATE" clause in DDL.
   *
   * @var bool
   */
  public $isAggregate;
  /**
   * Optional. Specifies whether the argument is input or output. Can be set for
   * procedures only.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. The name of this argument. Can be absent for function return
   * argument.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Defaults to FIXED_TYPE.
   *
   * Accepted values: ARGUMENT_KIND_UNSPECIFIED, FIXED_TYPE, ANY_TYPE
   *
   * @param self::ARGUMENT_KIND_* $argumentKind
   */
  public function setArgumentKind($argumentKind)
  {
    $this->argumentKind = $argumentKind;
  }
  /**
   * @return self::ARGUMENT_KIND_*
   */
  public function getArgumentKind()
  {
    return $this->argumentKind;
  }
  /**
   * Set if argument_kind == FIXED_TYPE.
   *
   * @param StandardSqlDataType $dataType
   */
  public function setDataType(StandardSqlDataType $dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return StandardSqlDataType
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Optional. Whether the argument is an aggregate function parameter. Must be
   * Unset for routine types other than AGGREGATE_FUNCTION. For
   * AGGREGATE_FUNCTION, if set to false, it is equivalent to adding "NOT
   * AGGREGATE" clause in DDL; Otherwise, it is equivalent to omitting "NOT
   * AGGREGATE" clause in DDL.
   *
   * @param bool $isAggregate
   */
  public function setIsAggregate($isAggregate)
  {
    $this->isAggregate = $isAggregate;
  }
  /**
   * @return bool
   */
  public function getIsAggregate()
  {
    return $this->isAggregate;
  }
  /**
   * Optional. Specifies whether the argument is input or output. Can be set for
   * procedures only.
   *
   * Accepted values: MODE_UNSPECIFIED, IN, OUT, INOUT
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. The name of this argument. Can be absent for function return
   * argument.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Argument::class, 'Google_Service_Bigquery_Argument');
