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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaFieldConfig extends \Google\Collection
{
  /**
   * Value used when unset.
   */
  public const COMPLETABLE_OPTION_COMPLETABLE_OPTION_UNSPECIFIED = 'COMPLETABLE_OPTION_UNSPECIFIED';
  /**
   * Completable option enabled for a schema field.
   */
  public const COMPLETABLE_OPTION_COMPLETABLE_ENABLED = 'COMPLETABLE_ENABLED';
  /**
   * Completable option disabled for a schema field.
   */
  public const COMPLETABLE_OPTION_COMPLETABLE_DISABLED = 'COMPLETABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const DYNAMIC_FACETABLE_OPTION_DYNAMIC_FACETABLE_OPTION_UNSPECIFIED = 'DYNAMIC_FACETABLE_OPTION_UNSPECIFIED';
  /**
   * Dynamic facetable option enabled for a schema field.
   */
  public const DYNAMIC_FACETABLE_OPTION_DYNAMIC_FACETABLE_ENABLED = 'DYNAMIC_FACETABLE_ENABLED';
  /**
   * Dynamic facetable option disabled for a schema field.
   */
  public const DYNAMIC_FACETABLE_OPTION_DYNAMIC_FACETABLE_DISABLED = 'DYNAMIC_FACETABLE_DISABLED';
  /**
   * Field type is unspecified.
   */
  public const FIELD_TYPE_FIELD_TYPE_UNSPECIFIED = 'FIELD_TYPE_UNSPECIFIED';
  /**
   * Field value type is Object.
   */
  public const FIELD_TYPE_OBJECT = 'OBJECT';
  /**
   * Field value type is String.
   */
  public const FIELD_TYPE_STRING = 'STRING';
  /**
   * Field value type is Number.
   */
  public const FIELD_TYPE_NUMBER = 'NUMBER';
  /**
   * Field value type is Integer.
   */
  public const FIELD_TYPE_INTEGER = 'INTEGER';
  /**
   * Field value type is Boolean.
   */
  public const FIELD_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * Field value type is Geolocation. Geolocation is expressed as an object with
   * the following keys: * `id`: a string representing the location id *
   * `longitude`: a number representing the longitude coordinate of the location
   * * `latitude`: a number repesenting the latitude coordinate of the location
   * * `address`: a string representing the full address of the location
   * `latitude` and `longitude` must always be provided together. At least one
   * of a) `address` or b) `latitude`-`longitude` pair must be provided.
   */
  public const FIELD_TYPE_GEOLOCATION = 'GEOLOCATION';
  /**
   * Field value type is Datetime. Datetime can be expressed as either: * a
   * number representing milliseconds-since-the-epoch * a string representing
   * milliseconds-since-the-epoch. e.g. `"1420070400001"` * a string
   * representing the [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) date or
   * date and time. e.g. `"2015-01-01"` or `"2015-01-01T12:10:30Z"`
   */
  public const FIELD_TYPE_DATETIME = 'DATETIME';
  /**
   * Value used when unset.
   */
  public const INDEXABLE_OPTION_INDEXABLE_OPTION_UNSPECIFIED = 'INDEXABLE_OPTION_UNSPECIFIED';
  /**
   * Indexable option enabled for a schema field.
   */
  public const INDEXABLE_OPTION_INDEXABLE_ENABLED = 'INDEXABLE_ENABLED';
  /**
   * Indexable option disabled for a schema field.
   */
  public const INDEXABLE_OPTION_INDEXABLE_DISABLED = 'INDEXABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const RECS_FILTERABLE_OPTION_FILTERABLE_OPTION_UNSPECIFIED = 'FILTERABLE_OPTION_UNSPECIFIED';
  /**
   * Filterable option enabled for a schema field.
   */
  public const RECS_FILTERABLE_OPTION_FILTERABLE_ENABLED = 'FILTERABLE_ENABLED';
  /**
   * Filterable option disabled for a schema field.
   */
  public const RECS_FILTERABLE_OPTION_FILTERABLE_DISABLED = 'FILTERABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const RETRIEVABLE_OPTION_RETRIEVABLE_OPTION_UNSPECIFIED = 'RETRIEVABLE_OPTION_UNSPECIFIED';
  /**
   * Retrievable option enabled for a schema field.
   */
  public const RETRIEVABLE_OPTION_RETRIEVABLE_ENABLED = 'RETRIEVABLE_ENABLED';
  /**
   * Retrievable option disabled for a schema field.
   */
  public const RETRIEVABLE_OPTION_RETRIEVABLE_DISABLED = 'RETRIEVABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const SEARCHABLE_OPTION_SEARCHABLE_OPTION_UNSPECIFIED = 'SEARCHABLE_OPTION_UNSPECIFIED';
  /**
   * Searchable option enabled for a schema field.
   */
  public const SEARCHABLE_OPTION_SEARCHABLE_ENABLED = 'SEARCHABLE_ENABLED';
  /**
   * Searchable option disabled for a schema field.
   */
  public const SEARCHABLE_OPTION_SEARCHABLE_DISABLED = 'SEARCHABLE_DISABLED';
  protected $collection_key = 'schemaOrgPaths';
  /**
   * If this field is set, only the corresponding source will be indexed for
   * this field. Otherwise, the values from different sources are merged.
   * Assuming a page with `` in meta tag, and `` in page map: if this enum is
   * set to METATAGS, we will only index ``; if this enum is not set, we will
   * merge them and index ``.
   *
   * @var string[]
   */
  public $advancedSiteSearchDataSources;
  /**
   * If completable_option is COMPLETABLE_ENABLED, field values are directly
   * used and returned as suggestions for Autocomplete in
   * CompletionService.CompleteQuery. If completable_option is unset, the server
   * behavior defaults to COMPLETABLE_DISABLED for fields that support setting
   * completable options, which are just `string` fields. For those fields that
   * do not support setting completable options, the server will skip
   * completable option setting, and setting completable_option for those fields
   * will throw `INVALID_ARGUMENT` error.
   *
   * @var string
   */
  public $completableOption;
  /**
   * If dynamic_facetable_option is DYNAMIC_FACETABLE_ENABLED, field values are
   * available for dynamic facet. Could only be DYNAMIC_FACETABLE_DISABLED if
   * FieldConfig.indexable_option is INDEXABLE_DISABLED. Otherwise, an
   * `INVALID_ARGUMENT` error will be returned. If dynamic_facetable_option is
   * unset, the server behavior defaults to DYNAMIC_FACETABLE_DISABLED for
   * fields that support setting dynamic facetable options. For those fields
   * that do not support setting dynamic facetable options, such as `object` and
   * `boolean`, the server will skip dynamic facetable option setting, and
   * setting dynamic_facetable_option for those fields will throw
   * `INVALID_ARGUMENT` error.
   *
   * @var string
   */
  public $dynamicFacetableOption;
  /**
   * Required. Field path of the schema field. For example: `title`,
   * `description`, `release_info.release_year`.
   *
   * @var string
   */
  public $fieldPath;
  /**
   * Output only. Raw type of the field.
   *
   * @var string
   */
  public $fieldType;
  /**
   * If indexable_option is INDEXABLE_ENABLED, field values are indexed so that
   * it can be filtered or faceted in SearchService.Search. If indexable_option
   * is unset, the server behavior defaults to INDEXABLE_DISABLED for fields
   * that support setting indexable options. For those fields that do not
   * support setting indexable options, such as `object` and `boolean` and key
   * properties, the server will skip indexable_option setting, and setting
   * indexable_option for those fields will throw `INVALID_ARGUMENT` error.
   *
   * @var string
   */
  public $indexableOption;
  /**
   * Output only. Type of the key property that this field is mapped to. Empty
   * string if this is not annotated as mapped to a key property. Example types
   * are `title`, `description`. Full list is defined by `keyPropertyMapping` in
   * the schema field annotation. If the schema field has a `KeyPropertyMapping`
   * annotation, `indexable_option` and `searchable_option` of this field cannot
   * be modified.
   *
   * @var string
   */
  public $keyPropertyType;
  /**
   * Optional. The metatag name found in the HTML page. If user defines this
   * field, the value of this metatag name will be used to extract metatag. If
   * the user does not define this field, the FieldConfig.field_path will be
   * used to extract metatag.
   *
   * @var string
   */
  public $metatagName;
  /**
   * If recs_filterable_option is FILTERABLE_ENABLED, field values are
   * filterable by filter expression in RecommendationService.Recommend. If
   * FILTERABLE_ENABLED but the field type is numerical, field values are not
   * filterable by text queries in RecommendationService.Recommend. Only textual
   * fields are supported. If recs_filterable_option is unset, the default
   * setting is FILTERABLE_DISABLED for fields that support setting filterable
   * options. When a field set to [FILTERABLE_DISABLED] is filtered, a warning
   * is generated and an empty result is returned.
   *
   * @var string
   */
  public $recsFilterableOption;
  /**
   * If retrievable_option is RETRIEVABLE_ENABLED, field values are included in
   * the search results. If retrievable_option is unset, the server behavior
   * defaults to RETRIEVABLE_DISABLED for fields that support setting
   * retrievable options. For those fields that do not support setting
   * retrievable options, such as `object` and `boolean`, the server will skip
   * retrievable option setting, and setting retrievable_option for those fields
   * will throw `INVALID_ARGUMENT` error.
   *
   * @var string
   */
  public $retrievableOption;
  /**
   * Field paths for indexing custom attribute from schema.org data. More
   * details of schema.org and its defined types can be found at
   * [schema.org](https://schema.org). It is only used on advanced site search
   * schema. Currently only support full path from root. The full path to a
   * field is constructed by concatenating field names, starting from `_root`,
   * with a period `.` as the delimiter. Examples: * Publish date of the root:
   * _root.datePublished * Publish date of the reviews:
   * _root.review.datePublished
   *
   * @var string[]
   */
  public $schemaOrgPaths;
  /**
   * If searchable_option is SEARCHABLE_ENABLED, field values are searchable by
   * text queries in SearchService.Search. If SEARCHABLE_ENABLED but field type
   * is numerical, field values will not be searchable by text queries in
   * SearchService.Search, as there are no text values associated to numerical
   * fields. If searchable_option is unset, the server behavior defaults to
   * SEARCHABLE_DISABLED for fields that support setting searchable options.
   * Only `string` fields that have no key property mapping support setting
   * searchable_option. For those fields that do not support setting searchable
   * options, the server will skip searchable option setting, and setting
   * searchable_option for those fields will throw `INVALID_ARGUMENT` error.
   *
   * @var string
   */
  public $searchableOption;

  /**
   * If this field is set, only the corresponding source will be indexed for
   * this field. Otherwise, the values from different sources are merged.
   * Assuming a page with `` in meta tag, and `` in page map: if this enum is
   * set to METATAGS, we will only index ``; if this enum is not set, we will
   * merge them and index ``.
   *
   * @param string[] $advancedSiteSearchDataSources
   */
  public function setAdvancedSiteSearchDataSources($advancedSiteSearchDataSources)
  {
    $this->advancedSiteSearchDataSources = $advancedSiteSearchDataSources;
  }
  /**
   * @return string[]
   */
  public function getAdvancedSiteSearchDataSources()
  {
    return $this->advancedSiteSearchDataSources;
  }
  /**
   * If completable_option is COMPLETABLE_ENABLED, field values are directly
   * used and returned as suggestions for Autocomplete in
   * CompletionService.CompleteQuery. If completable_option is unset, the server
   * behavior defaults to COMPLETABLE_DISABLED for fields that support setting
   * completable options, which are just `string` fields. For those fields that
   * do not support setting completable options, the server will skip
   * completable option setting, and setting completable_option for those fields
   * will throw `INVALID_ARGUMENT` error.
   *
   * Accepted values: COMPLETABLE_OPTION_UNSPECIFIED, COMPLETABLE_ENABLED,
   * COMPLETABLE_DISABLED
   *
   * @param self::COMPLETABLE_OPTION_* $completableOption
   */
  public function setCompletableOption($completableOption)
  {
    $this->completableOption = $completableOption;
  }
  /**
   * @return self::COMPLETABLE_OPTION_*
   */
  public function getCompletableOption()
  {
    return $this->completableOption;
  }
  /**
   * If dynamic_facetable_option is DYNAMIC_FACETABLE_ENABLED, field values are
   * available for dynamic facet. Could only be DYNAMIC_FACETABLE_DISABLED if
   * FieldConfig.indexable_option is INDEXABLE_DISABLED. Otherwise, an
   * `INVALID_ARGUMENT` error will be returned. If dynamic_facetable_option is
   * unset, the server behavior defaults to DYNAMIC_FACETABLE_DISABLED for
   * fields that support setting dynamic facetable options. For those fields
   * that do not support setting dynamic facetable options, such as `object` and
   * `boolean`, the server will skip dynamic facetable option setting, and
   * setting dynamic_facetable_option for those fields will throw
   * `INVALID_ARGUMENT` error.
   *
   * Accepted values: DYNAMIC_FACETABLE_OPTION_UNSPECIFIED,
   * DYNAMIC_FACETABLE_ENABLED, DYNAMIC_FACETABLE_DISABLED
   *
   * @param self::DYNAMIC_FACETABLE_OPTION_* $dynamicFacetableOption
   */
  public function setDynamicFacetableOption($dynamicFacetableOption)
  {
    $this->dynamicFacetableOption = $dynamicFacetableOption;
  }
  /**
   * @return self::DYNAMIC_FACETABLE_OPTION_*
   */
  public function getDynamicFacetableOption()
  {
    return $this->dynamicFacetableOption;
  }
  /**
   * Required. Field path of the schema field. For example: `title`,
   * `description`, `release_info.release_year`.
   *
   * @param string $fieldPath
   */
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  /**
   * @return string
   */
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
  /**
   * Output only. Raw type of the field.
   *
   * Accepted values: FIELD_TYPE_UNSPECIFIED, OBJECT, STRING, NUMBER, INTEGER,
   * BOOLEAN, GEOLOCATION, DATETIME
   *
   * @param self::FIELD_TYPE_* $fieldType
   */
  public function setFieldType($fieldType)
  {
    $this->fieldType = $fieldType;
  }
  /**
   * @return self::FIELD_TYPE_*
   */
  public function getFieldType()
  {
    return $this->fieldType;
  }
  /**
   * If indexable_option is INDEXABLE_ENABLED, field values are indexed so that
   * it can be filtered or faceted in SearchService.Search. If indexable_option
   * is unset, the server behavior defaults to INDEXABLE_DISABLED for fields
   * that support setting indexable options. For those fields that do not
   * support setting indexable options, such as `object` and `boolean` and key
   * properties, the server will skip indexable_option setting, and setting
   * indexable_option for those fields will throw `INVALID_ARGUMENT` error.
   *
   * Accepted values: INDEXABLE_OPTION_UNSPECIFIED, INDEXABLE_ENABLED,
   * INDEXABLE_DISABLED
   *
   * @param self::INDEXABLE_OPTION_* $indexableOption
   */
  public function setIndexableOption($indexableOption)
  {
    $this->indexableOption = $indexableOption;
  }
  /**
   * @return self::INDEXABLE_OPTION_*
   */
  public function getIndexableOption()
  {
    return $this->indexableOption;
  }
  /**
   * Output only. Type of the key property that this field is mapped to. Empty
   * string if this is not annotated as mapped to a key property. Example types
   * are `title`, `description`. Full list is defined by `keyPropertyMapping` in
   * the schema field annotation. If the schema field has a `KeyPropertyMapping`
   * annotation, `indexable_option` and `searchable_option` of this field cannot
   * be modified.
   *
   * @param string $keyPropertyType
   */
  public function setKeyPropertyType($keyPropertyType)
  {
    $this->keyPropertyType = $keyPropertyType;
  }
  /**
   * @return string
   */
  public function getKeyPropertyType()
  {
    return $this->keyPropertyType;
  }
  /**
   * Optional. The metatag name found in the HTML page. If user defines this
   * field, the value of this metatag name will be used to extract metatag. If
   * the user does not define this field, the FieldConfig.field_path will be
   * used to extract metatag.
   *
   * @param string $metatagName
   */
  public function setMetatagName($metatagName)
  {
    $this->metatagName = $metatagName;
  }
  /**
   * @return string
   */
  public function getMetatagName()
  {
    return $this->metatagName;
  }
  /**
   * If recs_filterable_option is FILTERABLE_ENABLED, field values are
   * filterable by filter expression in RecommendationService.Recommend. If
   * FILTERABLE_ENABLED but the field type is numerical, field values are not
   * filterable by text queries in RecommendationService.Recommend. Only textual
   * fields are supported. If recs_filterable_option is unset, the default
   * setting is FILTERABLE_DISABLED for fields that support setting filterable
   * options. When a field set to [FILTERABLE_DISABLED] is filtered, a warning
   * is generated and an empty result is returned.
   *
   * Accepted values: FILTERABLE_OPTION_UNSPECIFIED, FILTERABLE_ENABLED,
   * FILTERABLE_DISABLED
   *
   * @param self::RECS_FILTERABLE_OPTION_* $recsFilterableOption
   */
  public function setRecsFilterableOption($recsFilterableOption)
  {
    $this->recsFilterableOption = $recsFilterableOption;
  }
  /**
   * @return self::RECS_FILTERABLE_OPTION_*
   */
  public function getRecsFilterableOption()
  {
    return $this->recsFilterableOption;
  }
  /**
   * If retrievable_option is RETRIEVABLE_ENABLED, field values are included in
   * the search results. If retrievable_option is unset, the server behavior
   * defaults to RETRIEVABLE_DISABLED for fields that support setting
   * retrievable options. For those fields that do not support setting
   * retrievable options, such as `object` and `boolean`, the server will skip
   * retrievable option setting, and setting retrievable_option for those fields
   * will throw `INVALID_ARGUMENT` error.
   *
   * Accepted values: RETRIEVABLE_OPTION_UNSPECIFIED, RETRIEVABLE_ENABLED,
   * RETRIEVABLE_DISABLED
   *
   * @param self::RETRIEVABLE_OPTION_* $retrievableOption
   */
  public function setRetrievableOption($retrievableOption)
  {
    $this->retrievableOption = $retrievableOption;
  }
  /**
   * @return self::RETRIEVABLE_OPTION_*
   */
  public function getRetrievableOption()
  {
    return $this->retrievableOption;
  }
  /**
   * Field paths for indexing custom attribute from schema.org data. More
   * details of schema.org and its defined types can be found at
   * [schema.org](https://schema.org). It is only used on advanced site search
   * schema. Currently only support full path from root. The full path to a
   * field is constructed by concatenating field names, starting from `_root`,
   * with a period `.` as the delimiter. Examples: * Publish date of the root:
   * _root.datePublished * Publish date of the reviews:
   * _root.review.datePublished
   *
   * @param string[] $schemaOrgPaths
   */
  public function setSchemaOrgPaths($schemaOrgPaths)
  {
    $this->schemaOrgPaths = $schemaOrgPaths;
  }
  /**
   * @return string[]
   */
  public function getSchemaOrgPaths()
  {
    return $this->schemaOrgPaths;
  }
  /**
   * If searchable_option is SEARCHABLE_ENABLED, field values are searchable by
   * text queries in SearchService.Search. If SEARCHABLE_ENABLED but field type
   * is numerical, field values will not be searchable by text queries in
   * SearchService.Search, as there are no text values associated to numerical
   * fields. If searchable_option is unset, the server behavior defaults to
   * SEARCHABLE_DISABLED for fields that support setting searchable options.
   * Only `string` fields that have no key property mapping support setting
   * searchable_option. For those fields that do not support setting searchable
   * options, the server will skip searchable option setting, and setting
   * searchable_option for those fields will throw `INVALID_ARGUMENT` error.
   *
   * Accepted values: SEARCHABLE_OPTION_UNSPECIFIED, SEARCHABLE_ENABLED,
   * SEARCHABLE_DISABLED
   *
   * @param self::SEARCHABLE_OPTION_* $searchableOption
   */
  public function setSearchableOption($searchableOption)
  {
    $this->searchableOption = $searchableOption;
  }
  /**
   * @return self::SEARCHABLE_OPTION_*
   */
  public function getSearchableOption()
  {
    return $this->searchableOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaFieldConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaFieldConfig');
