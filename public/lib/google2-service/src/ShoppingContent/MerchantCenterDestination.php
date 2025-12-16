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

class MerchantCenterDestination extends \Google\Model
{
  protected $attributionSettingsType = AttributionSettings::class;
  protected $attributionSettingsDataType = '';
  /**
   * Required. Three-letter currency code (ISO 4217). The currency code defines
   * in which currency the conversions sent to this destination will be reported
   * in Merchant Center.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Output only. Merchant Center Destination ID.
   *
   * @var string
   */
  public $destinationId;
  /**
   * Required. Merchant-specified display name for the destination. This is the
   * name that identifies the conversion source within the Merchant Center UI.
   * Limited to 64 characters.
   *
   * @var string
   */
  public $displayName;

  /**
   * Required. Attribution settings being used for the Merchant Center
   * Destination.
   *
   * @param AttributionSettings $attributionSettings
   */
  public function setAttributionSettings(AttributionSettings $attributionSettings)
  {
    $this->attributionSettings = $attributionSettings;
  }
  /**
   * @return AttributionSettings
   */
  public function getAttributionSettings()
  {
    return $this->attributionSettings;
  }
  /**
   * Required. Three-letter currency code (ISO 4217). The currency code defines
   * in which currency the conversions sent to this destination will be reported
   * in Merchant Center.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Output only. Merchant Center Destination ID.
   *
   * @param string $destinationId
   */
  public function setDestinationId($destinationId)
  {
    $this->destinationId = $destinationId;
  }
  /**
   * @return string
   */
  public function getDestinationId()
  {
    return $this->destinationId;
  }
  /**
   * Required. Merchant-specified display name for the destination. This is the
   * name that identifies the conversion source within the Merchant Center UI.
   * Limited to 64 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MerchantCenterDestination::class, 'Google_Service_ShoppingContent_MerchantCenterDestination');
