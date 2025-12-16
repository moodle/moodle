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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2CatalogAttribute extends \Google\Model
{
  /**
   * Value used when unset.
   */
  public const DYNAMIC_FACETABLE_OPTION_DYNAMIC_FACETABLE_OPTION_UNSPECIFIED = 'DYNAMIC_FACETABLE_OPTION_UNSPECIFIED';
  /**
   * Dynamic facetable option enabled for an attribute.
   */
  public const DYNAMIC_FACETABLE_OPTION_DYNAMIC_FACETABLE_ENABLED = 'DYNAMIC_FACETABLE_ENABLED';
  /**
   * Dynamic facetable option disabled for an attribute.
   */
  public const DYNAMIC_FACETABLE_OPTION_DYNAMIC_FACETABLE_DISABLED = 'DYNAMIC_FACETABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const EXACT_SEARCHABLE_OPTION_EXACT_SEARCHABLE_OPTION_UNSPECIFIED = 'EXACT_SEARCHABLE_OPTION_UNSPECIFIED';
  /**
   * Exact searchable option enabled for an attribute.
   */
  public const EXACT_SEARCHABLE_OPTION_EXACT_SEARCHABLE_ENABLED = 'EXACT_SEARCHABLE_ENABLED';
  /**
   * Exact searchable option disabled for an attribute.
   */
  public const EXACT_SEARCHABLE_OPTION_EXACT_SEARCHABLE_DISABLED = 'EXACT_SEARCHABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const INDEXABLE_OPTION_INDEXABLE_OPTION_UNSPECIFIED = 'INDEXABLE_OPTION_UNSPECIFIED';
  /**
   * Indexable option enabled for an attribute.
   */
  public const INDEXABLE_OPTION_INDEXABLE_ENABLED = 'INDEXABLE_ENABLED';
  /**
   * Indexable option disabled for an attribute.
   */
  public const INDEXABLE_OPTION_INDEXABLE_DISABLED = 'INDEXABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const RETRIEVABLE_OPTION_RETRIEVABLE_OPTION_UNSPECIFIED = 'RETRIEVABLE_OPTION_UNSPECIFIED';
  /**
   * Retrievable option enabled for an attribute.
   */
  public const RETRIEVABLE_OPTION_RETRIEVABLE_ENABLED = 'RETRIEVABLE_ENABLED';
  /**
   * Retrievable option disabled for an attribute.
   */
  public const RETRIEVABLE_OPTION_RETRIEVABLE_DISABLED = 'RETRIEVABLE_DISABLED';
  /**
   * Value used when unset.
   */
  public const SEARCHABLE_OPTION_SEARCHABLE_OPTION_UNSPECIFIED = 'SEARCHABLE_OPTION_UNSPECIFIED';
  /**
   * Searchable option enabled for an attribute.
   */
  public const SEARCHABLE_OPTION_SEARCHABLE_ENABLED = 'SEARCHABLE_ENABLED';
  /**
   * Searchable option disabled for an attribute.
   */
  public const SEARCHABLE_OPTION_SEARCHABLE_DISABLED = 'SEARCHABLE_DISABLED';
  /**
   * The type of the attribute is unknown. Used when type cannot be derived from
   * attribute that is not in_use.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Textual attribute.
   */
  public const TYPE_TEXTUAL = 'TEXTUAL';
  /**
   * Numerical attribute.
   */
  public const TYPE_NUMERICAL = 'NUMERICAL';
  /**
   * If DYNAMIC_FACETABLE_ENABLED, attribute values are available for dynamic
   * facet. Could only be DYNAMIC_FACETABLE_DISABLED if
   * CatalogAttribute.indexable_option is INDEXABLE_DISABLED. Otherwise, an
   * INVALID_ARGUMENT error is returned. Must be specified, otherwise throws
   * INVALID_FORMAT error.
   *
   * @var string
   */
  public $dynamicFacetableOption;
  /**
   * If EXACT_SEARCHABLE_ENABLED, attribute values will be exact searchable.
   * This property only applies to textual custom attributes and requires
   * indexable set to enabled to enable exact-searchable. If unset, the server
   * behavior defaults to EXACT_SEARCHABLE_DISABLED.
   *
   * @var string
   */
  public $exactSearchableOption;
  protected $facetConfigType = GoogleCloudRetailV2CatalogAttributeFacetConfig::class;
  protected $facetConfigDataType = '';
  /**
   * Output only. Indicates whether this attribute has been used by any
   * products. `True` if at least one Product is using this attribute in
   * Product.attributes. Otherwise, this field is `False`. CatalogAttribute can
   * be pre-loaded by using CatalogService.AddCatalogAttribute or
   * CatalogService.UpdateAttributesConfig APIs. This field is `False` for pre-
   * loaded CatalogAttributes. Only pre-loaded catalog attributes that are
   * neither in use by products nor predefined can be deleted. Catalog
   * attributes that are either in use by products or are predefined attributes
   * cannot be deleted; however, their configuration properties will reset to
   * default values upon removal request. After catalog changes, it takes about
   * 10 minutes for this field to update.
   *
   * @var bool
   */
  public $inUse;
  /**
   * When AttributesConfig.attribute_config_level is
   * CATALOG_LEVEL_ATTRIBUTE_CONFIG, if INDEXABLE_ENABLED attribute values are
   * indexed so that it can be filtered, faceted, or boosted in
   * SearchService.Search. Must be specified when
   * AttributesConfig.attribute_config_level is CATALOG_LEVEL_ATTRIBUTE_CONFIG,
   * otherwise throws INVALID_FORMAT error.
   *
   * @var string
   */
  public $indexableOption;
  /**
   * Required. Attribute name. For example: `color`, `brands`,
   * `attributes.custom_attribute`, such as `attributes.xyz`. To be indexable,
   * the attribute name can contain only alpha-numeric characters and
   * underscores. For example, an attribute named `attributes.abc_xyz` can be
   * indexed, but an attribute named `attributes.abc-xyz` cannot be indexed. If
   * the attribute key starts with `attributes.`, then the attribute is a custom
   * attribute. Attributes such as `brands`, `patterns`, and `title` are built-
   * in and called system attributes.
   *
   * @var string
   */
  public $key;
  /**
   * If RETRIEVABLE_ENABLED, attribute values are retrievable in the search
   * results. If unset, the server behavior defaults to RETRIEVABLE_DISABLED.
   *
   * @var string
   */
  public $retrievableOption;
  /**
   * When AttributesConfig.attribute_config_level is
   * CATALOG_LEVEL_ATTRIBUTE_CONFIG, if SEARCHABLE_ENABLED, attribute values are
   * searchable by text queries in SearchService.Search. If SEARCHABLE_ENABLED
   * but attribute type is numerical, attribute values will not be searchable by
   * text queries in SearchService.Search, as there are no text values
   * associated to numerical attributes. Must be specified, when
   * AttributesConfig.attribute_config_level is CATALOG_LEVEL_ATTRIBUTE_CONFIG,
   * otherwise throws INVALID_FORMAT error.
   *
   * @var string
   */
  public $searchableOption;
  /**
   * Output only. The type of this attribute. This is derived from the attribute
   * in Product.attributes.
   *
   * @var string
   */
  public $type;

  /**
   * If DYNAMIC_FACETABLE_ENABLED, attribute values are available for dynamic
   * facet. Could only be DYNAMIC_FACETABLE_DISABLED if
   * CatalogAttribute.indexable_option is INDEXABLE_DISABLED. Otherwise, an
   * INVALID_ARGUMENT error is returned. Must be specified, otherwise throws
   * INVALID_FORMAT error.
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
   * If EXACT_SEARCHABLE_ENABLED, attribute values will be exact searchable.
   * This property only applies to textual custom attributes and requires
   * indexable set to enabled to enable exact-searchable. If unset, the server
   * behavior defaults to EXACT_SEARCHABLE_DISABLED.
   *
   * Accepted values: EXACT_SEARCHABLE_OPTION_UNSPECIFIED,
   * EXACT_SEARCHABLE_ENABLED, EXACT_SEARCHABLE_DISABLED
   *
   * @param self::EXACT_SEARCHABLE_OPTION_* $exactSearchableOption
   */
  public function setExactSearchableOption($exactSearchableOption)
  {
    $this->exactSearchableOption = $exactSearchableOption;
  }
  /**
   * @return self::EXACT_SEARCHABLE_OPTION_*
   */
  public function getExactSearchableOption()
  {
    return $this->exactSearchableOption;
  }
  /**
   * Contains facet options.
   *
   * @param GoogleCloudRetailV2CatalogAttributeFacetConfig $facetConfig
   */
  public function setFacetConfig(GoogleCloudRetailV2CatalogAttributeFacetConfig $facetConfig)
  {
    $this->facetConfig = $facetConfig;
  }
  /**
   * @return GoogleCloudRetailV2CatalogAttributeFacetConfig
   */
  public function getFacetConfig()
  {
    return $this->facetConfig;
  }
  /**
   * Output only. Indicates whether this attribute has been used by any
   * products. `True` if at least one Product is using this attribute in
   * Product.attributes. Otherwise, this field is `False`. CatalogAttribute can
   * be pre-loaded by using CatalogService.AddCatalogAttribute or
   * CatalogService.UpdateAttributesConfig APIs. This field is `False` for pre-
   * loaded CatalogAttributes. Only pre-loaded catalog attributes that are
   * neither in use by products nor predefined can be deleted. Catalog
   * attributes that are either in use by products or are predefined attributes
   * cannot be deleted; however, their configuration properties will reset to
   * default values upon removal request. After catalog changes, it takes about
   * 10 minutes for this field to update.
   *
   * @param bool $inUse
   */
  public function setInUse($inUse)
  {
    $this->inUse = $inUse;
  }
  /**
   * @return bool
   */
  public function getInUse()
  {
    return $this->inUse;
  }
  /**
   * When AttributesConfig.attribute_config_level is
   * CATALOG_LEVEL_ATTRIBUTE_CONFIG, if INDEXABLE_ENABLED attribute values are
   * indexed so that it can be filtered, faceted, or boosted in
   * SearchService.Search. Must be specified when
   * AttributesConfig.attribute_config_level is CATALOG_LEVEL_ATTRIBUTE_CONFIG,
   * otherwise throws INVALID_FORMAT error.
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
   * Required. Attribute name. For example: `color`, `brands`,
   * `attributes.custom_attribute`, such as `attributes.xyz`. To be indexable,
   * the attribute name can contain only alpha-numeric characters and
   * underscores. For example, an attribute named `attributes.abc_xyz` can be
   * indexed, but an attribute named `attributes.abc-xyz` cannot be indexed. If
   * the attribute key starts with `attributes.`, then the attribute is a custom
   * attribute. Attributes such as `brands`, `patterns`, and `title` are built-
   * in and called system attributes.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * If RETRIEVABLE_ENABLED, attribute values are retrievable in the search
   * results. If unset, the server behavior defaults to RETRIEVABLE_DISABLED.
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
   * When AttributesConfig.attribute_config_level is
   * CATALOG_LEVEL_ATTRIBUTE_CONFIG, if SEARCHABLE_ENABLED, attribute values are
   * searchable by text queries in SearchService.Search. If SEARCHABLE_ENABLED
   * but attribute type is numerical, attribute values will not be searchable by
   * text queries in SearchService.Search, as there are no text values
   * associated to numerical attributes. Must be specified, when
   * AttributesConfig.attribute_config_level is CATALOG_LEVEL_ATTRIBUTE_CONFIG,
   * otherwise throws INVALID_FORMAT error.
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
  /**
   * Output only. The type of this attribute. This is derived from the attribute
   * in Product.attributes.
   *
   * Accepted values: UNKNOWN, TEXTUAL, NUMERICAL
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
class_alias(GoogleCloudRetailV2CatalogAttribute::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CatalogAttribute');
