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

class Attributes extends \Google\Collection
{
  protected $collection_key = 'videoLink';
  protected $additionalImageLinkType = Image::class;
  protected $additionalImageLinkDataType = 'array';
  /**
   * The target age group of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#agegroup.
   *
   * @var string
   */
  public $ageGroup;
  /**
   * The brand name of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#brand.
   *
   * @var string
   */
  public $brand;
  protected $capacityType = Capacity::class;
  protected $capacityDataType = '';
  protected $certificationType = GoogleShoppingManufacturersV1ProductCertification::class;
  protected $certificationDataType = 'array';
  /**
   * The color of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#color.
   *
   * @var string
   */
  public $color;
  protected $countType = Count::class;
  protected $countDataType = '';
  /**
   * The description of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#description.
   *
   * @var string
   */
  public $description;
  /**
   * The disclosure date of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#disclosure.
   *
   * @var string
   */
  public $disclosureDate;
  /**
   * A list of excluded destinations such as "ClientExport",
   * "ClientShoppingCatalog" or "PartnerShoppingCatalog". For more information,
   * see https://support.google.com/manufacturers/answer/7443550
   *
   * @var string[]
   */
  public $excludedDestination;
  protected $featureDescriptionType = FeatureDescription::class;
  protected $featureDescriptionDataType = 'array';
  /**
   * The flavor of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#flavor.
   *
   * @var string
   */
  public $flavor;
  /**
   * The format of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#format.
   *
   * @var string
   */
  public $format;
  /**
   * The target gender of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#gender.
   *
   * @var string
   */
  public $gender;
  protected $groceryType = Grocery::class;
  protected $groceryDataType = '';
  /**
   * The Global Trade Item Number (GTIN) of the product. For more information,
   * see https://support.google.com/manufacturers/answer/6124116#gtin.
   *
   * @var string[]
   */
  public $gtin;
  protected $imageLinkType = Image::class;
  protected $imageLinkDataType = '';
  /**
   * A list of included destinations such as "ClientExport",
   * "ClientShoppingCatalog" or "PartnerShoppingCatalog". For more information,
   * see https://support.google.com/manufacturers/answer/7443550
   *
   * @var string[]
   */
  public $includedDestination;
  /**
   * Optional. List of countries to show this product in. Countries provided in
   * this attribute will override any of the countries configured at feed level.
   * The values should be: the [CLDR territory
   * code](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) of
   * the countries in which this item will be shown.
   *
   * @var string[]
   */
  public $intendedCountry;
  /**
   * The item group id of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#itemgroupid.
   *
   * @var string
   */
  public $itemGroupId;
  /**
   * The material of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#material.
   *
   * @var string
   */
  public $material;
  /**
   * The Manufacturer Part Number (MPN) of the product. For more information,
   * see https://support.google.com/manufacturers/answer/6124116#mpn.
   *
   * @var string
   */
  public $mpn;
  protected $nutritionType = Nutrition::class;
  protected $nutritionDataType = '';
  /**
   * The pattern of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#pattern.
   *
   * @var string
   */
  public $pattern;
  protected $productDetailType = ProductDetail::class;
  protected $productDetailDataType = 'array';
  /**
   * The product highlights. For more information, see
   * https://support.google.com/manufacturers/answer/10066942
   *
   * @var string[]
   */
  public $productHighlight;
  /**
   * The name of the group of products related to the product. For more
   * information, see
   * https://support.google.com/manufacturers/answer/6124116#productline.
   *
   * @var string
   */
  public $productLine;
  /**
   * The canonical name of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#productname.
   *
   * @var string
   */
  public $productName;
  /**
   * The URL of the detail page of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#productpage.
   *
   * @var string
   */
  public $productPageUrl;
  /**
   * The type or category of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#producttype.
   *
   * @var string[]
   */
  public $productType;
  /**
   * The release date of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#release.
   *
   * @var string
   */
  public $releaseDate;
  /**
   * Rich product content. For more information, see
   * https://support.google.com/manufacturers/answer/9389865
   *
   * @var string[]
   */
  public $richProductContent;
  /**
   * The scent of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#scent.
   *
   * @var string
   */
  public $scent;
  /**
   * The size of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#size.
   *
   * @var string
   */
  public $size;
  /**
   * The size system of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#sizesystem.
   *
   * @var string
   */
  public $sizeSystem;
  /**
   * The size type of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#sizetype.
   *
   * @var string[]
   */
  public $sizeType;
  protected $suggestedRetailPriceType = Price::class;
  protected $suggestedRetailPriceDataType = '';
  /**
   * The target client id. Should only be used in the accounts of the data
   * partners. For more information, see
   * https://support.google.com/manufacturers/answer/10857344
   *
   * @var string
   */
  public $targetClientId;
  /**
   * The theme of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#theme.
   *
   * @var string
   */
  public $theme;
  /**
   * The title of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#title.
   *
   * @var string
   */
  public $title;
  /**
   * The videos of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#video.
   *
   * @var string[]
   */
  public $videoLink;
  /**
   * Virtual Model (3d) asset link.
   *
   * @var string
   */
  public $virtualModelLink;

