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

namespace Google\Service\CloudSearch;

class QueryOperator extends \Google\Collection
{
  /**
   * Invalid value.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  public const TYPE_INTEGER = 'INTEGER';
  public const TYPE_DOUBLE = 'DOUBLE';
  public const TYPE_TIMESTAMP = 'TIMESTAMP';
  public const TYPE_BOOLEAN = 'BOOLEAN';
  public const TYPE_ENUM = 'ENUM';
  public const TYPE_DATE = 'DATE';
  public const TYPE_TEXT = 'TEXT';
  public const TYPE_HTML = 'HTML';
  protected $collection_key = 'enumValues';
  /**
   * Display name of the operator
   *
   * @var string
   */
  public $displayName;
  /**
   * Potential list of values for the opeatror field. This field is only filled
   * when we can safely enumerate all the possible values of this operator.
   *
   * @var string[]
   */
  public $enumValues;
  /**
   * Indicates the operator name that can be used to isolate the property using
   * the greater-than operator.
   *
   * @var string
   */
  public $greaterThanOperatorName;
  /**
   * Can this operator be used to get facets.
   *
   * @var bool
   */
  public $isFacetable;
  /**
   * Indicates if multiple values can be set for this property.
   *
   * @var bool
   */
  public $isRepeatable;
  /**
   * Will the property associated with this facet be returned as part of search
   * results.
   *
   * @var bool
   */
  public $isReturnable;
  /**
   * Can this operator be used to sort results.
   *
   * @var bool
   */
  public $isSortable;
  /**
   * Can get suggestions for this field.
   *
   * @var bool
   */
  public $isSuggestable;
  /**
   * Indicates the operator name that can be used to isolate the property using
   * the less-than operator.
   *
   * @var string
   */
  public $lessThanOperatorName;
  /**
   * The name of the object corresponding to the operator. This field is only
   * filled for schema-specific operators, and is unset for common operators.
   *
   * @var string
   */
  public $objectType;
  /**
   * The name of the operator.
   *
   * @var string
   */
  public $operatorName;
  /**
   * The type of the operator.
   *
   * @var string
   */
  public $type;

  /**
   * Display name of the operator
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Potential list of values for the opeatror field. This field is only filled
   * when we can safely enumerate all the possible values of this operator.
   *
   * @param string[] $enumValues
   */
  public function setEnumValues($enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return string[]
   */
  public function getEnumValues()
  {
    return $this->enumValues;
  }
  /**
   * Indicates the operator name that can be used to isolate the property using
   * the greater-than operator.
   *
   * @param string $greaterThanOperatorName
   */
  public function setGreaterThanOperatorName($greaterThanOperatorName)
  {
    $this->greaterThanOperatorName = $greaterThanOperatorName;
  }
  /**
   * @return string
   */
  public function getGreaterThanOperatorName()
  {
    return $this->greaterThanOperatorName;
  }
  /**
   * Can this operator be used to get facets.
   *
   * @param bool $isFacetable
   */
  public function setIsFacetable($isFacetable)
  {
    $this->isFacetable = $isFacetable;
  }
  /**
   * @return bool
   */
  public function getIsFacetable()
  {
    return $this->isFacetable;
  }
  /**
   * Indicates if multiple values can be set for this property.
   *
   * @param bool $isRepeatable
   */
  public function setIsRepeatable($isRepeatable)
  {
    $this->isRepeatable = $isRepeatable;
  }
  /**
   * @return bool
   */
  public function getIsRepeatable()
  {
    return $this->isRepeatable;
  }
  /**
   * Will the property associated with this facet be returned as part of search
   * results.
   *
   * @param bool $isReturnable
   */
  public function setIsReturnable($isReturnable)
  {
    $this->isReturnable = $isReturnable;
  }
  /**
   * @return bool
   */
  public function getIsReturnable()
  {
    return $this->isReturnable;
  }
  /**
   * Can this operator be used to sort results.
   *
   * @param bool $isSortable
   */
  public function setIsSortable($isSortable)
  {
    $this->isSortable = $isSortable;
  }
  /**
   * @return bool
   */
  public function getIsSortable()
  {
    return $this->isSortable;
  }
  /**
   * Can get suggestions for this field.
   *
   * @param bool $isSuggestable
   */
  public function setIsSuggestable($isSuggestable)
  {
    $this->isSuggestable = $isSuggestable;
  }
  /**
   * @return bool
   */
  public function getIsSuggestable()
  {
    return $this->isSuggestable;
  }
  /**
   * Indicates the operator name that can be used to isolate the property using
   * the less-than operator.
   *
   * @param string $lessThanOperatorName
   */
  public function setLessThanOperatorName($lessThanOperatorName)
  {
    $this->lessThanOperatorName = $lessThanOperatorName;
  }
  /**
   * @return string
   */
  public function getLessThanOperatorName()
  {
    return $this->lessThanOperatorName;
  }
  /**
   * The name of the object corresponding to the operator. This field is only
   * filled for schema-specific operators, and is unset for common operators.
   *
   * @param string $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return string
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * The name of the operator.
   *
   * @param string $operatorName
   */
  public function setOperatorName($operatorName)
  {
    $this->operatorName = $operatorName;
  }
  /**
   * @return string
   */
  public function getOperatorName()
  {
    return $this->operatorName;
  }
  /**
   * The type of the operator.
   *
   * Accepted values: UNKNOWN, INTEGER, DOUBLE, TIMESTAMP, BOOLEAN, ENUM, DATE,
   * TEXT, HTML
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
class_alias(QueryOperator::class, 'Google_Service_CloudSearch_QueryOperator');
