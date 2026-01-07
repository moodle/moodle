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

namespace Google\Service\DataManager;

class Event extends \Google\Collection
{
  /**
   * Unspecified EventSource. Should never be used.
   */
  public const EVENT_SOURCE_EVENT_SOURCE_UNSPECIFIED = 'EVENT_SOURCE_UNSPECIFIED';
  /**
   * The event was generated from a web browser.
   */
  public const EVENT_SOURCE_WEB = 'WEB';
  /**
   * The event was generated from an app.
   */
  public const EVENT_SOURCE_APP = 'APP';
  /**
   * The event was generated from an in-store transaction.
   */
  public const EVENT_SOURCE_IN_STORE = 'IN_STORE';
  /**
   * The event was generated from a phone call.
   */
  public const EVENT_SOURCE_PHONE = 'PHONE';
  /**
   * The event was generated from other sources.
   */
  public const EVENT_SOURCE_OTHER = 'OTHER';
  protected $collection_key = 'experimentalFields';
  protected $adIdentifiersType = AdIdentifiers::class;
  protected $adIdentifiersDataType = '';
  protected $additionalEventParametersType = EventParameter::class;
  protected $additionalEventParametersDataType = 'array';
  protected $cartDataType = CartData::class;
  protected $cartDataDataType = '';
  /**
   * Optional. A unique identifier for the user instance of a web client for
   * this GA4 web stream.
   *
   * @var string
   */
  public $clientId;
  protected $consentType = Consent::class;
  protected $consentDataType = '';
  /**
   * Optional. The conversion value associated with the event, for value-based
   * conversions.
   *
   * @var 
   */
  public $conversionValue;
  /**
   * Optional. The currency code associated with all monetary values within this
   * event.
   *
   * @var string
   */
  public $currency;
  protected $customVariablesType = CustomVariable::class;
  protected $customVariablesDataType = 'array';
  /**
   * Optional. Reference string used to determine the destination. If empty, the
   * event will be sent to all destinations in the request.
   *
   * @var string[]
   */
  public $destinationReferences;
  protected $eventDeviceInfoType = DeviceInfo::class;
  protected $eventDeviceInfoDataType = '';
  /**
   * Optional. The name of the event. Required for GA4 events.
   *
   * @var string
   */
  public $eventName;
  /**
   * Optional. Signal for where the event happened (web, app, in-store, etc.).
   *
   * @var string
   */
  public $eventSource;
  /**
   * Required. The time the event occurred.
   *
   * @var string
   */
  public $eventTimestamp;
  protected $experimentalFieldsType = ExperimentalField::class;
  protected $experimentalFieldsDataType = 'array';
  /**
   * Optional. The last time the event was updated.
   *
   * @var string
   */
  public $lastUpdatedTimestamp;
  /**
   * Optional. The unique identifier for this event. Required for conversions
   * using multiple data sources.
   *
   * @var string
   */
  public $transactionId;
  protected $userDataType = UserData::class;
  protected $userDataDataType = '';
  /**
   * Optional. A unique identifier for a user, as defined by the advertiser.
   *
   * @var string
   */
  public $userId;
  protected $userPropertiesType = UserProperties::class;
  protected $userPropertiesDataType = '';

