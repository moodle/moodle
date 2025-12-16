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

namespace Google\Service\ShoppingContent;

class Collection extends \Google\Collection
{
  protected $collection_key = 'imageLink';
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns. [Custom
   * label](https://support.google.com/merchants/answer/9674217)
   *
   * @var string
   */
  public $customLabel0;
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @var string
   */
  public $customLabel1;
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @var string
   */
  public $customLabel2;
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @var string
   */
  public $customLabel3;
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @var string
   */
  public $customLabel4;
  protected $featuredProductType = CollectionFeaturedProduct::class;
  protected $featuredProductDataType = 'array';
  /**
   * Your collection's name. [headline
   * attribute](https://support.google.com/merchants/answer/9673580)
   *
   * @var string[]
   */
  public $headline;
  /**
   * Required. The REST ID of the collection. Content API methods that operate
   * on collections take this as their collectionId parameter. The REST ID for a
   * collection is of the form collectionId. [id
   * attribute](https://support.google.com/merchants/answer/9649290)
   *
   * @var string
   */
  public $id;
  /**
   * The URL of a collection’s image. [image_link
   * attribute](https://support.google.com/merchants/answer/9703236)
   *
   * @var string[]
   */
  public $imageLink;
  /**
   * The language of a collection and the language of any featured products
   * linked to the collection. [language
   * attribute](https://support.google.com/merchants/answer/9673781)
   *
   * @var string
   */
  public $language;
  /**
   * A collection’s landing page. URL directly linking to your collection's page
   * on your website. [link
   * attribute](https://support.google.com/merchants/answer/9673983)
   *
   * @var string
   */
  public $link;
  /**
   * A collection’s mobile-optimized landing page when you have a different URL
   * for mobile and desktop traffic. [mobile_link
   * attribute](https://support.google.com/merchants/answer/9646123)
   *
   * @var string
   */
  public $mobileLink;
  /**
   * [product_country
   * attribute](https://support.google.com/merchants/answer/9674155)
   *
   * @var string
   */
  public $productCountry;

  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns. [Custom
   * label](https://support.google.com/merchants/answer/9674217)
   *
   * @param string $customLabel0
   */
  public function setCustomLabel0($customLabel0)
  {
    $this->customLabel0 = $customLabel0;
  }
  /**
   * @return string
   */
  public function getCustomLabel0()
  {
    return $this->customLabel0;
  }
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @param string $customLabel1
   */
  public function setCustomLabel1($customLabel1)
  {
    $this->customLabel1 = $customLabel1;
  }
  /**
   * @return string
   */
  public function getCustomLabel1()
  {
    return $this->customLabel1;
  }
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @param string $customLabel2
   */
  public function setCustomLabel2($customLabel2)
  {
    $this->customLabel2 = $customLabel2;
  }
  /**
   * @return string
   */
  public function getCustomLabel2()
  {
    return $this->customLabel2;
  }
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @param string $customLabel3
   */
  public function setCustomLabel3($customLabel3)
  {
    $this->customLabel3 = $customLabel3;
  }
  /**
   * @return string
   */
  public function getCustomLabel3()
  {
    return $this->customLabel3;
  }
  /**
   * Label that you assign to a collection to help organize bidding and
   * reporting in Shopping campaigns.
   *
   * @param string $customLabel4
   */
  public function setCustomLabel4($customLabel4)
  {
    $this->customLabel4 = $customLabel4;
  }
  /**
   * @return string
   */
  public function getCustomLabel4()
  {
    return $this->customLabel4;
  }
  /**
   * This identifies one or more products associated with the collection. Used
   * as a lookup to the corresponding product ID in your product feeds. Provide
   * a maximum of 100 featuredProduct (for collections). Provide up to 10
   * featuredProduct (for Shoppable Images only) with ID and X and Y
   * coordinates. [featured_product
   * attribute](https://support.google.com/merchants/answer/9703736)
   *
   * @param CollectionFeaturedProduct[] $featuredProduct
   */
  public function setFeaturedProduct($featuredProduct)
  {
    $this->featuredProduct = $featuredProduct;
  }
  /**
   * @return CollectionFeaturedProduct[]
   */
  public function getFeaturedProduct()
  {
    return $this->featuredProduct;
  }
  /**
   * Your collection's name. [headline
   * attribute](https://support.google.com/merchants/answer/9673580)
   *
   * @param string[] $headline
   */
  public function setHeadline($headline)
  {
    $this->headline = $headline;
  }
  /**
   * @return string[]
   */
  public function getHeadline()
  {
    return $this->headline;
  }
  /**
   * Required. The REST ID of the collection. Content API methods that operate
   * on collections take this as their collectionId parameter. The REST ID for a
   * collection is of the form collectionId. [id
   * attribute](https://support.google.com/merchants/answer/9649290)
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
   * The URL of a collection’s image. [image_link
   * attribute](https://support.google.com/merchants/answer/9703236)
   *
   * @param string[] $imageLink
   */
  public function setImageLink($imageLink)
  {
    $this->imageLink = $imageLink;
  }
  /**
   * @return string[]
   */
  public function getImageLink()
  {
    return $this->imageLink;
  }
  /**
   * The language of a collection and the language of any featured products
   * linked to the collection. [language
   * attribute](https://support.google.com/merchants/answer/9673781)
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * A collection’s landing page. URL directly linking to your collection's page
   * on your website. [link
   * attribute](https://support.google.com/merchants/answer/9673983)
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * A collection’s mobile-optimized landing page when you have a different URL
   * for mobile and desktop traffic. [mobile_link
   * attribute](https://support.google.com/merchants/answer/9646123)
   *
   * @param string $mobileLink
   */
  public function setMobileLink($mobileLink)
  {
    $this->mobileLink = $mobileLink;
  }
  /**
   * @return string
   */
  public function getMobileLink()
  {
    return $this->mobileLink;
  }
  /**
   * [product_country
   * attribute](https://support.google.com/merchants/answer/9674155)
   *
   * @param string $productCountry
   */
  public function setProductCountry($productCountry)
  {
    $this->productCountry = $productCountry;
  }
  /**
   * @return string
   */
  public function getProductCountry()
  {
    return $this->productCountry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Collection::class, 'Google_Service_ShoppingContent_Collection');
