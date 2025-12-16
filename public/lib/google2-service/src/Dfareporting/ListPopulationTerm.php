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

namespace Google\Service\Dfareporting;

class ListPopulationTerm extends \Google\Model
{
  public const OPERATOR_NUM_EQUALS = 'NUM_EQUALS';
  public const OPERATOR_NUM_LESS_THAN = 'NUM_LESS_THAN';
  public const OPERATOR_NUM_LESS_THAN_EQUAL = 'NUM_LESS_THAN_EQUAL';
  public const OPERATOR_NUM_GREATER_THAN = 'NUM_GREATER_THAN';
  public const OPERATOR_NUM_GREATER_THAN_EQUAL = 'NUM_GREATER_THAN_EQUAL';
  public const OPERATOR_STRING_EQUALS = 'STRING_EQUALS';
  public const OPERATOR_STRING_CONTAINS = 'STRING_CONTAINS';
  public const TYPE_CUSTOM_VARIABLE_TERM = 'CUSTOM_VARIABLE_TERM';
  public const TYPE_LIST_MEMBERSHIP_TERM = 'LIST_MEMBERSHIP_TERM';
  public const TYPE_REFERRER_TERM = 'REFERRER_TERM';
  /**
   * Will be true if the term should check if the user is in the list and false
   * if the term should check if the user is not in the list. This field is only
   * relevant when type is set to LIST_MEMBERSHIP_TERM. False by default.
   *
   * @var bool
   */
  public $contains;
  /**
   * Whether to negate the comparison result of this term during rule
   * evaluation. This field is only relevant when type is left unset or set to
   * CUSTOM_VARIABLE_TERM or REFERRER_TERM.
   *
   * @var bool
   */
  public $negation;
  /**
   * Comparison operator of this term. This field is only relevant when type is
   * left unset or set to CUSTOM_VARIABLE_TERM or REFERRER_TERM.
   *
   * @var string
   */
  public $operator;
  /**
   * ID of the list in question. This field is only relevant when type is set to
   * LIST_MEMBERSHIP_TERM.
   *
   * @var string
   */
  public $remarketingListId;
  /**
   * List population term type determines the applicable fields in this object.
   * If left unset or set to CUSTOM_VARIABLE_TERM, then variableName,
   * variableFriendlyName, operator, value, and negation are applicable. If set
   * to LIST_MEMBERSHIP_TERM then remarketingListId and contains are applicable.
   * If set to REFERRER_TERM then operator, value, and negation are applicable.
   *
   * @var string
   */
  public $type;
  /**
   * Literal to compare the variable to. This field is only relevant when type
   * is left unset or set to CUSTOM_VARIABLE_TERM or REFERRER_TERM.
   *
   * @var string
   */
  public $value;
  /**
   * Friendly name of this term's variable. This is a read-only, auto-generated
   * field. This field is only relevant when type is left unset or set to
   * CUSTOM_VARIABLE_TERM.
   *
   * @var string
   */
  public $variableFriendlyName;
  /**
   * Name of the variable (U1, U2, etc.) being compared in this term. This field
   * is only relevant when type is set to null, CUSTOM_VARIABLE_TERM or
   * REFERRER_TERM.
   *
   * @var string
   */
  public $variableName;

  /**
   * Will be true if the term should check if the user is in the list and false
   * if the term should check if the user is not in the list. This field is only
   * relevant when type is set to LIST_MEMBERSHIP_TERM. False by default.
   *
   * @param bool $contains
   */
  public function setContains($contains)
  {
    $this->contains = $contains;
  }
  /**
   * @return bool
   */
  public function getContains()
  {
    return $this->contains;
  }
  /**
   * Whether to negate the comparison result of this term during rule
   * evaluation. This field is only relevant when type is left unset or set to
   * CUSTOM_VARIABLE_TERM or REFERRER_TERM.
   *
   * @param bool $negation
   */
  public function setNegation($negation)
  {
    $this->negation = $negation;
  }
  /**
   * @return bool
   */
  public function getNegation()
  {
    return $this->negation;
  }
  /**
   * Comparison operator of this term. This field is only relevant when type is
   * left unset or set to CUSTOM_VARIABLE_TERM or REFERRER_TERM.
   *
   * Accepted values: NUM_EQUALS, NUM_LESS_THAN, NUM_LESS_THAN_EQUAL,
   * NUM_GREATER_THAN, NUM_GREATER_THAN_EQUAL, STRING_EQUALS, STRING_CONTAINS
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
  /**
   * ID of the list in question. This field is only relevant when type is set to
   * LIST_MEMBERSHIP_TERM.
   *
   * @param string $remarketingListId
   */
  public function setRemarketingListId($remarketingListId)
  {
    $this->remarketingListId = $remarketingListId;
  }
  /**
   * @return string
   */
  public function getRemarketingListId()
  {
    return $this->remarketingListId;
  }
  /**
   * List population term type determines the applicable fields in this object.
   * If left unset or set to CUSTOM_VARIABLE_TERM, then variableName,
   * variableFriendlyName, operator, value, and negation are applicable. If set
   * to LIST_MEMBERSHIP_TERM then remarketingListId and contains are applicable.
   * If set to REFERRER_TERM then operator, value, and negation are applicable.
   *
   * Accepted values: CUSTOM_VARIABLE_TERM, LIST_MEMBERSHIP_TERM, REFERRER_TERM
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Literal to compare the variable to. This field is only relevant when type
   * is left unset or set to CUSTOM_VARIABLE_TERM or REFERRER_TERM.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Friendly name of this term's variable. This is a read-only, auto-generated
   * field. This field is only relevant when type is left unset or set to
   * CUSTOM_VARIABLE_TERM.
   *
   * @param string $variableFriendlyName
   */
  public function setVariableFriendlyName($variableFriendlyName)
  {
    $this->variableFriendlyName = $variableFriendlyName;
  }
  /**
   * @return string
   */
  public function getVariableFriendlyName()
  {
    return $this->variableFriendlyName;
  }
  /**
   * Name of the variable (U1, U2, etc.) being compared in this term. This field
   * is only relevant when type is set to null, CUSTOM_VARIABLE_TERM or
   * REFERRER_TERM.
   *
   * @param string $variableName
   */
  public function setVariableName($variableName)
  {
    $this->variableName = $variableName;
  }
  /**
   * @return string
   */
  public function getVariableName()
  {
    return $this->variableName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPopulationTerm::class, 'Google_Service_Dfareporting_ListPopulationTerm');
