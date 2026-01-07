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

namespace Google\Service\SearchConsole;

class ApiDimensionFilter extends \Google\Model
{
  public const DIMENSION_QUERY = 'QUERY';
  public const DIMENSION_PAGE = 'PAGE';
  public const DIMENSION_COUNTRY = 'COUNTRY';
  public const DIMENSION_DEVICE = 'DEVICE';
  public const DIMENSION_SEARCH_APPEARANCE = 'SEARCH_APPEARANCE';
  public const OPERATOR_EQUALS = 'EQUALS';
  public const OPERATOR_NOT_EQUALS = 'NOT_EQUALS';
  public const OPERATOR_CONTAINS = 'CONTAINS';
  public const OPERATOR_NOT_CONTAINS = 'NOT_CONTAINS';
  public const OPERATOR_INCLUDING_REGEX = 'INCLUDING_REGEX';
  public const OPERATOR_EXCLUDING_REGEX = 'EXCLUDING_REGEX';
  /**
   * @var string
   */
  public $dimension;
  /**
   * @var string
   */
  public $expression;
  /**
   * @var string
   */
  public $operator;

  /**
   * @param self::DIMENSION_* $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return self::DIMENSION_*
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * @param string $expression
   */
  public function setExpression($expression)
  {
    $this->expression = $expression;
  }
  /**
   * @return string
   */
  public function getExpression()
  {
    return $this->expression;
  }
  /**
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
class_alias(ApiDimensionFilter::class, 'Google_Service_SearchConsole_ApiDimensionFilter');
