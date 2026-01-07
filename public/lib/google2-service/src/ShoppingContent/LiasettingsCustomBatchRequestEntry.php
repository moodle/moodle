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

class LiasettingsCustomBatchRequestEntry extends \Google\Model
{
  /**
   * The ID of the account for which to get/update account LIA settings.
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
   * Inventory validation contact email. Required only for
   * SetInventoryValidationContact.
   *
   * @var string
   */
  public $contactEmail;
  /**
   * Inventory validation contact name. Required only for
   * SetInventoryValidationContact.
   *
   * @var string
   */
  public $contactName;
  /**
   * The country code. Required only for RequestInventoryVerification.
   *
   * @var string
   */
  public $country;
  /**
   * The Business Profile. Required only for RequestGmbAccess.
   *
   * @var string
   */
  public $gmbEmail;
  protected $liaSettingsType = LiaSettings::class;
  protected $liaSettingsDataType = '';
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`get`" -
   * "`getAccessibleGmbAccounts`" - "`requestGmbAccess`" -
   * "`requestInventoryVerification`" - "`setInventoryVerificationContact`" -
   * "`update`"
   *
   * @var string
   */
  public $method;
  protected $omnichannelExperienceType = LiaOmnichannelExperience::class;
  protected $omnichannelExperienceDataType = '';
  /**
   * The ID of POS data provider. Required only for SetPosProvider.
   *
   * @var string
   */
  public $posDataProviderId;
  /**
   * The account ID by which this merchant is known to the POS provider.
   *
   * @var string
   */
  public $posExternalAccountId;

  /**
   * The ID of the account for which to get/update account LIA settings.
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
   * Inventory validation contact email. Required only for
   * SetInventoryValidationContact.
   *
   * @param string $contactEmail
   */
  public function setContactEmail($contactEmail)
  {
    $this->contactEmail = $contactEmail;
  }
  /**
   * @return string
   */
  public function getContactEmail()
  {
    return $this->contactEmail;
  }
  /**
   * Inventory validation contact name. Required only for
   * SetInventoryValidationContact.
   *
   * @param string $contactName
   */
  public function setContactName($contactName)
  {
    $this->contactName = $contactName;
  }
  /**
   * @return string
   */
  public function getContactName()
  {
    return $this->contactName;
  }
  /**
   * The country code. Required only for RequestInventoryVerification.
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
   * The Business Profile. Required only for RequestGmbAccess.
   *
   * @param string $gmbEmail
   */
  public function setGmbEmail($gmbEmail)
  {
    $this->gmbEmail = $gmbEmail;
  }
  /**
   * @return string
   */
  public function getGmbEmail()
  {
    return $this->gmbEmail;
  }
  /**
   * The account Lia settings to update. Only defined if the method is `update`.
   *
   * @param LiaSettings $liaSettings
   */
  public function setLiaSettings(LiaSettings $liaSettings)
  {
    $this->liaSettings = $liaSettings;
  }
  /**
   * @return LiaSettings
   */
  public function getLiaSettings()
  {
    return $this->liaSettings;
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
   * "`getAccessibleGmbAccounts`" - "`requestGmbAccess`" -
   * "`requestInventoryVerification`" - "`setInventoryVerificationContact`" -
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
   * The omnichannel experience for a country. Required only for
   * SetOmnichannelExperience.
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
   * The ID of POS data provider. Required only for SetPosProvider.
   *
   * @param string $posDataProviderId
   */
  public function setPosDataProviderId($posDataProviderId)
  {
    $this->posDataProviderId = $posDataProviderId;
  }
  /**
   * @return string
   */
  public function getPosDataProviderId()
  {
    return $this->posDataProviderId;
  }
  /**
   * The account ID by which this merchant is known to the POS provider.
   *
   * @param string $posExternalAccountId
   */
  public function setPosExternalAccountId($posExternalAccountId)
  {
    $this->posExternalAccountId = $posExternalAccountId;
  }
  /**
   * @return string
   */
  public function getPosExternalAccountId()
  {
    return $this->posExternalAccountId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiasettingsCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_LiasettingsCustomBatchRequestEntry');
