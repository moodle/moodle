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

namespace Google\Service\Walletobjects;

class Issuer extends \Google\Model
{
  protected $callbackOptionsType = CallbackOptions::class;
  protected $callbackOptionsDataType = '';
  protected $contactInfoType = IssuerContactInfo::class;
  protected $contactInfoDataType = '';
  /**
   * URL for the issuer's home page.
   *
   * @var string
   */
  public $homepageUrl;
  /**
   * The unique identifier for an issuer account. This is automatically
   * generated when the issuer is inserted.
   *
   * @var string
   */
  public $issuerId;
  /**
   * The account name of the issuer.
   *
   * @var string
   */
  public $name;
  protected $smartTapMerchantDataType = SmartTapMerchantData::class;
  protected $smartTapMerchantDataDataType = '';

  /**
   * Allows the issuer to provide their callback settings.
   *
   * @param CallbackOptions $callbackOptions
   */
  public function setCallbackOptions(CallbackOptions $callbackOptions)
  {
    $this->callbackOptions = $callbackOptions;
  }
  /**
   * @return CallbackOptions
   */
  public function getCallbackOptions()
  {
    return $this->callbackOptions;
  }
  /**
   * Issuer contact information.
   *
   * @param IssuerContactInfo $contactInfo
   */
  public function setContactInfo(IssuerContactInfo $contactInfo)
  {
    $this->contactInfo = $contactInfo;
  }
  /**
   * @return IssuerContactInfo
   */
  public function getContactInfo()
  {
    return $this->contactInfo;
  }
  /**
   * URL for the issuer's home page.
   *
   * @param string $homepageUrl
   */
  public function setHomepageUrl($homepageUrl)
  {
    $this->homepageUrl = $homepageUrl;
  }
  /**
   * @return string
   */
  public function getHomepageUrl()
  {
    return $this->homepageUrl;
  }
  /**
   * The unique identifier for an issuer account. This is automatically
   * generated when the issuer is inserted.
   *
   * @param string $issuerId
   */
  public function setIssuerId($issuerId)
  {
    $this->issuerId = $issuerId;
  }
  /**
   * @return string
   */
  public function getIssuerId()
  {
    return $this->issuerId;
  }
  /**
   * The account name of the issuer.
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
   * Available only to Smart Tap enabled partners. Contact support for
   * additional guidance.
   *
   * @param SmartTapMerchantData $smartTapMerchantData
   */
  public function setSmartTapMerchantData(SmartTapMerchantData $smartTapMerchantData)
  {
    $this->smartTapMerchantData = $smartTapMerchantData;
  }
  /**
   * @return SmartTapMerchantData
   */
  public function getSmartTapMerchantData()
  {
    return $this->smartTapMerchantData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Issuer::class, 'Google_Service_Walletobjects_Issuer');
