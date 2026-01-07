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

class GoogleAdsSearchads360V0ResourcesCustomColumn extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const RENDER_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Unknown.
   */
  public const RENDER_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The custom column is a raw numerical value. See value_type field to
   * determine if it is an integer or a double.
   */
  public const RENDER_TYPE_NUMBER = 'NUMBER';
  /**
   * The custom column should be multiplied by 100 to retrieve the percentage
   * value.
   */
  public const RENDER_TYPE_PERCENT = 'PERCENT';
  /**
   * The custom column value is a monetary value and is in micros.
   */
  public const RENDER_TYPE_MONEY = 'MONEY';
  /**
   * The custom column value is a string.
   */
  public const RENDER_TYPE_STRING = 'STRING';
  /**
   * The custom column value is a boolean.
   */
  public const RENDER_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * The custom column value is a date represented as an integer in YYYYMMDD
   * format.
   */
  public const RENDER_TYPE_DATE = 'DATE';
  /**
   * Not specified.
   */
  public const VALUE_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Unknown.
   */
  public const VALUE_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The custom column value is a string.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * The custom column value is an int64 number.
   */
  public const VALUE_TYPE_INT64 = 'INT64';
  /**
   * The custom column value is a double number.
   */
  public const VALUE_TYPE_DOUBLE = 'DOUBLE';
  /**
   * The custom column value is a boolean.
   */
  public const VALUE_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * The custom column value is a date, in YYYYMMDD format.
   */
  public const VALUE_TYPE_DATE = 'DATE';
  protected $collection_key = 'referencedSystemColumns';
  /**
   * Output only. User-defined description of the custom column.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. ID of the custom column.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. User-defined name of the custom column.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. True when the custom column is available to be used in the
   * query of SearchAds360Service.Search and SearchAds360Service.SearchStream.
   *
   * @var bool
   */
  public $queryable;
  /**
   * Output only. The list of the referenced system columns of this custom
   * column. For example, A custom column "sum of impressions and clicks" has
   * referenced system columns of {"metrics.clicks", "metrics.impressions"}.
   *
   * @var string[]
   */
  public $referencedSystemColumns;
  /**
   * Output only. True when the custom column is referring to one or more
   * attributes.
   *
   * @var bool
   */
  public $referencesAttributes;
  /**
   * Output only. True when the custom column is referring to one or more
   * metrics.
   *
   * @var bool
   */
  public $referencesMetrics;
  /**
   * Output only. How the result value of the custom column should be
   * interpreted.
   *
   * @var string
   */
  public $renderType;
  /**
   * Immutable. The resource name of the custom column. Custom column resource
   * names have the form:
   * `customers/{customer_id}/customColumns/{custom_column_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The type of the result value of the custom column.
   *
   * @var string
   */
  public $valueType;

  /**
   * Output only. User-defined description of the custom column.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. ID of the custom column.
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
   * Output only. User-defined name of the custom column.
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
  /**
   * Output only. True when the custom column is available to be used in the
   * query of SearchAds360Service.Search and SearchAds360Service.SearchStream.
   *
   * @param bool $queryable
   */
  public function setQueryable($queryable)
  {
    $this->queryable = $queryable;
  }
  /**
   * @return bool
   */
  public function getQueryable()
  {
    return $this->queryable;
  }
  /**
   * Output only. The list of the referenced system columns of this custom
   * column. For example, A custom column "sum of impressions and clicks" has
   * referenced system columns of {"metrics.clicks", "metrics.impressions"}.
   *
   * @param string[] $referencedSystemColumns
   */
  public function setReferencedSystemColumns($referencedSystemColumns)
  {
    $this->referencedSystemColumns = $referencedSystemColumns;
  }
  /**
   * @return string[]
   */
  public function getReferencedSystemColumns()
  {
    return $this->referencedSystemColumns;
  }
  /**
   * Output only. True when the custom column is referring to one or more
   * attributes.
   *
   * @param bool $referencesAttributes
   */
  public function setReferencesAttributes($referencesAttributes)
  {
    $this->referencesAttributes = $referencesAttributes;
  }
  /**
   * @return bool
   */
  public function getReferencesAttributes()
  {
    return $this->referencesAttributes;
  }
  /**
   * Output only. True when the custom column is referring to one or more
   * metrics.
   *
   * @param bool $referencesMetrics
   */
  public function setReferencesMetrics($referencesMetrics)
  {
    $this->referencesMetrics = $referencesMetrics;
  }
  /**
   * @return bool
   */
  public function getReferencesMetrics()
  {
    return $this->referencesMetrics;
  }
  /**
   * Output only. How the result value of the custom column should be
   * interpreted.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, NUMBER, PERCENT, MONEY, STRING,
   * BOOLEAN, DATE
   *
   * @param self::RENDER_TYPE_* $renderType
   */
  public function setRenderType($renderType)
  {
    $this->renderType = $renderType;
  }
  /**
   * @return self::RENDER_TYPE_*
   */
  public function getRenderType()
  {
    return $this->renderType;
  }
  /**
   * Immutable. The resource name of the custom column. Custom column resource
   * names have the form:
   * `customers/{customer_id}/customColumns/{custom_column_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. The type of the result value of the custom column.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, STRING, INT64, DOUBLE, BOOLEAN, DATE
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCustomColumn::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCustomColumn');
