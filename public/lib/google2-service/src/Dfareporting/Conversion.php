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

namespace Google\Service\Dfareporting;

class Conversion extends \Google\Collection
{
  /**
   * Granted.
   */
  public const AD_USER_DATA_CONSENT_GRANTED = 'GRANTED';
  /**
   * Denied.
   */
  public const AD_USER_DATA_CONSENT_DENIED = 'DENIED';
  protected $collection_key = 'userIdentifiers';
  /**
   * This represents consent for ad user data.
   *
   * @var string
   */
  public $adUserDataConsent;
  protected $cartDataType = CartData::class;
  protected $cartDataDataType = '';
  /**
   * Whether this particular request may come from a user under the age of 13,
   * under COPPA compliance.
   *
   * @var bool
   */
  public $childDirectedTreatment;
  protected $customVariablesType = CustomFloodlightVariable::class;
  protected $customVariablesDataType = 'array';
  /**
   * The display click ID. This field is mutually exclusive with
   * encryptedUserId, encryptedUserIdCandidates[], matchId, mobileDeviceId,
   * gclid, and impressionId. This or encryptedUserId or
   * encryptedUserIdCandidates[] or matchId or mobileDeviceId or gclid or
   * impressionId is a required field.
   *
   * @var string
   */
  public $dclid;
  /**
   * The alphanumeric encrypted user ID. When set, encryptionInfo should also be
   * specified. This field is mutually exclusive with
   * encryptedUserIdCandidates[], matchId, mobileDeviceId, gclid, dclid, and
   * impressionId. This or encryptedUserIdCandidates[] or matchId or
   * mobileDeviceId or gclid or dclid or impressionId is a required field.
   *
   * @var string
   */
  public $encryptedUserId;
  /**
   * A list of the alphanumeric encrypted user IDs. Any user ID with exposure
   * prior to the conversion timestamp will be used in the inserted conversion.
   * If no such user ID is found then the conversion will be rejected with
   * INVALID_ARGUMENT error. When set, encryptionInfo should also be specified.
   * This field may only be used when calling batchinsert; it is not supported
   * by batchupdate. This field is mutually exclusive with encryptedUserId,
   * matchId, mobileDeviceId, gclid dclid, and impressionId. This or
   * encryptedUserId or matchId or mobileDeviceId or gclid or dclid or
   * impressionId is a required field.
   *
   * @var string[]
   */
  public $encryptedUserIdCandidates;
  /**
   * Floodlight Activity ID of this conversion. This is a required field.
   *
   * @var string
   */
  public $floodlightActivityId;
  /**
   * Floodlight Configuration ID of this conversion. This is a required field.
   *
   * @var string
   */
  public $floodlightConfigurationId;
  /**
   * The Google click ID. This field is mutually exclusive with encryptedUserId,
   * encryptedUserIdCandidates[], matchId, mobileDeviceId, dclid, and
   * impressionId. This or encryptedUserId or encryptedUserIdCandidates[] or
   * matchId or mobileDeviceId or dclid or impressionId is a required field.
   *
   * @var string
   */
  public $gclid;
  /**
   * The impression ID. This field is mutually exclusive with encryptedUserId,
   * encryptedUserIdCandidates[], matchId, mobileDeviceId, and gclid. One of
   * these identifiers must be set.
   *
   * @var string
   */
  public $impressionId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#conversion".
   *
   * @var string
   */
  public $kind;
  /**
   * Whether Limit Ad Tracking is enabled. When set to true, the conversion will
   * be used for reporting but not targeting. This will prevent remarketing.
   *
   * @var bool
   */
  public $limitAdTracking;
  /**
   * The match ID field. A match ID is your own first-party identifier that has
   * been synced with Google using the match ID feature in Floodlight. This
   * field is mutually exclusive with encryptedUserId,
   * encryptedUserIdCandidates[],mobileDeviceId, gclid, dclid, and impressionId.
   * This or encryptedUserId orencryptedUserIdCandidates[] or mobileDeviceId or
   * gclid or dclid or impressionIdis a required field.
   *
   * @var string
   */
  public $matchId;
  /**
   * The mobile device ID. This field is mutually exclusive with
   * encryptedUserId, encryptedUserIdCandidates[], matchId, gclid, dclid, and
   * impressionId. This or encryptedUserId or encryptedUserIdCandidates[] or
   * matchId or gclid or dclid or impressionId is a required field.
   *
   * @var string
   */
  public $mobileDeviceId;
  /**
   * Whether the conversion was for a non personalized ad.
   *
   * @var bool
   */
  public $nonPersonalizedAd;
  /**
   * The ordinal of the conversion. Use this field to control how conversions of
   * the same user and day are de-duplicated. This is a required field.
   *
   * @var string
   */
  public $ordinal;
  /**
   * The quantity of the conversion. This is a required field.
   *
   * @var string
   */
  public $quantity;
  /**
   * Session attributes for the conversion, encoded as based64 bytes. This field
   * may only be used when calling batchinsert; it is not supported by
   * batchupdate.
   *
   * @var string
   */
  public $sessionAttributesEncoded;
  /**
   * The timestamp of conversion, in Unix epoch micros. This is a required
   * field.
   *
   * @var string
   */
  public $timestampMicros;
  /**
   * Whether this particular request may come from a user under the age of 16
   * (may differ by country), under compliance with the European Union's General
   * Data Protection Regulation (GDPR).
   *
   * @var bool
   */
  public $treatmentForUnderage;
  protected $userIdentifiersType = UserIdentifier::class;
  protected $userIdentifiersDataType = 'array';
  /**
   * The value of the conversion. Interpreted in CM360 Floodlight config parent
   * advertiser's currency code. This is a required field.
   *
   * @var 
   */
  public $value;

