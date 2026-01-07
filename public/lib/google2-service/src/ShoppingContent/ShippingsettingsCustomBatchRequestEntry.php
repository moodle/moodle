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

class ShippingsettingsCustomBatchRequestEntry extends \Google\Model
{
  /**
   * The ID of the account for which to get/update account shipping settings.
   *
   * @var string
   */
  public $accountId;
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`get`" -
   * "`update`"
   *
   * @var string
   */
  public $method;
  protected $shippingSettingsType = ShippingSettings::class;
  protected $shippingSettingsDataType = '';

  /**
   * The ID of the account for which to get/update account shipping settings.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * An entry ID, unique within the batch request.
   *
   * @param string $batchId
   */
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  /**
   * @return string
   */
  public function getBatchId()
  {
    return $this->batchId;
  }
  /**
   * The ID of the managing account.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * The method of the batch entry. Acceptable values are: - "`get`" -
   * "`update`"
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The account shipping settings to update. Only defined if the method is
   * `update`.
   *
   * @param ShippingSettings $shippingSettings
   */
  public function setShippingSettings(ShippingSettings $shippingSettings)
  {
    $this->shippingSettings = $shippingSettings;
  }
  /**
   * @return ShippingSettings
   */
  public function getShippingSettings()
  {
    return $this->shippingSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShippingsettingsCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_ShippingsettingsCustomBatchRequestEntry');
