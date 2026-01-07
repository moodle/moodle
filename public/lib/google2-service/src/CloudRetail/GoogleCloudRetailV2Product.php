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

class GoogleCloudRetailV2Product extends \Google\Collection
{
  /**
   * Default product availability. Default to Availability.IN_STOCK if unset.
   */
  public const AVAILABILITY_AVAILABILITY_UNSPECIFIED = 'AVAILABILITY_UNSPECIFIED';
  /**
   * Product in stock.
   */
  public const AVAILABILITY_IN_STOCK = 'IN_STOCK';
  /**
   * Product out of stock.
   */
  public const AVAILABILITY_OUT_OF_STOCK = 'OUT_OF_STOCK';
  /**
   * Product that is in pre-order state.
   */
  public const AVAILABILITY_PREORDER = 'PREORDER';
  /**
   * Product that is back-ordered (i.e. temporarily out of stock).
   */
  public const AVAILABILITY_BACKORDER = 'BACKORDER';
  /**
   * Default value. Default to
   * Catalog.product_level_config.ingestion_product_type if unset.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The primary type. As the primary unit for predicting, indexing and search
   * serving, a Type.PRIMARY Product is grouped with multiple Type.VARIANT
   * Products.
   */
  public const TYPE_PRIMARY = 'PRIMARY';
  /**
   * The variant type. Type.VARIANT Products usually share some common
   * attributes on the same Type.PRIMARY Products, but they have variant
   * attributes like different colors, sizes and prices, etc.
   */
  public const TYPE_VARIANT = 'VARIANT';
  /**
   * The collection type. Collection products are bundled Type.PRIMARY Products
   * or Type.VARIANT Products that are sold together, such as a jewelry set with
   * necklaces, earrings and rings, etc.
   */
  public const TYPE_COLLECTION = 'COLLECTION';
  protected $collection_key = 'variants';
  protected $attributesType = GoogleCloudRetailV2CustomAttribute::class;
  protected $attributesDataType = 'map';
  protected $audienceType = GoogleCloudRetailV2Audience::class;
  protected $audienceDataType = '';
  /**
   * The online availability of the Product. Default to Availability.IN_STOCK.
   * For primary products with variants set the availability of the primary as
   * Availability.OUT_OF_STOCK and set the true availability at the variant
   * level. This way the primary product will be considered "in stock" as long
   * as it has at least one variant in stock. For primary products with no
   * variants set the true availability at the primary level. Corresponding
   * properties: Google Merchant Center property
   * [availability](https://support.google.com/merchants/answer/6324448).
   * Schema.org property [Offer.availability](https://schema.org/availability).
   *
   * @var string
   */
  public $availability;
  /**
   * The available quantity of the item.
   *
   * @var int
   */
  public $availableQuantity;
  /**
   * The timestamp when this Product becomes available for SearchService.Search.
   * Note that this is only applicable to Type.PRIMARY and Type.COLLECTION, and
   * ignored for Type.VARIANT.
   *
   * @var string
   */
  public $availableTime;
  /**
   * The brands of the product. A maximum of 30 brands are allowed unless
   * overridden through the Google Cloud console. Each brand must be a UTF-8
   * encoded string with a length limit of 1,000 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [brand](https://support.google.com/merchants/answer/6324351). Schema.org
   * property [Product.brand](https://schema.org/brand).
   *
   * @var string[]
   */
  public $brands;
  /**
   * Product categories. This field is repeated for supporting one product
   * belonging to several parallel categories. Strongly recommended using the
   * full path for better search / recommendation quality. To represent full
   * path of category, use '>' sign to separate different hierarchies. If '>' is
   * part of the category name, replace it with other character(s). For example,
   * if a shoes product belongs to both ["Shoes & Accessories" -> "Shoes"] and
   * ["Sports & Fitness" -> "Athletic Clothing" -> "Shoes"], it could be
   * represented as: "categories": [ "Shoes & Accessories > Shoes", "Sports &
   * Fitness > Athletic Clothing > Shoes" ] Must be set for Type.PRIMARY Product
   * otherwise an INVALID_ARGUMENT error is returned. At most 250 values are
   * allowed per Product unless overridden through the Google Cloud console.
   * Empty values are not allowed. Each value must be a UTF-8 encoded string
   * with a length limit of 5,000 characters. Otherwise, an INVALID_ARGUMENT
   * error is returned. Corresponding properties: Google Merchant Center
   * property google_product_category. Schema.org property [Product.category]
   * (https://schema.org/category). [mc_google_product_category]:
   * https://support.google.com/merchants/answer/6324436
   *
   * @var string[]
   */
  public $categories;
  /**
   * The id of the collection members when type is Type.COLLECTION. Non-existent
   * product ids are allowed. The type of the members must be either
   * Type.PRIMARY or Type.VARIANT otherwise an INVALID_ARGUMENT error is thrown.
   * Should not set it for other types. A maximum of 1000 values are allowed.
   * Otherwise, an INVALID_ARGUMENT error is return.
   *
   * @var string[]
   */
  public $collectionMemberIds;
  protected $colorInfoType = GoogleCloudRetailV2ColorInfo::class;
  protected $colorInfoDataType = '';
  /**
   * The condition of the product. Strongly encouraged to use the standard
   * values: "new", "refurbished", "used". A maximum of 1 value is allowed per
   * Product. Each value must be a UTF-8 encoded string with a length limit of
   * 128 characters. Otherwise, an INVALID_ARGUMENT error is returned.
   * Corresponding properties: Google Merchant Center property
   * [condition](https://support.google.com/merchants/answer/6324469).
   * Schema.org property
   * [Offer.itemCondition](https://schema.org/itemCondition).
   *
   * @var string[]
   */
  public $conditions;
  /**
   * Product description. This field must be a UTF-8 encoded string with a
   * length limit of 5,000 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. Corresponding properties: Google Merchant Center property
   * [description](https://support.google.com/merchants/answer/6324468).
   * Schema.org property [Product.description](https://schema.org/description).
   *
   * @var string
   */
  public $description;
  /**
   * Note that this field is applied in the following ways: * If the Product is
   * already expired when it is uploaded, this product is not indexed for
   * search. * If the Product is not expired when it is uploaded, only the
   * Type.PRIMARY's and Type.COLLECTION's expireTime is respected, and
   * Type.VARIANT's expireTime is not used. In general, we suggest the users to
   * delete the stale products explicitly, instead of using this field to
   * determine staleness. expire_time must be later than available_time and
   * publish_time, otherwise an INVALID_ARGUMENT error is thrown. Corresponding
   * properties: Google Merchant Center property
   * [expiration_date](https://support.google.com/merchants/answer/6324499).
   *
   * @var string
   */
  public $expireTime;
  protected $fulfillmentInfoType = GoogleCloudRetailV2FulfillmentInfo::class;
  protected $fulfillmentInfoDataType = 'array';
  /**
   * The Global Trade Item Number (GTIN) of the product. This field must be a
   * UTF-8 encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. This field must be a Unigram.
   * Otherwise, an INVALID_ARGUMENT error is returned. Corresponding properties:
   * Google Merchant Center property
   * [gtin](https://support.google.com/merchants/answer/6324461). Schema.org
   * property [Product.isbn](https://schema.org/isbn),
   * [Product.gtin8](https://schema.org/gtin8),
   * [Product.gtin12](https://schema.org/gtin12),
   * [Product.gtin13](https://schema.org/gtin13), or
   * [Product.gtin14](https://schema.org/gtin14). If the value is not a valid
   * GTIN, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $gtin;
  /**
   * Immutable. Product identifier, which is the final component of name. For
   * example, this field is "id_1", if name is `projects/locations/global/catalo
   * gs/default_catalog/branches/default_branch/products/id_1`. This field must
   * be a UTF-8 encoded string with a length limit of 128 characters. Otherwise,
   * an INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [id](https://support.google.com/merchants/answer/6324405). Schema.org
   * property [Product.sku](https://schema.org/sku).
   *
   * @var string
   */
  public $id;
  protected $imagesType = GoogleCloudRetailV2Image::class;
  protected $imagesDataType = 'array';
  /**
   * Language of the title/description and other string attributes. Use language
   * tags defined by [BCP 47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). For
   * product prediction, this field is ignored and the model automatically
   * detects the text language. The Product can include text in different
   * languages, but duplicating Products to provide text in multiple languages
   * can result in degraded model performance. For product search this field is
   * in use. It defaults to "en-US" if unset.
   *
   * @var string
   */
  public $languageCode;
  protected $localInventoriesType = GoogleCloudRetailV2LocalInventory::class;
  protected $localInventoriesDataType = 'array';
  /**
   * The material of the product. For example, "leather", "wooden". A maximum of
   * 20 values are allowed. Each value must be a UTF-8 encoded string with a
   * length limit of 200 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. Corresponding properties: Google Merchant Center property
   * [material](https://support.google.com/merchants/answer/6324410). Schema.org
   * property [Product.material](https://schema.org/material).
   *
   * @var string[]
   */
  public $materials;
  /**
   * Immutable. Full resource name of the product, such as `projects/locations/g
   * lobal/catalogs/default_catalog/branches/default_branch/products/product_id`
   * .
   *
   * @var string
   */
  public $name;
  /**
   * The pattern or graphic print of the product. For example, "striped", "polka
   * dot", "paisley". A maximum of 20 values are allowed per Product. Each value
   * must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an INVALID_ARGUMENT error is returned. Corresponding properties:
   * Google Merchant Center property
   * [pattern](https://support.google.com/merchants/answer/6324483). Schema.org
   * property [Product.pattern](https://schema.org/pattern).
   *
   * @var string[]
   */
  public $patterns;
  protected $priceInfoType = GoogleCloudRetailV2PriceInfo::class;
  protected $priceInfoDataType = '';
  /**
   * Variant group identifier. Must be an id, with the same parent branch with
   * this product. Otherwise, an error is thrown. For Type.PRIMARY Products,
   * this field can only be empty or set to the same value as id. For VARIANT
   * Products, this field cannot be empty. A maximum of 2,000 products are
   * allowed to share the same Type.PRIMARY Product. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [item_group_id](https://support.google.com/merchants/answer/6324507).
   * Schema.org property
   * [Product.inProductGroupWithID](https://schema.org/inProductGroupWithID).
   *
   * @var string
   */
  public $primaryProductId;
  protected $promotionsType = GoogleCloudRetailV2Promotion::class;
  protected $promotionsDataType = 'array';
  /**
   * The timestamp when the product is published by the retailer for the first
   * time, which indicates the freshness of the products. Note that this field
   * is different from available_time, given it purely describes product
   * freshness regardless of when it is available on search and recommendation.
   *
   * @var string
   */
  public $publishTime;
  protected $ratingType = GoogleCloudRetailV2Rating::class;
  protected $ratingDataType = '';
  /**
   * Indicates which fields in the Products are returned in SearchResponse.
   * Supported fields for all types: * audience * availability * brands *
   * color_info * conditions * gtin * materials * name * patterns * price_info *
   * rating * sizes * title * uri Supported fields only for Type.PRIMARY and
   * Type.COLLECTION: * categories * description * images Supported fields only
   * for Type.VARIANT: * Only the first image in images To mark attributes as
   * retrievable, include paths of the form "attributes.key" where "key" is the
   * key of a custom attribute, as specified in attributes. For Type.PRIMARY and
   * Type.COLLECTION, the following fields are always returned in SearchResponse
   * by default: * name For Type.VARIANT, the following fields are always
   * returned in by default: * name * color_info Note: Returning more fields in
   * SearchResponse can increase response payload size and serving latency. This
   * field is deprecated. Use the retrievable site-wide control instead.
   *
   * @deprecated
   * @var string
   */
  public $retrievableFields;
  /**
   * The size of the product. To represent different size systems or size types,
   * consider using this format: [[[size_system:]size_type:]size_value]. For
   * example, in "US:MENS:M", "US" represents size system; "MENS" represents
   * size type; "M" represents size value. In "GIRLS:27", size system is empty;
   * "GIRLS" represents size type; "27" represents size value. In "32 inches",
   * both size system and size type are empty, while size value is "32 inches".
   * A maximum of 20 values are allowed per Product. Each value must be a UTF-8
   * encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [size](https://support.google.com/merchants/answer/6324492),
   * [size_type](https://support.google.com/merchants/answer/6324497), and
   * [size_system](https://support.google.com/merchants/answer/6324502).
   * Schema.org property [Product.size](https://schema.org/size).
   *
   * @var string[]
   */
  public $sizes;
  /**
   * Custom tags associated with the product. At most 250 values are allowed per
   * Product. This value must be a UTF-8 encoded string with a length limit of
   * 1,000 characters. Otherwise, an INVALID_ARGUMENT error is returned. This
   * tag can be used for filtering recommendation results by passing the tag as
   * part of the PredictRequest.filter. Corresponding properties: Google
   * Merchant Center property
   * [custom_label_0–4](https://support.google.com/merchants/answer/6324473).
   *
   * @var string[]
   */
  public $tags;
  /**
   * Required. Product title. This field must be a UTF-8 encoded string with a
   * length limit of 1,000 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. Corresponding properties: Google Merchant Center property
   * [title](https://support.google.com/merchants/answer/6324415). Schema.org
   * property [Product.name](https://schema.org/name).
   *
   * @var string
   */
  public $title;
  /**
   * Input only. The TTL (time to live) of the product. Note that this is only
   * applicable to Type.PRIMARY and Type.COLLECTION, and ignored for
   * Type.VARIANT. In general, we suggest the users to delete the stale products
   * explicitly, instead of using this field to determine staleness. If it is
   * set, it must be a non-negative value, and expire_time is set as current
   * timestamp plus ttl. The derived expire_time is returned in the output and
   * ttl is left blank when retrieving the Product. If it is set, the product is
   * not available for SearchService.Search after current timestamp plus ttl.
   * However, the product can still be retrieved by ProductService.GetProduct
   * and ProductService.ListProducts.
   *
   * @var string
   */
  public $ttl;
  /**
   * Immutable. The type of the product. Default to
   * Catalog.product_level_config.ingestion_product_type if unset.
   *
   * @var string
   */
  public $type;
  /**
   * Canonical URL directly linking to the product detail page. It is strongly
   * recommended to provide a valid uri for the product, otherwise the service
   * performance could be significantly degraded. This field must be a UTF-8
   * encoded string with a length limit of 5,000 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [link](https://support.google.com/merchants/answer/6324416). Schema.org
   * property [Offer.url](https://schema.org/url).
   *
   * @var string
   */
  public $uri;
  protected $variantsType = GoogleCloudRetailV2Product::class;
  protected $variantsDataType = 'array';

