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

namespace Google\Service\AndroidPublisher;

class OneTimeProductTaxAndComplianceSettings extends \Google\Collection
{
  protected $collection_key = 'regionalTaxConfigs';
  /**
   * Whether this one-time product is declared as a product representing a
   * tokenized digital asset.
   *
   * @var bool
   */
  public $isTokenizedDigitalAsset;
  /**
   * Product tax category code to assign to the one-time product. Product tax
   * category determines the transaction tax rates applied to the product. Refer
   * to the [Help Center article](https://support.google.com/googleplay/android-
   * developer/answer/16408159) for more information.
   *
   * @var string
   */
  public $productTaxCategoryCode;
  protected $regionalTaxConfigsType = RegionalTaxConfig::class;
  protected $regionalTaxConfigsDataType = 'array';

  /**
   * Whether this one-time product is declared as a product representing a
   * tokenized digital asset.
   *
   * @param bool $isTokenizedDigitalAsset
   */
  public function setIsTokenizedDigitalAsset($isTokenizedDigitalAsset)
  {
    $this->isTokenizedDigitalAsset = $isTokenizedDigitalAsset;
  }
  /**
   * @return bool
   */
  public function getIsTokenizedDigitalAsset()
  {
    return $this->isTokenizedDigitalAsset;
  }
  /**
   * Product tax category code to assign to the one-time product. Product tax
   * category determines the transaction tax rates applied to the product. Refer
   * to the [Help Center article](https://support.google.com/googleplay/android-
   * developer/answer/16408159) for more information.
   *
   * @param string $productTaxCategoryCode
   */
  public function setProductTaxCategoryCode($productTaxCategoryCode)
  {
    $this->productTaxCategoryCode = $productTaxCategoryCode;
  }
  /**
   * @return string
   */
  public function getProductTaxCategoryCode()
  {
    return $this->productTaxCategoryCode;
  }
  /**
   * Regional tax configuration.
   *
   * @param RegionalTaxConfig[] $regionalTaxConfigs
   */
  public function setRegionalTaxConfigs($regionalTaxConfigs)
  {
    $this->regionalTaxConfigs = $regionalTaxConfigs;
  }
  /**
   * @return RegionalTaxConfig[]
   */
  public function getRegionalTaxConfigs()
  {
    return $this->regionalTaxConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductTaxAndComplianceSettings::class, 'Google_Service_AndroidPublisher_OneTimeProductTaxAndComplianceSettings');
