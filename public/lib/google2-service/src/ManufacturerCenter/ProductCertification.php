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

class ProductCertification extends \Google\Collection
{
  protected $collection_key = 'productType';
  /**
   * Required. This is the product's brand name. The brand is used to help
   * identify your product.
   *
   * @var string
   */
  public $brand;
  protected $certificationType = Certification::class;
  protected $certificationDataType = 'array';
  /**
   * Optional. A 2-letter country code (ISO 3166-1 Alpha 2).
   *
   * @var string[]
   */
  public $countryCode;
  protected $destinationStatusesType = DestinationStatus::class;
  protected $destinationStatusesDataType = 'array';
  protected $issuesType = Issue::class;
  protected $issuesDataType = 'array';
  /**
   * Optional. These are the Manufacturer Part Numbers (MPN). MPNs are used to
   * uniquely identify a specific product among all products from the same
   * manufacturer
   *
   * @var string[]
   */
  public $mpn;
  /**
   * Required. The unique name identifier of a product certification Format:
   * accounts/{account}/languages/{language_code}/productCertifications/{id}
   * Where `id` is a some unique identifier and `language_code` is a 2-letter
   * ISO 639-1 code of a Shopping supported language according to
   * https://support.google.com/merchants/answer/160637.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Another name for GTIN.
   *
   * @var string[]
   */
  public $productCode;
  /**
   * Optional. These are your own product categorization system in your product
   * data.
   *
   * @var string[]
   */
  public $productType;
  /**
   * Required. This is to clearly identify the product you are certifying.
   *
   * @var string
   */
  public $title;

  /**
   * Required. This is the product's brand name. The brand is used to help
   * identify your product.
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
   * Required. A list of certifications to link to the described product.
   *
   * @param Certification[] $certification
   */
  public function setCertification($certification)
  {
    $this->certification = $certification;
  }
  /**
   * @return Certification[]
   */
  public function getCertification()
  {
    return $this->certification;
  }
  /**
   * Optional. A 2-letter country code (ISO 3166-1 Alpha 2).
   *
   * @param string[] $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string[]
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Output only. The statuses of the destinations.
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
   * Output only. A server-generated list of issues associated with the product.
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
   * Optional. These are the Manufacturer Part Numbers (MPN). MPNs are used to
   * uniquely identify a specific product among all products from the same
   * manufacturer
   *
   * @param string[] $mpn
   */
  public function setMpn($mpn)
  {
    $this->mpn = $mpn;
  }
  /**
   * @return string[]
   */
  public function getMpn()
  {
    return $this->mpn;
  }
  /**
   * Required. The unique name identifier of a product certification Format:
   * accounts/{account}/languages/{language_code}/productCertifications/{id}
   * Where `id` is a some unique identifier and `language_code` is a 2-letter
   * ISO 639-1 code of a Shopping supported language according to
   * https://support.google.com/merchants/answer/160637.
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
   * Optional. Another name for GTIN.
   *
   * @param string[] $productCode
   */
  public function setProductCode($productCode)
  {
    $this->productCode = $productCode;
  }
  /**
   * @return string[]
   */
  public function getProductCode()
  {
    return $this->productCode;
  }
  /**
   * Optional. These are your own product categorization system in your product
   * data.
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
   * Required. This is to clearly identify the product you are certifying.
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
class_alias(ProductCertification::class, 'Google_Service_ManufacturerCenter_ProductCertification');
