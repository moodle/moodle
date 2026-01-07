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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonWebpageConditionInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const OPERAND_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const OPERAND_UNKNOWN = 'UNKNOWN';
  /**
   * Operand denoting a webpage URL targeting condition.
   */
  public const OPERAND_URL = 'URL';
  /**
   * Operand denoting a webpage category targeting condition.
   */
  public const OPERAND_CATEGORY = 'CATEGORY';
  /**
   * Operand denoting a webpage title targeting condition.
   */
  public const OPERAND_PAGE_TITLE = 'PAGE_TITLE';
  /**
   * Operand denoting a webpage content targeting condition.
   */
  public const OPERAND_PAGE_CONTENT = 'PAGE_CONTENT';
  /**
   * Operand denoting a webpage custom label targeting condition.
   */
  public const OPERAND_CUSTOM_LABEL = 'CUSTOM_LABEL';
  /**
   * Not specified.
   */
  public const OPERATOR_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const OPERATOR_UNKNOWN = 'UNKNOWN';
  /**
   * The argument web condition is equal to the compared web condition.
   */
  public const OPERATOR_EQUALS = 'EQUALS';
  /**
   * The argument web condition is part of the compared web condition.
   */
  public const OPERATOR_CONTAINS = 'CONTAINS';
  /**
   * Argument of webpage targeting condition.
   *
   * @var string
   */
  public $argument;
  /**
   * Operand of webpage targeting condition.
   *
   * @var string
   */
  public $operand;
  /**
   * Operator of webpage targeting condition.
   *
   * @var string
   */
  public $operator;

  /**
   * Argument of webpage targeting condition.
   *
   * @param string $argument
   */
  public function setArgument($argument)
  {
    $this->argument = $argument;
  }
  /**
   * @return string
   */
  public function getArgument()
  {
    return $this->argument;
  }
  /**
   * Operand of webpage targeting condition.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, URL, CATEGORY, PAGE_TITLE,
   * PAGE_CONTENT, CUSTOM_LABEL
   *
   * @param self::OPERAND_* $operand
   */
  public function setOperand($operand)
  {
    $this->operand = $operand;
  }
  /**
   * @return self::OPERAND_*
   */
  public function getOperand()
  {
    return $this->operand;
  }
  /**
   * Operator of webpage targeting condition.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, EQUALS, CONTAINS
   *
   * @param self::OPERATOR_* $operator
   */
  public function setOperator($operator)
  {
    $this->operator = $operator;
  }
  /**
   * @return self::OPERATOR_*
   */
  public function getOperator()
  {
    return $this->operator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonWebpageConditionInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonWebpageConditionInfo');
