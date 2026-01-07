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

namespace Google\Service\ManufacturerCenter;

class Product extends \Google\Collection
{
  protected $collection_key = 'issues';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * The content language of the product as a two-letter ISO 639-1 language code
   * (for example, en).
   *
   * @var string
   */
  public $contentLanguage;
  protected $destinationStatusesType = DestinationStatus::class;
  protected $destinationStatusesDataType = 'array';
  /**
   * Optional. The feed label for the product.
   *
   * @var string
   */
  public $feedLabel;
  protected $issuesType = Issue::class;
  protected $issuesDataType = 'array';
  /**
   * Name in the format `{target_country}:{content_language}:{product_id}`.
   * `target_country` - The target country of the product as a CLDR territory
   * code (for example, US). `content_language` - The content language of the
   * product as a two-letter ISO 639-1 language code (for example, en).
   * `product_id` - The ID of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#id.
   *
   * @var string
   */
  public $name;
  /**
   * Parent ID in the format `accounts/{account_id}`. `account_id` - The ID of
   * the Manufacturer Center account.
   *
   * @var string
   */
  public $parent;
  /**
   * The ID of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#id.
   *
   * @var string
   */
  public $productId;
  /**
   * The target country of the product as a CLDR territory code (for example,
   * US).
   *
   * @var string
   */
  public $targetCountry;

  /**
   * Attributes of the product uploaded to the Manufacturer Center. Manually
   * edited attributes are taken into account.
   *
   * @param Attributes $attributes
   */
  public function setAttributes(Attributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return Attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * The content language of the product as a two-letter ISO 639-1 language code
   * (for example, en).
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * The status of the destinations.
   *
   * @param DestinationStatus[] $destinationStatuses
   */
  public function setDestinationStatuses($destinationStatuses)
  {
    $this->destinationStatuses = $destinationStatuses;
  }
  /**
   * @return DestinationStatus[]
   */
  public function getDestinationStatuses()
  {
    return $this->destinationStatuses;
  }
  /**
   * Optional. The feed label for the product.
   *
   * @param string $feedLabel
   */
  public function setFeedLabel($feedLabel)
  {
    $this->feedLabel = $feedLabel;
  }
  /**
   * @return string
   */
  public function getFeedLabel()
  {
    return $this->feedLabel;
  }
  /**
   * A server-generated list of issues associated with the product.
   *
   * @param Issue[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return Issue[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Name in the format `{target_country}:{content_language}:{product_id}`.
   * `target_country` - The target country of the product as a CLDR territory
   * code (for example, US). `content_language` - The content language of the
   * product as a two-letter ISO 639-1 language code (for example, en).
   * `product_id` - The ID of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#id.
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
   * Parent ID in the format `accounts/{account_id}`. `account_id` - The ID of
   * the Manufacturer Center account.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * The ID of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#id.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The target country of the product as a CLDR territory code (for example,
   * US).
   *
   * @param string $targetCountry
   */
  public function setTargetCountry($targetCountry)
  {
    $this->targetCountry = $targetCountry;
  }
  /**
   * @return string
   */
  public function getTargetCountry()
  {
    return $this->targetCountry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Product::class, 'Google_Service_ManufacturerCenter_Product');
