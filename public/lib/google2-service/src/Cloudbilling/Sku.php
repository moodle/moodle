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

namespace Google\Service\Cloudbilling;

class Sku extends \Google\Collection
{
  protected $collection_key = 'serviceRegions';
  protected $categoryType = Category::class;
  protected $categoryDataType = '';
  /**
   * A human readable description of the SKU, has a maximum length of 256
   * characters.
   *
   * @var string
   */
  public $description;
  protected $geoTaxonomyType = GeoTaxonomy::class;
  protected $geoTaxonomyDataType = '';
  /**
   * The resource name for the SKU. Example:
   * "services/6F81-5844-456A/skus/D041-B8A1-6E0B"
   *
   * @var string
   */
  public $name;
  protected $pricingInfoType = PricingInfo::class;
  protected $pricingInfoDataType = 'array';
  /**
   * Identifies the service provider. This is 'Google' for first party services
   * in Google Cloud Platform.
   *
   * @var string
   */
  public $serviceProviderName;
  /**
   * List of service regions this SKU is offered at. Example: "asia-east1"
   * Service regions can be found at https://cloud.google.com/about/locations/
   *
   * @var string[]
   */
  public $serviceRegions;
  /**
   * The identifier for the SKU. Example: "D041-B8A1-6E0B"
   *
   * @var string
   */
  public $skuId;

  /**
   * The category hierarchy of this SKU, purely for organizational purpose.
   *
   * @param Category $category
   */
  public function setCategory(Category $category)
  {
    $this->category = $category;
  }
  /**
   * @return Category
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * A human readable description of the SKU, has a maximum length of 256
   * characters.
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
   * The geographic taxonomy for this sku.
   *
   * @param GeoTaxonomy $geoTaxonomy
   */
  public function setGeoTaxonomy(GeoTaxonomy $geoTaxonomy)
  {
    $this->geoTaxonomy = $geoTaxonomy;
  }
  /**
   * @return GeoTaxonomy
   */
  public function getGeoTaxonomy()
  {
    return $this->geoTaxonomy;
  }
  /**
   * The resource name for the SKU. Example:
   * "services/6F81-5844-456A/skus/D041-B8A1-6E0B"
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
   * A timeline of pricing info for this SKU in chronological order.
   *
   * @param PricingInfo[] $pricingInfo
   */
  public function setPricingInfo($pricingInfo)
  {
    $this->pricingInfo = $pricingInfo;
  }
  /**
   * @return PricingInfo[]
   */
  public function getPricingInfo()
  {
    return $this->pricingInfo;
  }
  /**
   * Identifies the service provider. This is 'Google' for first party services
   * in Google Cloud Platform.
   *
   * @param string $serviceProviderName
   */
  public function setServiceProviderName($serviceProviderName)
  {
    $this->serviceProviderName = $serviceProviderName;
  }
  /**
   * @return string
   */
  public function getServiceProviderName()
  {
    return $this->serviceProviderName;
  }
  /**
   * List of service regions this SKU is offered at. Example: "asia-east1"
   * Service regions can be found at https://cloud.google.com/about/locations/
   *
   * @param string[] $serviceRegions
   */
  public function setServiceRegions($serviceRegions)
  {
    $this->serviceRegions = $serviceRegions;
  }
  /**
   * @return string[]
   */
  public function getServiceRegions()
  {
    return $this->serviceRegions;
  }
  /**
   * The identifier for the SKU. Example: "D041-B8A1-6E0B"
   *
   * @param string $skuId
   */
  public function setSkuId($skuId)
  {
    $this->skuId = $skuId;
  }
  /**
   * @return string
   */
  public function getSkuId()
  {
    return $this->skuId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Sku::class, 'Google_Service_Cloudbilling_Sku');
