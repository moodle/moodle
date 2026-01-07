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

class DimensionValue extends \Google\Model
{
  public const MATCH_TYPE_EXACT = 'EXACT';
  public const MATCH_TYPE_BEGINS_WITH = 'BEGINS_WITH';
  public const MATCH_TYPE_CONTAINS = 'CONTAINS';
  public const MATCH_TYPE_WILDCARD_EXPRESSION = 'WILDCARD_EXPRESSION';
  /**
   * The name of the dimension.
   *
   * @var string
   */
  public $dimensionName;
  /**
   * The eTag of this response for caching purposes.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID associated with the value if available.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of resource this is, in this case dfareporting#dimensionValue.
   *
   * @var string
   */
  public $kind;
  /**
   * Determines how the 'value' field is matched when filtering. If not
   * specified, defaults to EXACT. If set to WILDCARD_EXPRESSION, '*' is allowed
   * as a placeholder for variable length character sequences, and it can be
   * escaped with a backslash. Note, only paid search dimensions
   * ('dfa:paidSearch*') allow a matchType other than EXACT.
   *
   * @var string
   */
  public $matchType;
  /**
   * The value of the dimension.
   *
   * @var string
   */
  public $value;

  /**
   * The name of the dimension.
   *
   * @param string $dimensionName
   */
  public function setDimensionName($dimensionName)
  {
    $this->dimensionName = $dimensionName;
  }
  /**
   * @return string
   */
  public function getDimensionName()
  {
    return $this->dimensionName;
  }
  /**
   * The eTag of this response for caching purposes.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The ID associated with the value if available.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The kind of resource this is, in this case dfareporting#dimensionValue.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Determines how the 'value' field is matched when filtering. If not
   * specified, defaults to EXACT. If set to WILDCARD_EXPRESSION, '*' is allowed
   * as a placeholder for variable length character sequences, and it can be
   * escaped with a backslash. Note, only paid search dimensions
   * ('dfa:paidSearch*') allow a matchType other than EXACT.
   *
   * Accepted values: EXACT, BEGINS_WITH, CONTAINS, WILDCARD_EXPRESSION
   *
   * @param self::MATCH_TYPE_* $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return self::MATCH_TYPE_*
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * The value of the dimension.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionValue::class, 'Google_Service_Dfareporting_DimensionValue');
