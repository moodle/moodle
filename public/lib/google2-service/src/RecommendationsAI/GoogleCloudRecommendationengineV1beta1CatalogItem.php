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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1CatalogItem extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $categoryHierarchiesType = GoogleCloudRecommendationengineV1beta1CatalogItemCategoryHierarchy::class;
  protected $categoryHierarchiesDataType = 'array';
  /**
   * Optional. Catalog item description. UTF-8 encoded string with a length
   * limit of 5 KiB.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Catalog item identifier. UTF-8 encoded string with a length limit
   * of 128 bytes. This id must be unique among all catalog items within the
   * same catalog. It should also be used when logging user events in order for
   * the user events to be joined with the Catalog.
   *
   * @var string
   */
  public $id;
  protected $itemAttributesType = GoogleCloudRecommendationengineV1beta1FeatureMap::class;
  protected $itemAttributesDataType = '';
  /**
   * Optional. Variant group identifier for prediction results. UTF-8 encoded
   * string with a length limit of 128 bytes. This field must be enabled before
   * it can be used. [Learn more](/recommendations-ai/docs/catalog#item-group-
   * id).
   *
   * @var string
   */
  public $itemGroupId;
  /**
   * Optional. Deprecated. The model automatically detects the text language.
   * Your catalog can include text in different languages, but duplicating
   * catalog items to provide text in multiple languages can result in degraded
   * model performance.
   *
   * @var string
   */
  public $languageCode;
  protected $productMetadataType = GoogleCloudRecommendationengineV1beta1ProductCatalogItem::class;
  protected $productMetadataDataType = '';
  /**
   * Optional. Filtering tags associated with the catalog item. Each tag should
   * be a UTF-8 encoded string with a length limit of 1 KiB. This tag can be
   * used for filtering recommendation results by passing the tag as part of the
   * predict request filter.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Required. Catalog item title. UTF-8 encoded string with a length limit of 1
   * KiB.
   *
   * @var string
   */
  public $title;

  /**
   * Required. Catalog item categories. This field is repeated for supporting
   * one catalog item belonging to several parallel category hierarchies. For
   * example, if a shoes product belongs to both ["Shoes & Accessories" ->
   * "Shoes"] and ["Sports & Fitness" -> "Athletic Clothing" -> "Shoes"], it
   * could be represented as: "categoryHierarchies": [ { "categories": ["Shoes &
   * Accessories", "Shoes"]}, { "categories": ["Sports & Fitness", "Athletic
   * Clothing", "Shoes"] } ]
   *
   * @param GoogleCloudRecommendationengineV1beta1CatalogItemCategoryHierarchy[] $categoryHierarchies
   */
  public function setCategoryHierarchies($categoryHierarchies)
  {
    $this->categoryHierarchies = $categoryHierarchies;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1CatalogItemCategoryHierarchy[]
   */
  public function getCategoryHierarchies()
  {
    return $this->categoryHierarchies;
  }
  /**
   * Optional. Catalog item description. UTF-8 encoded string with a length
   * limit of 5 KiB.
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
   * Required. Catalog item identifier. UTF-8 encoded string with a length limit
   * of 128 bytes. This id must be unique among all catalog items within the
   * same catalog. It should also be used when logging user events in order for
   * the user events to be joined with the Catalog.
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
   * Optional. Highly encouraged. Extra catalog item attributes to be included
   * in the recommendation model. For example, for retail products, this could
   * include the store name, vendor, style, color, etc. These are very strong
   * signals for recommendation model, thus we highly recommend providing the
   * item attributes here.
   *
   * @param GoogleCloudRecommendationengineV1beta1FeatureMap $itemAttributes
   */
  public function setItemAttributes(GoogleCloudRecommendationengineV1beta1FeatureMap $itemAttributes)
  {
    $this->itemAttributes = $itemAttributes;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1FeatureMap
   */
  public function getItemAttributes()
  {
    return $this->itemAttributes;
  }
  /**
   * Optional. Variant group identifier for prediction results. UTF-8 encoded
   * string with a length limit of 128 bytes. This field must be enabled before
   * it can be used. [Learn more](/recommendations-ai/docs/catalog#item-group-
   * id).
   *
   * @param string $itemGroupId
   */
  public function setItemGroupId($itemGroupId)
  {
    $this->itemGroupId = $itemGroupId;
  }
  /**
   * @return string
   */
  public function getItemGroupId()
  {
    return $this->itemGroupId;
  }
  /**
   * Optional. Deprecated. The model automatically detects the text language.
   * Your catalog can include text in different languages, but duplicating
   * catalog items to provide text in multiple languages can result in degraded
   * model performance.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. Metadata specific to retail products.
   *
   * @param GoogleCloudRecommendationengineV1beta1ProductCatalogItem $productMetadata
   */
  public function setProductMetadata(GoogleCloudRecommendationengineV1beta1ProductCatalogItem $productMetadata)
  {
    $this->productMetadata = $productMetadata;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1ProductCatalogItem
   */
  public function getProductMetadata()
  {
    return $this->productMetadata;
  }
  /**
   * Optional. Filtering tags associated with the catalog item. Each tag should
   * be a UTF-8 encoded string with a length limit of 1 KiB. This tag can be
   * used for filtering recommendation results by passing the tag as part of the
   * predict request filter.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Required. Catalog item title. UTF-8 encoded string with a length limit of 1
   * KiB.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1CatalogItem::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1CatalogItem');
