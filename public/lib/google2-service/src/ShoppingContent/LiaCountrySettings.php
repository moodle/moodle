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

class LiaCountrySettings extends \Google\Model
{
  protected $aboutType = LiaAboutPageSettings::class;
  protected $aboutDataType = '';
  /**
   * Required. CLDR country code (for example, "US").
   *
   * @var string
   */
  public $country;
  /**
   * The status of the "Merchant hosted local storefront" feature.
   *
   * @var bool
   */
  public $hostedLocalStorefrontActive;
  protected $inventoryType = LiaInventorySettings::class;
  protected $inventoryDataType = '';
  protected $omnichannelExperienceType = LiaOmnichannelExperience::class;
  protected $omnichannelExperienceDataType = '';
  protected $onDisplayToOrderType = LiaOnDisplayToOrderSettings::class;
  protected $onDisplayToOrderDataType = '';
  protected $posDataProviderType = LiaPosDataProvider::class;
  protected $posDataProviderDataType = '';
  /**
   * The status of the "Store pickup" feature.
   *
   * @var bool
   */
  public $storePickupActive;

  /**
   * The settings for the About page.
   *
   * @param LiaAboutPageSettings $about
   */
  public function setAbout(LiaAboutPageSettings $about)
  {
    $this->about = $about;
  }
  /**
   * @return LiaAboutPageSettings
   */
  public function getAbout()
  {
    return $this->about;
  }
  /**
   * Required. CLDR country code (for example, "US").
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The status of the "Merchant hosted local storefront" feature.
   *
   * @param bool $hostedLocalStorefrontActive
   */
  public function setHostedLocalStorefrontActive($hostedLocalStorefrontActive)
  {
    $this->hostedLocalStorefrontActive = $hostedLocalStorefrontActive;
  }
  /**
   * @return bool
   */
  public function getHostedLocalStorefrontActive()
  {
    return $this->hostedLocalStorefrontActive;
  }
  /**
   * LIA inventory verification settings.
   *
   * @param LiaInventorySettings $inventory
   */
  public function setInventory(LiaInventorySettings $inventory)
  {
    $this->inventory = $inventory;
  }
  /**
   * @return LiaInventorySettings
   */
  public function getInventory()
  {
    return $this->inventory;
  }
  /**
   * The omnichannel experience configured for this country.
   *
   * @param LiaOmnichannelExperience $omnichannelExperience
   */
  public function setOmnichannelExperience(LiaOmnichannelExperience $omnichannelExperience)
  {
    $this->omnichannelExperience = $omnichannelExperience;
  }
  /**
   * @return LiaOmnichannelExperience
   */
  public function getOmnichannelExperience()
  {
    return $this->omnichannelExperience;
  }
  /**
   * LIA "On Display To Order" settings.
   *
   * @param LiaOnDisplayToOrderSettings $onDisplayToOrder
   */
  public function setOnDisplayToOrder(LiaOnDisplayToOrderSettings $onDisplayToOrder)
  {
    $this->onDisplayToOrder = $onDisplayToOrder;
  }
  /**
   * @return LiaOnDisplayToOrderSettings
   */
  public function getOnDisplayToOrder()
  {
    return $this->onDisplayToOrder;
  }
  /**
   * The POS data provider linked with this country.
   *
   * @param LiaPosDataProvider $posDataProvider
   */
  public function setPosDataProvider(LiaPosDataProvider $posDataProvider)
  {
    $this->posDataProvider = $posDataProvider;
  }
  /**
   * @return LiaPosDataProvider
   */
  public function getPosDataProvider()
  {
    return $this->posDataProvider;
  }
  /**
   * The status of the "Store pickup" feature.
   *
   * @param bool $storePickupActive
   */
  public function setStorePickupActive($storePickupActive)
  {
    $this->storePickupActive = $storePickupActive;
  }
  /**
   * @return bool
   */
  public function getStorePickupActive()
  {
    return $this->storePickupActive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiaCountrySettings::class, 'Google_Service_ShoppingContent_LiaCountrySettings');
