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

namespace Google\Service\TagManager;

class Condition extends \Google\Collection
{
  public const TYPE_conditionTypeUnspecified = 'conditionTypeUnspecified';
  public const TYPE_equals = 'equals';
  public const TYPE_contains = 'contains';
  public const TYPE_startsWith = 'startsWith';
  public const TYPE_endsWith = 'endsWith';
  public const TYPE_matchRegex = 'matchRegex';
  public const TYPE_greater = 'greater';
  public const TYPE_greaterOrEquals = 'greaterOrEquals';
  public const TYPE_less = 'less';
  public const TYPE_lessOrEquals = 'lessOrEquals';
  public const TYPE_cssSelector = 'cssSelector';
  public const TYPE_urlMatches = 'urlMatches';
  protected $collection_key = 'parameter';
  protected $parameterType = Parameter::class;
  protected $parameterDataType = 'array';
  /**
   * The type of operator for this condition.
   *
   * @var string
   */
  public $type;

  /**
   * A list of named parameters (key/value), depending on the condition's type.
   * Notes: - For binary operators, include parameters named arg0 and arg1 for
   * specifying the left and right operands, respectively. - At this time, the
   * left operand (arg0) must be a reference to a variable. - For case-
   * insensitive Regex matching, include a boolean parameter named ignore_case
   * that is set to true. If not specified or set to any other value, the
   * matching will be case sensitive. - To negate an operator, include a boolean
   * parameter named negate boolean parameter that is set to true.
   *
   * @param Parameter[] $parameter
   */
  public function setParameter($parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return Parameter[]
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * The type of operator for this condition.
   *
   * Accepted values: conditionTypeUnspecified, equals, contains, startsWith,
   * endsWith, matchRegex, greater, greaterOrEquals, less, lessOrEquals,
   * cssSelector, urlMatches
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Condition::class, 'Google_Service_TagManager_Condition');