  /**
   * This represents consent for ad user data.
   *
   * Accepted values: GRANTED, DENIED
   *
   * @param self::AD_USER_DATA_CONSENT_* $adUserDataConsent
   */
  public function setAdUserDataConsent($adUserDataConsent)
  {
    $this->adUserDataConsent = $adUserDataConsent;
  }
  /**
   * @return self::AD_USER_DATA_CONSENT_*
   */
  public function getAdUserDataConsent()
  {
    return $this->adUserDataConsent;
  }
  /**
   * The cart data associated with this conversion.
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
   * Whether this particular request may come from a user under the age of 13,
   * under COPPA compliance.
   *
   * @param bool $childDirectedTreatment
   */
  public function setChildDirectedTreatment($childDirectedTreatment)
  {
    $this->childDirectedTreatment = $childDirectedTreatment;
  }
  /**
   * @return bool
   */
  public function getChildDirectedTreatment()
  {
    return $this->childDirectedTreatment;
  }
  /**
   * Custom floodlight variables.
   *
   * @param CustomFloodlightVariable[] $customVariables
   */
  public function setCustomVariables($customVariables)
  {
    $this->customVariables = $customVariables;
  }
  /**
   * @return CustomFloodlightVariable[]
   */
  public function getCustomVariables()
  {
    return $this->customVariables;
  }
  /**
   * The display click ID. This field is mutually exclusive with
   * encryptedUserId, encryptedUserIdCandidates[], matchId, mobileDeviceId,
   * gclid, and impressionId. This or encryptedUserId or
   * encryptedUserIdCandidates[] or matchId or mobileDeviceId or gclid or
   * impressionId is a required field.
   *
   * @param string $dclid
   */
  public function setDclid($dclid)
  {
    $this->dclid = $dclid;
  }
  /**
   * @return string
   */
  public function getDclid()
  {
    return $this->dclid;
  }
  /**
   * The alphanumeric encrypted user ID. When set, encryptionInfo should also be
   * specified. This field is mutually exclusive with
   * encryptedUserIdCandidates[], matchId, mobileDeviceId, gclid, dclid, and
   * impressionId. This or encryptedUserIdCandidates[] or matchId or
   * mobileDeviceId or gclid or dclid or impressionId is a required field.
   *
   * @param string $encryptedUserId
   */
  public function setEncryptedUserId($encryptedUserId)
  {
    $this->encryptedUserId = $encryptedUserId;
  }
  /**
   * @return string
   */
  public function getEncryptedUserId()
  {
    return $this->encryptedUserId;
  }
  /**
   * A list of the alphanumeric encrypted user IDs. Any user ID with exposure
   * prior to the conversion timestamp will be used in the inserted conversion.
   * If no such user ID is found then the conversion will be rejected with
   * INVALID_ARGUMENT error. When set, encryptionInfo should also be specified.
   * This field may only be used when calling batchinsert; it is not supported
   * by batchupdate. This field is mutually exclusive with encryptedUserId,
   * matchId, mobileDeviceId, gclid dclid, and impressionId. This or
   * encryptedUserId or matchId or mobileDeviceId or gclid or dclid or
   * impressionId is a required field.
   *
   * @param string[] $encryptedUserIdCandidates
   */
  public function setEncryptedUserIdCandidates($encryptedUserIdCandidates)
  {
    $this->encryptedUserIdCandidates = $encryptedUserIdCandidates;
  }
  /**
   * @return string[]
   */
  public function getEncryptedUserIdCandidates()
  {
    return $this->encryptedUserIdCandidates;
  }
  /**
   * Floodlight Activity ID of this conversion. This is a required field.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
  /**
   * Floodlight Configuration ID of this conversion. This is a required field.
   *
   * @param string $floodlightConfigurationId
   */
  public function setFloodlightConfigurationId($floodlightConfigurationId)
  {
    $this->floodlightConfigurationId = $floodlightConfigurationId;
  }
  /**
   * @return string
   */
  public function getFloodlightConfigurationId()
  {
    return $this->floodlightConfigurationId;
  }
  /**
   * The Google click ID. This field is mutually exclusive with encryptedUserId,
   * encryptedUserIdCandidates[], matchId, mobileDeviceId, dclid, and
   * impressionId. This or encryptedUserId or encryptedUserIdCandidates[] or
   * matchId or mobileDeviceId or dclid or impressionId is a required field.
   *
   * @param string $gclid
   */
  public function setGclid($gclid)
  {
    $this->gclid = $gclid;
  }
  /**
   * @return string
   */
  public function getGclid()
  {
    return $this->gclid;
  }
  /**
   * The impression ID. This field is mutually exclusive with encryptedUserId,
   * encryptedUserIdCandidates[], matchId, mobileDeviceId, and gclid. One of
   * these identifiers must be set.
   *
   * @param string $impressionId
   */
  public function setImpressionId($impressionId)
  {
    $this->impressionId = $impressionId;
  }
  /**
   * @return string
   */
  public function getImpressionId()
  {
    return $this->impressionId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#conversion".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Whether Limit Ad Tracking is enabled. When set to true, the conversion will
   * be used for reporting but not targeting. This will prevent remarketing.
   *
   * @param bool $limitAdTracking
   */
  public function setLimitAdTracking($limitAdTracking)
  {
    $this->limitAdTracking = $limitAdTracking;
  }
  /**
   * @return bool
   */
  public function getLimitAdTracking()
  {
    return $this->limitAdTracking;
  }
  /**
   * The match ID field. A match ID is your own first-party identifier that has
   * been synced with Google using the match ID feature in Floodlight. This
   * field is mutually exclusive with encryptedUserId,
   * encryptedUserIdCandidates[],mobileDeviceId, gclid, dclid, and impressionId.
   * This or encryptedUserId orencryptedUserIdCandidates[] or mobileDeviceId or
   * gclid or dclid or impressionIdis a required field.
   *
   * @param string $matchId
   */
  public function setMatchId($matchId)
  {
    $this->matchId = $matchId;
  }
  /**
   * @return string
   */
  public function getMatchId()
  {
    return $this->matchId;
  }
  /**
   * The mobile device ID. This field is mutually exclusive with
   * encryptedUserId, encryptedUserIdCandidates[], matchId, gclid, dclid, and
   * impressionId. This or encryptedUserId or encryptedUserIdCandidates[] or
   * matchId or gclid or dclid or impressionId is a required field.
   *
   * @param string $mobileDeviceId
   */
  public function setMobileDeviceId($mobileDeviceId)
  {
    $this->mobileDeviceId = $mobileDeviceId;
  }
  /**
   * @return string
   */
  public function getMobileDeviceId()
  {
    return $this->mobileDeviceId;
  }
  /**
   * Whether the conversion was for a non personalized ad.
   *
   * @param bool $nonPersonalizedAd
   */
  public function setNonPersonalizedAd($nonPersonalizedAd)
  {
    $this->nonPersonalizedAd = $nonPersonalizedAd;
  }
  /**
   * @return bool
   */
  public function getNonPersonalizedAd()
  {
    return $this->nonPersonalizedAd;
  }
  /**
   * The ordinal of the conversion. Use this field to control how conversions of
   * the same user and day are de-duplicated. This is a required field.
   *
   * @param string $ordinal
   */
  public function setOrdinal($ordinal)
  {
    $this->ordinal = $ordinal;
  }
  /**
   * @return string
   */
  public function getOrdinal()
  {
    return $this->ordinal;
  }
  /**
   * The quantity of the conversion. This is a required field.
   *
   * @param string $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return string
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
  /**
   * Session attributes for the conversion, encoded as based64 bytes. This field
   * may only be used when calling batchinsert; it is not supported by
   * batchupdate.
   *
   * @param string $sessionAttributesEncoded
   */
  public function setSessionAttributesEncoded($sessionAttributesEncoded)
  {
    $this->sessionAttributesEncoded = $sessionAttributesEncoded;
  }
  /**
   * @return string
   */
  public function getSessionAttributesEncoded()
  {
    return $this->sessionAttributesEncoded;
  }
  /**
   * The timestamp of conversion, in Unix epoch micros. This is a required
   * field.
   *
   * @param string $timestampMicros
   */
  public function setTimestampMicros($timestampMicros)
  {
    $this->timestampMicros = $timestampMicros;
  }
  /**
   * @return string
   */
  public function getTimestampMicros()
  {
    return $this->timestampMicros;
  }
  /**
   * Whether this particular request may come from a user under the age of 16
   * (may differ by country), under compliance with the European Union's General
   * Data Protection Regulation (GDPR).
   *
   * @param bool $treatmentForUnderage
   */
  public function setTreatmentForUnderage($treatmentForUnderage)
  {
    $this->treatmentForUnderage = $treatmentForUnderage;
  }
  /**
   * @return bool
   */
  public function getTreatmentForUnderage()
  {
    return $this->treatmentForUnderage;
  }
  /**
   * The user identifiers to enhance the conversion. The maximum number of user
   * identifiers for each conversion is 5.
   *
   * @param UserIdentifier[] $userIdentifiers
   */
  public function setUserIdentifiers($userIdentifiers)
  {
    $this->userIdentifiers = $userIdentifiers;
  }
  /**
   * @return UserIdentifier[]
   */
  public function getUserIdentifiers()
  {
    return $this->userIdentifiers;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Conversion::class, 'Google_Service_Dfareporting_Conversion');