  /**
   * The additional images of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#addlimage.
   *
   * @param Image[] $additionalImageLink
   */
  public function setAdditionalImageLink($additionalImageLink)
  {
    $this->additionalImageLink = $additionalImageLink;
  }
  /**
   * @return Image[]
   */
  public function getAdditionalImageLink()
  {
    return $this->additionalImageLink;
  }
  /**
   * The target age group of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#agegroup.
   *
   * @param string $ageGroup
   */
  public function setAgeGroup($ageGroup)
  {
    $this->ageGroup = $ageGroup;
  }
  /**
   * @return string
   */
  public function getAgeGroup()
  {
    return $this->ageGroup;
  }
  /**
   * The brand name of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#brand.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * The capacity of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#capacity.
   *
   * @param Capacity $capacity
   */
  public function setCapacity(Capacity $capacity)
  {
    $this->capacity = $capacity;
  }
  /**
   * @return Capacity
   */
  public function getCapacity()
  {
    return $this->capacity;
  }
  /**
   * Optional. List of certifications claimed by this product.
   *
   * @param GoogleShoppingManufacturersV1ProductCertification[] $certification
   */
  public function setCertification($certification)
  {
    $this->certification = $certification;
  }
  /**
   * @return GoogleShoppingManufacturersV1ProductCertification[]
   */
  public function getCertification()
  {
    return $this->certification;
  }
  /**
   * The color of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#color.
   *
   * @param string $color
   */
  public function setColor($color)
  {
    $this->color = $color;
  }
  /**
   * @return string
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The count of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#count.
   *
   * @param Count $count
   */
  public function setCount(Count $count)
  {
    $this->count = $count;
  }
  /**
   * @return Count
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The description of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#description.
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
   * The disclosure date of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#disclosure.
   *
   * @param string $disclosureDate
   */
  public function setDisclosureDate($disclosureDate)
  {
    $this->disclosureDate = $disclosureDate;
  }
  /**
   * @return string
   */
  public function getDisclosureDate()
  {
    return $this->disclosureDate;
  }
  /**
   * A list of excluded destinations such as "ClientExport",
   * "ClientShoppingCatalog" or "PartnerShoppingCatalog". For more information,
   * see https://support.google.com/manufacturers/answer/7443550
   *
   * @param string[] $excludedDestination
   */
  public function setExcludedDestination($excludedDestination)
  {
    $this->excludedDestination = $excludedDestination;
  }
  /**
   * @return string[]
   */
  public function getExcludedDestination()
  {
    return $this->excludedDestination;
  }
  /**
   * The rich format description of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#featuredesc.
   *
   * @param FeatureDescription[] $featureDescription
   */
  public function setFeatureDescription($featureDescription)
  {
    $this->featureDescription = $featureDescription;
  }
  /**
   * @return FeatureDescription[]
   */
  public function getFeatureDescription()
  {
    return $this->featureDescription;
  }
  /**
   * The flavor of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#flavor.
   *
   * @param string $flavor
   */
  public function setFlavor($flavor)
  {
    $this->flavor = $flavor;
  }
  /**
   * @return string
   */
  public function getFlavor()
  {
    return $this->flavor;
  }
  /**
   * The format of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#format.
   *
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * The target gender of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#gender.
   *
   * @param string $gender
   */
  public function setGender($gender)
  {
    $this->gender = $gender;
  }
  /**
   * @return string
   */
  public function getGender()
  {
    return $this->gender;
  }
  /**
   * Grocery Attributes. See more at
   * https://support.google.com/manufacturers/answer/12098458#grocery.
   *
   * @param Grocery $grocery
   */
  public function setGrocery(Grocery $grocery)
  {
    $this->grocery = $grocery;
  }
  /**
   * @return Grocery
   */
  public function getGrocery()
  {
    return $this->grocery;
  }
  /**
   * The Global Trade Item Number (GTIN) of the product. For more information,
   * see https://support.google.com/manufacturers/answer/6124116#gtin.
   *
   * @param string[] $gtin
   */
  public function setGtin($gtin)
  {
    $this->gtin = $gtin;
  }
  /**
   * @return string[]
   */
  public function getGtin()
  {
    return $this->gtin;
  }
  /**
   * The image of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#image.
   *
   * @param Image $imageLink
   */
  public function setImageLink(Image $imageLink)
  {
    $this->imageLink = $imageLink;
  }
  /**
   * @return Image
   */
  public function getImageLink()
  {
    return $this->imageLink;
  }
  /**
   * A list of included destinations such as "ClientExport",
   * "ClientShoppingCatalog" or "PartnerShoppingCatalog". For more information,
   * see https://support.google.com/manufacturers/answer/7443550
   *
   * @param string[] $includedDestination
   */
  public function setIncludedDestination($includedDestination)
  {
    $this->includedDestination = $includedDestination;
  }
  /**
   * @return string[]
   */
  public function getIncludedDestination()
  {
    return $this->includedDestination;
  }
  /**
   * Optional. List of countries to show this product in. Countries provided in
   * this attribute will override any of the countries configured at feed level.
   * The values should be: the [CLDR territory
   * code](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) of
   * the countries in which this item will be shown.
   *
   * @param string[] $intendedCountry
   */
  public function setIntendedCountry($intendedCountry)
  {
    $this->intendedCountry = $intendedCountry;
  }
  /**
   * @return string[]
   */
  public function getIntendedCountry()
  {
    return $this->intendedCountry;
  }
  /**
   * The item group id of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#itemgroupid.
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
   * The material of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#material.
   *
   * @param string $material
   */
  public function setMaterial($material)
  {
    $this->material = $material;
  }
  /**
   * @return string
   */
  public function getMaterial()
  {
    return $this->material;
  }
  /**
   * The Manufacturer Part Number (MPN) of the product. For more information,
   * see https://support.google.com/manufacturers/answer/6124116#mpn.
   *
   * @param string $mpn
   */
  public function setMpn($mpn)
  {
    $this->mpn = $mpn;
  }
  /**
   * @return string
   */
  public function getMpn()
  {
    return $this->mpn;
  }
  /**
   * Nutrition Attributes. See more at
   * https://support.google.com/manufacturers/answer/12098458#food-servings.
   *
   * @param Nutrition $nutrition
   */
  public function setNutrition(Nutrition $nutrition)
  {
    $this->nutrition = $nutrition;
  }
  /**
   * @return Nutrition
   */
  public function getNutrition()
  {
    return $this->nutrition;
  }
  /**
   * The pattern of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#pattern.
   *
   * @param string $pattern
   */
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  /**
   * @return string
   */
  public function getPattern()
  {
    return $this->pattern;
  }
  /**
   * The details of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#productdetail.
   *
   * @param ProductDetail[] $productDetail
   */
  public function setProductDetail($productDetail)
  {
    $this->productDetail = $productDetail;
  }
  /**
   * @return ProductDetail[]
   */
  public function getProductDetail()
  {
    return $this->productDetail;
  }
  /**
   * The product highlights. For more information, see
   * https://support.google.com/manufacturers/answer/10066942
   *
   * @param string[] $productHighlight
   */
  public function setProductHighlight($productHighlight)
  {
    $this->productHighlight = $productHighlight;
  }
  /**
   * @return string[]
   */
  public function getProductHighlight()
  {
    return $this->productHighlight;
  }
  /**
   * The name of the group of products related to the product. For more
   * information, see
   * https://support.google.com/manufacturers/answer/6124116#productline.
   *
   * @param string $productLine
   */
  public function setProductLine($productLine)
  {
    $this->productLine = $productLine;
  }
  /**
   * @return string
   */
  public function getProductLine()
  {
    return $this->productLine;
  }
  /**
   * The canonical name of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#productname.
   *
   * @param string $productName
   */
  public function setProductName($productName)
  {
    $this->productName = $productName;
  }
  /**
   * @return string
   */
  public function getProductName()
  {
    return $this->productName;
  }
  /**
   * The URL of the detail page of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#productpage.
   *
   * @param string $productPageUrl
   */
  public function setProductPageUrl($productPageUrl)
  {
    $this->productPageUrl = $productPageUrl;
  }
  /**
   * @return string
   */
  public function getProductPageUrl()
  {
    return $this->productPageUrl;
  }
  /**
   * The type or category of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#producttype.
   *
   * @param string[] $productType
   */
  public function setProductType($productType)
  {
    $this->productType = $productType;
  }
  /**
   * @return string[]
   */
  public function getProductType()
  {
    return $this->productType;
  }
  /**
   * The release date of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#release.
   *
   * @param string $releaseDate
   */
  public function setReleaseDate($releaseDate)
  {
    $this->releaseDate = $releaseDate;
  }
  /**
   * @return string
   */
  public function getReleaseDate()
  {
    return $this->releaseDate;
  }
  /**
   * Rich product content. For more information, see
   * https://support.google.com/manufacturers/answer/9389865
   *
   * @param string[] $richProductContent
   */
  public function setRichProductContent($richProductContent)
  {
    $this->richProductContent = $richProductContent;
  }
  /**
   * @return string[]
   */
  public function getRichProductContent()
  {
    return $this->richProductContent;
  }
  /**
   * The scent of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#scent.
   *
   * @param string $scent
   */
  public function setScent($scent)
  {
    $this->scent = $scent;
  }
  /**
   * @return string
   */
  public function getScent()
  {
    return $this->scent;
  }
  /**
   * The size of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#size.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * The size system of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#sizesystem.
   *
   * @param string $sizeSystem
   */
  public function setSizeSystem($sizeSystem)
  {
    $this->sizeSystem = $sizeSystem;
  }
  /**
   * @return string
   */
  public function getSizeSystem()
  {
    return $this->sizeSystem;
  }
  /**
   * The size type of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#sizetype.
   *
   * @param string[] $sizeType
   */
  public function setSizeType($sizeType)
  {
    $this->sizeType = $sizeType;
  }
  /**
   * @return string[]
   */
  public function getSizeType()
  {
    return $this->sizeType;
  }
  /**
   * The suggested retail price (MSRP) of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#price.
   *
   * @param Price $suggestedRetailPrice
   */
  public function setSuggestedRetailPrice(Price $suggestedRetailPrice)
  {
    $this->suggestedRetailPrice = $suggestedRetailPrice;
  }
  /**
   * @return Price
   */
  public function getSuggestedRetailPrice()
  {
    return $this->suggestedRetailPrice;
  }
  /**
   * The target client id. Should only be used in the accounts of the data
   * partners. For more information, see
   * https://support.google.com/manufacturers/answer/10857344
   *
   * @param string $targetClientId
   */
  public function setTargetClientId($targetClientId)
  {
    $this->targetClientId = $targetClientId;
  }
  /**
   * @return string
   */
  public function getTargetClientId()
  {
    return $this->targetClientId;
  }
  /**
   * The theme of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#theme.
   *
   * @param string $theme
   */
  public function setTheme($theme)
  {
    $this->theme = $theme;
  }
  /**
   * @return string
   */
  public function getTheme()
  {
    return $this->theme;
  }
  /**
   * The title of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#title.
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
   * The videos of the product. For more information, see
   * https://support.google.com/manufacturers/answer/6124116#video.
   *
   * @param string[] $videoLink
   */
  public function setVideoLink($videoLink)
  {
    $this->videoLink = $videoLink;
  }
  /**
   * @return string[]
   */
  public function getVideoLink()
  {
    return $this->videoLink;
  }
  /**
   * Virtual Model (3d) asset link.
   *
   * @param string $virtualModelLink
   */
  public function setVirtualModelLink($virtualModelLink)
  {
    $this->virtualModelLink = $virtualModelLink;
  }
  /**
   * @return string
   */
  public function getVirtualModelLink()
  {
    return $this->virtualModelLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attributes::class, 'Google_Service_ManufacturerCenter_Attributes');
