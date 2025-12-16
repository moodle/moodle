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

class GoogleCloudRetailV2AttributesConfig extends \Google\Model
{
  /**
   * Value used when unset. In this case, server behavior defaults to
   * CATALOG_LEVEL_ATTRIBUTE_CONFIG.
   */
  public const ATTRIBUTE_CONFIG_LEVEL_ATTRIBUTE_CONFIG_LEVEL_UNSPECIFIED = 'ATTRIBUTE_CONFIG_LEVEL_UNSPECIFIED';
  /**
   * At this level, we honor the attribute configurations set in
   * Product.attributes.
   */
  public const ATTRIBUTE_CONFIG_LEVEL_PRODUCT_LEVEL_ATTRIBUTE_CONFIG = 'PRODUCT_LEVEL_ATTRIBUTE_CONFIG';
  /**
   * At this level, we honor the attribute configurations set in
   * `CatalogConfig.attribute_configs`.
   */
  public const ATTRIBUTE_CONFIG_LEVEL_CATALOG_LEVEL_ATTRIBUTE_CONFIG = 'CATALOG_LEVEL_ATTRIBUTE_CONFIG';
  /**
   * Output only. The AttributeConfigLevel used for this catalog.
   *
   * @var string
   */
  public $attributeConfigLevel;
  protected $catalogAttributesType = GoogleCloudRetailV2CatalogAttribute::class;
  protected $catalogAttributesDataType = 'map';
  /**
   * Required. Immutable. The fully qualified resource name of the attribute
   * config. Format: `projects/locations/catalogs/attributesConfig`
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The AttributeConfigLevel used for this catalog.
   *
   * Accepted values: ATTRIBUTE_CONFIG_LEVEL_UNSPECIFIED,
   * PRODUCT_LEVEL_ATTRIBUTE_CONFIG, CATALOG_LEVEL_ATTRIBUTE_CONFIG
   *
   * @param self::ATTRIBUTE_CONFIG_LEVEL_* $attributeConfigLevel
   */
  public function setAttributeConfigLevel($attributeConfigLevel)
  {
    $this->attributeConfigLevel = $attributeConfigLevel;
  }
  /**
   * @return self::ATTRIBUTE_CONFIG_LEVEL_*
   */
  public function getAttributeConfigLevel()
  {
    return $this->attributeConfigLevel;
  }
  /**
   * Enable attribute(s) config at catalog level. For example, indexable,
   * dynamic_facetable, or searchable for each attribute. The key is catalog
   * attribute's name. For example: `color`, `brands`,
   * `attributes.custom_attribute`, such as `attributes.xyz`. The maximum number
   * of catalog attributes allowed in a request is 1000.
   *
   * @param GoogleCloudRetailV2CatalogAttribute[] $catalogAttributes
   */
  public function setCatalogAttributes($catalogAttributes)
  {
    $this->catalogAttributes = $catalogAttributes;
  }
  /**
   * @return GoogleCloudRetailV2CatalogAttribute[]
   */
  public function getCatalogAttributes()
  {
    return $this->catalogAttributes;
  }
  /**
   * Required. Immutable. The fully qualified resource name of the attribute
   * config. Format: `projects/locations/catalogs/attributesConfig`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2AttributesConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2AttributesConfig');
