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

class GoogleAdsSearchads360V0ResourcesSearchAds360Field extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const CATEGORY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Unknown
   */
  public const CATEGORY_UNKNOWN = 'UNKNOWN';
  /**
   * The described artifact is a resource.
   */
  public const CATEGORY_RESOURCE = 'RESOURCE';
  /**
   * The described artifact is a field and is an attribute of a resource.
   * Including a resource attribute field in a query may segment the query if
   * the resource to which it is attributed segments the resource found in the
   * FROM clause.
   */
  public const CATEGORY_ATTRIBUTE = 'ATTRIBUTE';
  /**
   * The described artifact is a field and always segments search queries.
   */
  public const CATEGORY_SEGMENT = 'SEGMENT';
  /**
   * The described artifact is a field and is a metric. It never segments search
   * queries.
   */
  public const CATEGORY_METRIC = 'METRIC';
  /**
   * Unspecified
   */
  public const DATA_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Unknown
   */
  public const DATA_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Maps to google.protobuf.BoolValue Applicable operators: =, !=
   */
  public const DATA_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Maps to google.protobuf.StringValue. It can be compared using the set of
   * operators specific to dates however. Applicable operators: =, <, >, <=, >=,
   * BETWEEN, DURING, and IN
   */
  public const DATA_TYPE_DATE = 'DATE';
  /**
   * Maps to google.protobuf.DoubleValue Applicable operators: =, !=, <, >, IN,
   * NOT IN
   */
  public const DATA_TYPE_DOUBLE = 'DOUBLE';
  /**
   * Maps to an enum. It's specific definition can be found at type_url.
   * Applicable operators: =, !=, IN, NOT IN
   */
  public const DATA_TYPE_ENUM = 'ENUM';
  /**
   * Maps to google.protobuf.FloatValue Applicable operators: =, !=, <, >, IN,
   * NOT IN
   */
  public const DATA_TYPE_FLOAT = 'FLOAT';
  /**
   * Maps to google.protobuf.Int32Value Applicable operators: =, !=, <, >, <=,
   * >=, BETWEEN, IN, NOT IN
   */
  public const DATA_TYPE_INT32 = 'INT32';
  /**
   * Maps to google.protobuf.Int64Value Applicable operators: =, !=, <, >, <=,
   * >=, BETWEEN, IN, NOT IN
   */
  public const DATA_TYPE_INT64 = 'INT64';
  /**
   * Maps to a protocol buffer message type. The data type's details can be
   * found in type_url. No operators work with MESSAGE fields.
   */
  public const DATA_TYPE_MESSAGE = 'MESSAGE';
  /**
   * Maps to google.protobuf.StringValue. Represents the resource name (unique
   * id) of a resource or one of its foreign keys. No operators work with
   * RESOURCE_NAME fields.
   */
  public const DATA_TYPE_RESOURCE_NAME = 'RESOURCE_NAME';
  /**
   * Maps to google.protobuf.StringValue. Applicable operators: =, !=, LIKE, NOT
   * LIKE, IN, NOT IN
   */
  public const DATA_TYPE_STRING = 'STRING';
  /**
   * Maps to google.protobuf.UInt64Value Applicable operators: =, !=, <, >, <=,
   * >=, BETWEEN, IN, NOT IN
   */
  public const DATA_TYPE_UINT64 = 'UINT64';
  protected $collection_key = 'selectableWith';
  /**
   * Output only. The names of all resources that are selectable with the
   * described artifact. Fields from these resources do not segment metrics when
   * included in search queries. This field is only set for artifacts whose
   * category is RESOURCE.
   *
   * @var string[]
   */
  public $attributeResources;
  /**
   * Output only. The category of the artifact.
   *
   * @var string
   */
  public $category;
  /**
   * Output only. This field determines the operators that can be used with the
   * artifact in WHERE clauses.
   *
   * @var string
   */
  public $dataType;
  /**
   * Output only. Values the artifact can assume if it is a field of type ENUM.
   * This field is only set for artifacts of category SEGMENT or ATTRIBUTE.
   *
   * @var string[]
   */
  public $enumValues;
  /**
   * Output only. Whether the artifact can be used in a WHERE clause in search
   * queries.
   *
   * @var bool
   */
  public $filterable;
  /**
   * Output only. Whether the field artifact is repeated.
   *
   * @var bool
   */
  public $isRepeated;
  /**
   * Output only. This field lists the names of all metrics that are selectable
   * with the described artifact when it is used in the FROM clause. It is only
   * set for artifacts whose category is RESOURCE.
   *
   * @var string[]
   */
  public $metrics;
  /**
   * Output only. The name of the artifact.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the artifact. Artifact resource names
   * have the form: `SearchAds360Fields/{name}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. This field lists the names of all artifacts, whether a segment
   * or another resource, that segment metrics when included in search queries
   * and when the described artifact is used in the FROM clause. It is only set
   * for artifacts whose category is RESOURCE.
   *
   * @var string[]
   */
  public $segments;
  /**
   * Output only. Whether the artifact can be used in a SELECT clause in search
   * queries.
   *
   * @var bool
   */
  public $selectable;
  /**
   * Output only. The names of all resources, segments, and metrics that are
   * selectable with the described artifact.
   *
   * @var string[]
   */
  public $selectableWith;
  /**
   * Output only. Whether the artifact can be used in a ORDER BY clause in
   * search queries.
   *
   * @var bool
   */
  public $sortable;
  /**
   * Output only. The URL of proto describing the artifact's data type.
   *
   * @var string
   */
  public $typeUrl;

  /**
   * Output only. The names of all resources that are selectable with the
   * described artifact. Fields from these resources do not segment metrics when
   * included in search queries. This field is only set for artifacts whose
   * category is RESOURCE.
   *
   * @param string[] $attributeResources
   */
  public function setAttributeResources($attributeResources)
  {
    $this->attributeResources = $attributeResources;
  }
  /**
   * @return string[]
   */
  public function getAttributeResources()
  {
    return $this->attributeResources;
  }
  /**
   * Output only. The category of the artifact.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, RESOURCE, ATTRIBUTE, SEGMENT, METRIC
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Output only. This field determines the operators that can be used with the
   * artifact in WHERE clauses.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BOOLEAN, DATE, DOUBLE, ENUM, FLOAT,
   * INT32, INT64, MESSAGE, RESOURCE_NAME, STRING, UINT64
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Output only. Values the artifact can assume if it is a field of type ENUM.
   * This field is only set for artifacts of category SEGMENT or ATTRIBUTE.
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
   * Output only. Whether the artifact can be used in a WHERE clause in search
   * queries.
   *
   * @param bool $filterable
   */
  public function setFilterable($filterable)
  {
    $this->filterable = $filterable;
  }
  /**
   * @return bool
   */
  public function getFilterable()
  {
    return $this->filterable;
  }
  /**
   * Output only. Whether the field artifact is repeated.
   *
   * @param bool $isRepeated
   */
  public function setIsRepeated($isRepeated)
  {
    $this->isRepeated = $isRepeated;
  }
  /**
   * @return bool
   */
  public function getIsRepeated()
  {
    return $this->isRepeated;
  }
  /**
   * Output only. This field lists the names of all metrics that are selectable
   * with the described artifact when it is used in the FROM clause. It is only
   * set for artifacts whose category is RESOURCE.
   *
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Output only. The name of the artifact.
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
   * Output only. The resource name of the artifact. Artifact resource names
   * have the form: `SearchAds360Fields/{name}`
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
   * Output only. This field lists the names of all artifacts, whether a segment
   * or another resource, that segment metrics when included in search queries
   * and when the described artifact is used in the FROM clause. It is only set
   * for artifacts whose category is RESOURCE.
   *
   * @param string[] $segments
   */
  public function setSegments($segments)
  {
    $this->segments = $segments;
  }
  /**
   * @return string[]
   */
  public function getSegments()
  {
    return $this->segments;
  }
  /**
   * Output only. Whether the artifact can be used in a SELECT clause in search
   * queries.
   *
   * @param bool $selectable
   */
  public function setSelectable($selectable)
  {
    $this->selectable = $selectable;
  }
  /**
   * @return bool
   */
  public function getSelectable()
  {
    return $this->selectable;
  }
  /**
   * Output only. The names of all resources, segments, and metrics that are
   * selectable with the described artifact.
   *
   * @param string[] $selectableWith
   */
  public function setSelectableWith($selectableWith)
  {
    $this->selectableWith = $selectableWith;
  }
  /**
   * @return string[]
   */
  public function getSelectableWith()
  {
    return $this->selectableWith;
  }
  /**
   * Output only. Whether the artifact can be used in a ORDER BY clause in
   * search queries.
   *
   * @param bool $sortable
   */
  public function setSortable($sortable)
  {
    $this->sortable = $sortable;
  }
  /**
   * @return bool
   */
  public function getSortable()
  {
    return $this->sortable;
  }
  /**
   * Output only. The URL of proto describing the artifact's data type.
   *
   * @param string $typeUrl
   */
  public function setTypeUrl($typeUrl)
  {
    $this->typeUrl = $typeUrl;
  }
  /**
   * @return string
   */
  public function getTypeUrl()
  {
    return $this->typeUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesSearchAds360Field::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesSearchAds360Field');