  /**
   * Highly encouraged. Extra product attributes to be included. For example,
   * for products, this could include the store name, vendor, style, color, etc.
   * These are very strong signals for recommendation model, thus we highly
   * recommend providing the attributes here. Features that can take on one of a
   * limited number of possible values. Two types of features can be set are:
   * Textual features. some examples would be the brand/maker of a product, or
   * country of a customer. Numerical features. Some examples would be the
   * height/weight of a product, or age of a customer. For example: `{ "vendor":
   * {"text": ["vendor123", "vendor456"]}, "lengths_cm": {"numbers":[2.3,
   * 15.4]}, "heights_cm": {"numbers":[8.1, 6.4]} }`. This field needs to pass
   * all below criteria, otherwise an INVALID_ARGUMENT error is returned: * Max
   * entries count: 200. * The key must be a UTF-8 encoded string with a length
   * limit of 128 characters. * For indexable attribute, the key must match the
   * pattern: `a-zA-Z0-9*`. For example, `key0LikeThis` or `KEY_1_LIKE_THIS`. *
   * For text attributes, at most 400 values are allowed. Empty values are not
   * allowed. Each value must be a non-empty UTF-8 encoded string with a length
   * limit of 256 characters. * For number attributes, at most 400 values are
   * allowed.
   *
   * @param GoogleCloudRetailV2CustomAttribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudRetailV2CustomAttribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * The target group associated with a given audience (e.g. male, veterans, car
   * owners, musicians, etc.) of the product.
   *
   * @param GoogleCloudRetailV2Audience $audience
   */
  public function setAudience(GoogleCloudRetailV2Audience $audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return GoogleCloudRetailV2Audience
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * The online availability of the Product. Default to Availability.IN_STOCK.
   * For primary products with variants set the availability of the primary as
   * Availability.OUT_OF_STOCK and set the true availability at the variant
   * level. This way the primary product will be considered "in stock" as long
   * as it has at least one variant in stock. For primary products with no
   * variants set the true availability at the primary level. Corresponding
   * properties: Google Merchant Center property
   * [availability](https://support.google.com/merchants/answer/6324448).
   * Schema.org property [Offer.availability](https://schema.org/availability).
   *
   * Accepted values: AVAILABILITY_UNSPECIFIED, IN_STOCK, OUT_OF_STOCK,
   * PREORDER, BACKORDER
   *
   * @param self::AVAILABILITY_* $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return self::AVAILABILITY_*
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * The available quantity of the item.
   *
   * @param int $availableQuantity
   */
  public function setAvailableQuantity($availableQuantity)
  {
    $this->availableQuantity = $availableQuantity;
  }
  /**
   * @return int
   */
  public function getAvailableQuantity()
  {
    return $this->availableQuantity;
  }
  /**
   * The timestamp when this Product becomes available for SearchService.Search.
   * Note that this is only applicable to Type.PRIMARY and Type.COLLECTION, and
   * ignored for Type.VARIANT.
   *
   * @param string $availableTime
   */
  public function setAvailableTime($availableTime)
  {
    $this->availableTime = $availableTime;
  }
  /**
   * @return string
   */
  public function getAvailableTime()
  {
    return $this->availableTime;
  }
  /**
   * The brands of the product. A maximum of 30 brands are allowed unless
   * overridden through the Google Cloud console. Each brand must be a UTF-8
   * encoded string with a length limit of 1,000 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [brand](https://support.google.com/merchants/answer/6324351). Schema.org
   * property [Product.brand](https://schema.org/brand).
   *
   * @param string[] $brands
   */
  public function setBrands($brands)
  {
    $this->brands = $brands;
  }
  /**
   * @return string[]
   */
  public function getBrands()
  {
    return $this->brands;
  }
  /**
   * Product categories. This field is repeated for supporting one product
   * belonging to several parallel categories. Strongly recommended using the
   * full path for better search / recommendation quality. To represent full
   * path of category, use '>' sign to separate different hierarchies. If '>' is
   * part of the category name, replace it with other character(s). For example,
   * if a shoes product belongs to both ["Shoes & Accessories" -> "Shoes"] and
   * ["Sports & Fitness" -> "Athletic Clothing" -> "Shoes"], it could be
   * represented as: "categories": [ "Shoes & Accessories > Shoes", "Sports &
   * Fitness > Athletic Clothing > Shoes" ] Must be set for Type.PRIMARY Product
   * otherwise an INVALID_ARGUMENT error is returned. At most 250 values are
   * allowed per Product unless overridden through the Google Cloud console.
   * Empty values are not allowed. Each value must be a UTF-8 encoded string
   * with a length limit of 5,000 characters. Otherwise, an INVALID_ARGUMENT
   * error is returned. Corresponding properties: Google Merchant Center
   * property google_product_category. Schema.org property [Product.category]
   * (https://schema.org/category). [mc_google_product_category]:
   * https://support.google.com/merchants/answer/6324436
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * The id of the collection members when type is Type.COLLECTION. Non-existent
   * product ids are allowed. The type of the members must be either
   * Type.PRIMARY or Type.VARIANT otherwise an INVALID_ARGUMENT error is thrown.
   * Should not set it for other types. A maximum of 1000 values are allowed.
   * Otherwise, an INVALID_ARGUMENT error is return.
   *
   * @param string[] $collectionMemberIds
   */
  public function setCollectionMemberIds($collectionMemberIds)
  {
    $this->collectionMemberIds = $collectionMemberIds;
  }
  /**
   * @return string[]
   */
  public function getCollectionMemberIds()
  {
    return $this->collectionMemberIds;
  }
  /**
   * The color of the product. Corresponding properties: Google Merchant Center
   * property [color](https://support.google.com/merchants/answer/6324487).
   * Schema.org property [Product.color](https://schema.org/color).
   *
   * @param GoogleCloudRetailV2ColorInfo $colorInfo
   */
  public function setColorInfo(GoogleCloudRetailV2ColorInfo $colorInfo)
  {
    $this->colorInfo = $colorInfo;
  }
  /**
   * @return GoogleCloudRetailV2ColorInfo
   */
  public function getColorInfo()
  {
    return $this->colorInfo;
  }
  /**
   * The condition of the product. Strongly encouraged to use the standard
   * values: "new", "refurbished", "used". A maximum of 1 value is allowed per
   * Product. Each value must be a UTF-8 encoded string with a length limit of
   * 128 characters. Otherwise, an INVALID_ARGUMENT error is returned.
   * Corresponding properties: Google Merchant Center property
   * [condition](https://support.google.com/merchants/answer/6324469).
   * Schema.org property
   * [Offer.itemCondition](https://schema.org/itemCondition).
   *
   * @param string[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return string[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Product description. This field must be a UTF-8 encoded string with a
   * length limit of 5,000 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. Corresponding properties: Google Merchant Center property
   * [description](https://support.google.com/merchants/answer/6324468).
   * Schema.org property [Product.description](https://schema.org/description).
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
   * Note that this field is applied in the following ways: * If the Product is
   * already expired when it is uploaded, this product is not indexed for
   * search. * If the Product is not expired when it is uploaded, only the
   * Type.PRIMARY's and Type.COLLECTION's expireTime is respected, and
   * Type.VARIANT's expireTime is not used. In general, we suggest the users to
   * delete the stale products explicitly, instead of using this field to
   * determine staleness. expire_time must be later than available_time and
   * publish_time, otherwise an INVALID_ARGUMENT error is thrown. Corresponding
   * properties: Google Merchant Center property
   * [expiration_date](https://support.google.com/merchants/answer/6324499).
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Fulfillment information, such as the store IDs for in-store pickup or
   * region IDs for different shipping methods. All the elements must have
   * distinct FulfillmentInfo.type. Otherwise, an INVALID_ARGUMENT error is
   * returned.
   *
   * @param GoogleCloudRetailV2FulfillmentInfo[] $fulfillmentInfo
   */
  public function setFulfillmentInfo($fulfillmentInfo)
  {
    $this->fulfillmentInfo = $fulfillmentInfo;
  }
  /**
   * @return GoogleCloudRetailV2FulfillmentInfo[]
   */
  public function getFulfillmentInfo()
  {
    return $this->fulfillmentInfo;
  }
  /**
   * The Global Trade Item Number (GTIN) of the product. This field must be a
   * UTF-8 encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. This field must be a Unigram.
   * Otherwise, an INVALID_ARGUMENT error is returned. Corresponding properties:
   * Google Merchant Center property
   * [gtin](https://support.google.com/merchants/answer/6324461). Schema.org
   * property [Product.isbn](https://schema.org/isbn),
   * [Product.gtin8](https://schema.org/gtin8),
   * [Product.gtin12](https://schema.org/gtin12),
   * [Product.gtin13](https://schema.org/gtin13), or
   * [Product.gtin14](https://schema.org/gtin14). If the value is not a valid
   * GTIN, an INVALID_ARGUMENT error is returned.
   *
   * @param string $gtin
   */
  public function setGtin($gtin)
  {
    $this->gtin = $gtin;
  }
  /**
   * @return string
   */
  public function getGtin()
  {
    return $this->gtin;
  }
  /**
   * Immutable. Product identifier, which is the final component of name. For
   * example, this field is "id_1", if name is `projects/locations/global/catalo
   * gs/default_catalog/branches/default_branch/products/id_1`. This field must
   * be a UTF-8 encoded string with a length limit of 128 characters. Otherwise,
   * an INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [id](https://support.google.com/merchants/answer/6324405). Schema.org
   * property [Product.sku](https://schema.org/sku).
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
   * Product images for the product. We highly recommend putting the main image
   * first. A maximum of 300 images are allowed. Corresponding properties:
   * Google Merchant Center property
   * [image_link](https://support.google.com/merchants/answer/6324350).
   * Schema.org property [Product.image](https://schema.org/image).
   *
   * @param GoogleCloudRetailV2Image[] $images
   */
  public function setImages($images)
  {
    $this->images = $images;
  }
  /**
   * @return GoogleCloudRetailV2Image[]
   */
  public function getImages()
  {
    return $this->images;
  }
  /**
   * Language of the title/description and other string attributes. Use language
   * tags defined by [BCP 47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). For
   * product prediction, this field is ignored and the model automatically
   * detects the text language. The Product can include text in different
   * languages, but duplicating Products to provide text in multiple languages
   * can result in degraded model performance. For product search this field is
   * in use. It defaults to "en-US" if unset.
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
   * Output only. A list of local inventories specific to different places. This
   * field can be managed by ProductService.AddLocalInventories and
   * ProductService.RemoveLocalInventories APIs if fine-grained, high-volume
   * updates are necessary.
   *
   * @param GoogleCloudRetailV2LocalInventory[] $localInventories
   */
  public function setLocalInventories($localInventories)
  {
    $this->localInventories = $localInventories;
  }
  /**
   * @return GoogleCloudRetailV2LocalInventory[]
   */
  public function getLocalInventories()
  {
    return $this->localInventories;
  }
  /**
   * The material of the product. For example, "leather", "wooden". A maximum of
   * 20 values are allowed. Each value must be a UTF-8 encoded string with a
   * length limit of 200 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. Corresponding properties: Google Merchant Center property
   * [material](https://support.google.com/merchants/answer/6324410). Schema.org
   * property [Product.material](https://schema.org/material).
   *
   * @param string[] $materials
   */
  public function setMaterials($materials)
  {
    $this->materials = $materials;
  }
  /**
   * @return string[]
   */
  public function getMaterials()
  {
    return $this->materials;
  }
  /**
   * Immutable. Full resource name of the product, such as `projects/locations/g
   * lobal/catalogs/default_catalog/branches/default_branch/products/product_id`
   * .
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
   * The pattern or graphic print of the product. For example, "striped", "polka
   * dot", "paisley". A maximum of 20 values are allowed per Product. Each value
   * must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an INVALID_ARGUMENT error is returned. Corresponding properties:
   * Google Merchant Center property
   * [pattern](https://support.google.com/merchants/answer/6324483). Schema.org
   * property [Product.pattern](https://schema.org/pattern).
   *
   * @param string[] $patterns
   */
  public function setPatterns($patterns)
  {
    $this->patterns = $patterns;
  }
  /**
   * @return string[]
   */
  public function getPatterns()
  {
    return $this->patterns;
  }
  /**
   * Product price and cost information. Corresponding properties: Google
   * Merchant Center property
   * [price](https://support.google.com/merchants/answer/6324371).
   *
   * @param GoogleCloudRetailV2PriceInfo $priceInfo
   */
  public function setPriceInfo(GoogleCloudRetailV2PriceInfo $priceInfo)
  {
    $this->priceInfo = $priceInfo;
  }
  /**
   * @return GoogleCloudRetailV2PriceInfo
   */
  public function getPriceInfo()
  {
    return $this->priceInfo;
  }
  /**
   * Variant group identifier. Must be an id, with the same parent branch with
   * this product. Otherwise, an error is thrown. For Type.PRIMARY Products,
   * this field can only be empty or set to the same value as id. For VARIANT
   * Products, this field cannot be empty. A maximum of 2,000 products are
   * allowed to share the same Type.PRIMARY Product. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [item_group_id](https://support.google.com/merchants/answer/6324507).
   * Schema.org property
   * [Product.inProductGroupWithID](https://schema.org/inProductGroupWithID).
   *
   * @param string $primaryProductId
   */
  public function setPrimaryProductId($primaryProductId)
  {
    $this->primaryProductId = $primaryProductId;
  }
  /**
   * @return string
   */
  public function getPrimaryProductId()
  {
    return $this->primaryProductId;
  }
  /**
   * The promotions applied to the product. A maximum of 10 values are allowed
   * per Product. Only Promotion.promotion_id will be used, other fields will be
   * ignored if set.
   *
   * @param GoogleCloudRetailV2Promotion[] $promotions
   */
  public function setPromotions($promotions)
  {
    $this->promotions = $promotions;
  }
  /**
   * @return GoogleCloudRetailV2Promotion[]
   */
  public function getPromotions()
  {
    return $this->promotions;
  }
  /**
   * The timestamp when the product is published by the retailer for the first
   * time, which indicates the freshness of the products. Note that this field
   * is different from available_time, given it purely describes product
   * freshness regardless of when it is available on search and recommendation.
   *
   * @param string $publishTime
   */
  public function setPublishTime($publishTime)
  {
    $this->publishTime = $publishTime;
  }
  /**
   * @return string
   */
  public function getPublishTime()
  {
    return $this->publishTime;
  }
  /**
   * The rating of this product.
   *
   * @param GoogleCloudRetailV2Rating $rating
   */
  public function setRating(GoogleCloudRetailV2Rating $rating)
  {
    $this->rating = $rating;
  }
  /**
   * @return GoogleCloudRetailV2Rating
   */
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * Indicates which fields in the Products are returned in SearchResponse.
   * Supported fields for all types: * audience * availability * brands *
   * color_info * conditions * gtin * materials * name * patterns * price_info *
   * rating * sizes * title * uri Supported fields only for Type.PRIMARY and
   * Type.COLLECTION: * categories * description * images Supported fields only
   * for Type.VARIANT: * Only the first image in images To mark attributes as
   * retrievable, include paths of the form "attributes.key" where "key" is the
   * key of a custom attribute, as specified in attributes. For Type.PRIMARY and
   * Type.COLLECTION, the following fields are always returned in SearchResponse
   * by default: * name For Type.VARIANT, the following fields are always
   * returned in by default: * name * color_info Note: Returning more fields in
   * SearchResponse can increase response payload size and serving latency. This
   * field is deprecated. Use the retrievable site-wide control instead.
   *
   * @deprecated
   * @param string $retrievableFields
   */
  public function setRetrievableFields($retrievableFields)
  {
    $this->retrievableFields = $retrievableFields;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getRetrievableFields()
  {
    return $this->retrievableFields;
  }
  /**
   * The size of the product. To represent different size systems or size types,
   * consider using this format: [[[size_system:]size_type:]size_value]. For
   * example, in "US:MENS:M", "US" represents size system; "MENS" represents
   * size type; "M" represents size value. In "GIRLS:27", size system is empty;
   * "GIRLS" represents size type; "27" represents size value. In "32 inches",
   * both size system and size type are empty, while size value is "32 inches".
   * A maximum of 20 values are allowed per Product. Each value must be a UTF-8
   * encoded string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [size](https://support.google.com/merchants/answer/6324492),
   * [size_type](https://support.google.com/merchants/answer/6324497), and
   * [size_system](https://support.google.com/merchants/answer/6324502).
   * Schema.org property [Product.size](https://schema.org/size).
   *
   * @param string[] $sizes
   */
  public function setSizes($sizes)
  {
    $this->sizes = $sizes;
  }
  /**
   * @return string[]
   */
  public function getSizes()
  {
    return $this->sizes;
  }
  /**
   * Custom tags associated with the product. At most 250 values are allowed per
   * Product. This value must be a UTF-8 encoded string with a length limit of
   * 1,000 characters. Otherwise, an INVALID_ARGUMENT error is returned. This
   * tag can be used for filtering recommendation results by passing the tag as
   * part of the PredictRequest.filter. Corresponding properties: Google
   * Merchant Center property
   * [custom_label_0–4](https://support.google.com/merchants/answer/6324473).
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
   * Required. Product title. This field must be a UTF-8 encoded string with a
   * length limit of 1,000 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned. Corresponding properties: Google Merchant Center property
   * [title](https://support.google.com/merchants/answer/6324415). Schema.org
   * property [Product.name](https://schema.org/name).
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
  /**
   * Input only. The TTL (time to live) of the product. Note that this is only
   * applicable to Type.PRIMARY and Type.COLLECTION, and ignored for
   * Type.VARIANT. In general, we suggest the users to delete the stale products
   * explicitly, instead of using this field to determine staleness. If it is
   * set, it must be a non-negative value, and expire_time is set as current
   * timestamp plus ttl. The derived expire_time is returned in the output and
   * ttl is left blank when retrieving the Product. If it is set, the product is
   * not available for SearchService.Search after current timestamp plus ttl.
   * However, the product can still be retrieved by ProductService.GetProduct
   * and ProductService.ListProducts.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Immutable. The type of the product. Default to
   * Catalog.product_level_config.ingestion_product_type if unset.
   *
   * Accepted values: TYPE_UNSPECIFIED, PRIMARY, VARIANT, COLLECTION
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
  /**
   * Canonical URL directly linking to the product detail page. It is strongly
   * recommended to provide a valid uri for the product, otherwise the service
   * performance could be significantly degraded. This field must be a UTF-8
   * encoded string with a length limit of 5,000 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned. Corresponding properties: Google
   * Merchant Center property
   * [link](https://support.google.com/merchants/answer/6324416). Schema.org
   * property [Offer.url](https://schema.org/url).
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * Output only. Product variants grouped together on primary product which
   * share similar product attributes. It's automatically grouped by
   * primary_product_id for all the product variants. Only populated for
   * Type.PRIMARY Products. Note: This field is OUTPUT_ONLY for
   * ProductService.GetProduct. Do not set this field in API requests.
   *
   * @param GoogleCloudRetailV2Product[] $variants
   */
  public function setVariants($variants)
  {
    $this->variants = $variants;
  }
  /**
   * @return GoogleCloudRetailV2Product[]
   */
  public function getVariants()
  {
    return $this->variants;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Product::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Product');
