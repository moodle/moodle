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

class GoogleCloudRetailV2CustomAttribute extends \Google\Collection
{
  protected $collection_key = 'text';
  /**
   * This field is normally ignored unless
   * AttributesConfig.attribute_config_level of the Catalog is set to the
   * deprecated 'PRODUCT_LEVEL_ATTRIBUTE_CONFIG' mode. For information about
   * product-level attribute configuration, see [Configuration
   * modes](https://cloud.google.com/retail/docs/attribute-config#config-modes).
   * If true, custom attribute values are indexed, so that they can be filtered,
   * faceted or boosted in SearchService.Search. This field is ignored in a
   * UserEvent. See SearchRequest.filter, SearchRequest.facet_specs and
   * SearchRequest.boost_spec for more details.
   *
   * @deprecated
   * @var bool
   */
  public $indexable;
  /**
   * The numerical values of this custom attribute. For example, `[2.3, 15.4]`
   * when the key is "lengths_cm". Exactly one of text or numbers should be set.
   * Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var []
   */
  public $numbers;
  /**
   * This field is normally ignored unless
   * AttributesConfig.attribute_config_level of the Catalog is set to the
   * deprecated 'PRODUCT_LEVEL_ATTRIBUTE_CONFIG' mode. For information about
   * product-level attribute configuration, see [Configuration
   * modes](https://cloud.google.com/retail/docs/attribute-config#config-modes).
   * If true, custom attribute values are searchable by text queries in
   * SearchService.Search. This field is ignored in a UserEvent. Only set if
   * type text is set. Otherwise, a INVALID_ARGUMENT error is returned.
   *
   * @deprecated
   * @var bool
   */
  public $searchable;
  /**
   * The textual values of this custom attribute. For example, `["yellow",
   * "green"]` when the key is "color". Empty string is not allowed. Otherwise,
   * an INVALID_ARGUMENT error is returned. Exactly one of text or numbers
   * should be set. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string[]
   */
  public $text;

  /**
   * This field is normally ignored unless
   * AttributesConfig.attribute_config_level of the Catalog is set to the
   * deprecated 'PRODUCT_LEVEL_ATTRIBUTE_CONFIG' mode. For information about
   * product-level attribute configuration, see [Configuration
   * modes](https://cloud.google.com/retail/docs/attribute-config#config-modes).
   * If true, custom attribute values are indexed, so that they can be filtered,
   * faceted or boosted in SearchService.Search. This field is ignored in a
   * UserEvent. See SearchRequest.filter, SearchRequest.facet_specs and
   * SearchRequest.boost_spec for more details.
   *
   * @deprecated
   * @param bool $indexable
   */
  public function setIndexable($indexable)
  {
    $this->indexable = $indexable;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIndexable()
  {
    return $this->indexable;
  }
  public function setNumbers($numbers)
  {
    $this->numbers = $numbers;
  }
  public function getNumbers()
  {
    return $this->numbers;
  }
  /**
   * This field is normally ignored unless
   * AttributesConfig.attribute_config_level of the Catalog is set to the
   * deprecated 'PRODUCT_LEVEL_ATTRIBUTE_CONFIG' mode. For information about
   * product-level attribute configuration, see [Configuration
   * modes](https://cloud.google.com/retail/docs/attribute-config#config-modes).
   * If true, custom attribute values are searchable by text queries in
   * SearchService.Search. This field is ignored in a UserEvent. Only set if
   * type text is set. Otherwise, a INVALID_ARGUMENT error is returned.
   *
   * @deprecated
   * @param bool $searchable
   */
  public function setSearchable($searchable)
  {
    $this->searchable = $searchable;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getSearchable()
  {
    return $this->searchable;
  }
  /**
   * The textual values of this custom attribute. For example, `["yellow",
   * "green"]` when the key is "color". Empty string is not allowed. Otherwise,
   * an INVALID_ARGUMENT error is returned. Exactly one of text or numbers
   * should be set. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @param string[] $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string[]
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CustomAttribute::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CustomAttribute');