  /**
   * Optional. Identifiers and other information used to match the conversion
   * event with other online activity (such as ad clicks).
   *
   * @param AdIdentifiers $adIdentifiers
   */
  public function setAdIdentifiers(AdIdentifiers $adIdentifiers)
  {
    $this->adIdentifiers = $adIdentifiers;
  }
  /**
   * @return AdIdentifiers
   */
  public function getAdIdentifiers()
  {
    return $this->adIdentifiers;
  }
  /**
   * Optional. A bucket of any [event parameters](https://developers.google.com/
   * analytics/devguides/collection/protocol/ga4/reference/events) to be
   * included within the event that were not already specified using other
   * structured fields.
   *
   * @param EventParameter[] $additionalEventParameters
   */
  public function setAdditionalEventParameters($additionalEventParameters)
  {
    $this->additionalEventParameters = $additionalEventParameters;
  }
  /**
   * @return EventParameter[]
   */
  public function getAdditionalEventParameters()
  {
    return $this->additionalEventParameters;
  }
  /**
   * Optional. Information about the transaction and items associated with the
   * event.
   *
   * @param CartData $cartData
   */
  public function setCartData(CartData $cartData)
  {
    $this->cartData = $cartData;
  }
  /**
   * @return CartData
   */
  public function getCartData()
  {
    return $this->cartData;
  }
  /**
   * Optional. A unique identifier for the user instance of a web client for
   * this GA4 web stream.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Optional. Information about whether the associated user has provided
   * different types of consent.
   *
   * @param Consent $consent
   */
  public function setConsent(Consent $consent)
  {
    $this->consent = $consent;
  }
  /**
   * @return Consent
   */
  public function getConsent()
  {
    return $this->consent;
  }
  public function setConversionValue($conversionValue)
  {
    $this->conversionValue = $conversionValue;
  }
  public function getConversionValue()
  {
    return $this->conversionValue;
  }
  /**
   * Optional. The currency code associated with all monetary values within this
   * event.
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * Optional. Additional key/value pair information to send to the conversion
   * containers (conversion action or FL activity).
   *
   * @param CustomVariable[] $customVariables
   */
  public function setCustomVariables($customVariables)
  {
    $this->customVariables = $customVariables;
  }
  /**
   * @return CustomVariable[]
   */
  public function getCustomVariables()
  {
    return $this->customVariables;
  }
  /**
   * Optional. Reference string used to determine the destination. If empty, the
   * event will be sent to all destinations in the request.
   *
   * @param string[] $destinationReferences
   */
  public function setDestinationReferences($destinationReferences)
  {
    $this->destinationReferences = $destinationReferences;
  }
  /**
   * @return string[]
   */
  public function getDestinationReferences()
  {
    return $this->destinationReferences;
  }
  /**
   * Optional. Information gathered about the device being used (if any) when
   * the event happened.
   *
   * @param DeviceInfo $eventDeviceInfo
   */
  public function setEventDeviceInfo(DeviceInfo $eventDeviceInfo)
  {
    $this->eventDeviceInfo = $eventDeviceInfo;
  }
  /**
   * @return DeviceInfo
   */
  public function getEventDeviceInfo()
  {
    return $this->eventDeviceInfo;
  }
  /**
   * Optional. The name of the event. Required for GA4 events.
   *
   * @param string $eventName
   */
  public function setEventName($eventName)
  {
    $this->eventName = $eventName;
  }
  /**
   * @return string
   */
  public function getEventName()
  {
    return $this->eventName;
  }
  /**
   * Optional. Signal for where the event happened (web, app, in-store, etc.).
   *
   * Accepted values: EVENT_SOURCE_UNSPECIFIED, WEB, APP, IN_STORE, PHONE, OTHER
   *
   * @param self::EVENT_SOURCE_* $eventSource
   */
  public function setEventSource($eventSource)
  {
    $this->eventSource = $eventSource;
  }
  /**
   * @return self::EVENT_SOURCE_*
   */
  public function getEventSource()
  {
    return $this->eventSource;
  }
  /**
   * Required. The time the event occurred.
   *
   * @param string $eventTimestamp
   */
  public function setEventTimestamp($eventTimestamp)
  {
    $this->eventTimestamp = $eventTimestamp;
  }
  /**
   * @return string
   */
  public function getEventTimestamp()
  {
    return $this->eventTimestamp;
  }
  /**
   * Optional. A list of key/value pairs for experimental fields that may
   * eventually be promoted to be part of the API.
   *
   * @param ExperimentalField[] $experimentalFields
   */
  public function setExperimentalFields($experimentalFields)
  {
    $this->experimentalFields = $experimentalFields;
  }
  /**
   * @return ExperimentalField[]
   */
  public function getExperimentalFields()
  {
    return $this->experimentalFields;
  }
  /**
   * Optional. The last time the event was updated.
   *
   * @param string $lastUpdatedTimestamp
   */
  public function setLastUpdatedTimestamp($lastUpdatedTimestamp)
  {
    $this->lastUpdatedTimestamp = $lastUpdatedTimestamp;
  }
  /**
   * @return string
   */
  public function getLastUpdatedTimestamp()
  {
    return $this->lastUpdatedTimestamp;
  }
  /**
   * Optional. The unique identifier for this event. Required for conversions
   * using multiple data sources.
   *
   * @param string $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
  /**
   * Optional. Pieces of user provided data, representing the user the event is
   * associated with.
   *
   * @param UserData $userData
   */
  public function setUserData(UserData $userData)
  {
    $this->userData = $userData;
  }
  /**
   * @return UserData
   */
  public function getUserData()
  {
    return $this->userData;
  }
  /**
   * Optional. A unique identifier for a user, as defined by the advertiser.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
  /**
   * Optional. Advertiser-assessed information about the user at the time that
   * the event happened.
   *
   * @param UserProperties $userProperties
   */
  public function setUserProperties(UserProperties $userProperties)
  {
    $this->userProperties = $userProperties;
  }
  /**
   * @return UserProperties
   */
  public function getUserProperties()
  {
    return $this->userProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Event::class, 'Google_Service_DataManager_Event');
