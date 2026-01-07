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

namespace Google\Service\Localservices;

class GoogleAdsHomeservicesLocalservicesV1DetailedLeadReport extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CHARGE_STATUS_CHARGE_STATUS_UNSPECIFIED = 'CHARGE_STATUS_UNSPECIFIED';
  /**
   * Charged.
   */
  public const CHARGE_STATUS_CHARGED = 'CHARGED';
  /**
   * Not charged.
   */
  public const CHARGE_STATUS_NOT_CHARGED = 'NOT_CHARGED';
  /**
   * Not specified.
   */
  public const LEAD_TYPE_LEAD_TYPE_UNSPECIFIED = 'LEAD_TYPE_UNSPECIFIED';
  /**
   * Message lead.
   */
  public const LEAD_TYPE_MESSAGE = 'MESSAGE';
  /**
   * Phone call lead.
   */
  public const LEAD_TYPE_PHONE_CALL = 'PHONE_CALL';
  /**
   * Booking lead.
   */
  public const LEAD_TYPE_BOOKING = 'BOOKING';
  /**
   * Identifies account that received the lead.
   *
   * @var string
   */
  public $accountId;
  protected $aggregatorInfoType = GoogleAdsHomeservicesLocalservicesV1AggregatorInfo::class;
  protected $aggregatorInfoDataType = '';
  protected $bookingLeadType = GoogleAdsHomeservicesLocalservicesV1BookingLead::class;
  protected $bookingLeadDataType = '';
  /**
   * Business name associated to the account.
   *
   * @var string
   */
  public $businessName;
  /**
   * Whether the lead has been charged.
   *
   * @var string
   */
  public $chargeStatus;
  /**
   * Currency code.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Dispute status related to the lead.
   *
   * @var string
   */
  public $disputeStatus;
  /**
   * Location of the associated account's home city.
   *
   * @var string
   */
  public $geo;
  /**
   * Unique identifier of a Detailed Lead Report.
   *
   * @var string
   */
  public $googleAdsLeadId;
  /**
   * Lead category (e.g. hvac, plumber)
   *
   * @var string
   */
  public $leadCategory;
  /**
   * Timestamp of when the lead was created.
   *
   * @var string
   */
  public $leadCreationTimestamp;
  /**
   * Deprecated in favor of google_ads_lead_id. Unique identifier of a Detailed
   * Lead Report.
   *
   * @var string
   */
  public $leadId;
  /**
   * Price of the lead (available only after it has been charged).
   *
   * @var 
   */
  public $leadPrice;
  /**
   * Lead type.
   *
   * @var string
   */
  public $leadType;
  protected $messageLeadType = GoogleAdsHomeservicesLocalservicesV1MessageLead::class;
  protected $messageLeadDataType = '';
  protected $phoneLeadType = GoogleAdsHomeservicesLocalservicesV1PhoneLead::class;
  protected $phoneLeadDataType = '';
  protected $timezoneType = GoogleTypeTimeZone::class;
  protected $timezoneDataType = '';

  /**
   * Identifies account that received the lead.
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
   * Aggregator specific information related to the lead.
   *
   * @param GoogleAdsHomeservicesLocalservicesV1AggregatorInfo $aggregatorInfo
   */
  public function setAggregatorInfo(GoogleAdsHomeservicesLocalservicesV1AggregatorInfo $aggregatorInfo)
  {
    $this->aggregatorInfo = $aggregatorInfo;
  }
  /**
   * @return GoogleAdsHomeservicesLocalservicesV1AggregatorInfo
   */
  public function getAggregatorInfo()
  {
    return $this->aggregatorInfo;
  }
  /**
   * More information associated to only booking leads.
   *
   * @param GoogleAdsHomeservicesLocalservicesV1BookingLead $bookingLead
   */
  public function setBookingLead(GoogleAdsHomeservicesLocalservicesV1BookingLead $bookingLead)
  {
    $this->bookingLead = $bookingLead;
  }
  /**
   * @return GoogleAdsHomeservicesLocalservicesV1BookingLead
   */
  public function getBookingLead()
  {
    return $this->bookingLead;
  }
  /**
   * Business name associated to the account.
   *
   * @param string $businessName
   */
  public function setBusinessName($businessName)
  {
    $this->businessName = $businessName;
  }
  /**
   * @return string
   */
  public function getBusinessName()
  {
    return $this->businessName;
  }
  /**
   * Whether the lead has been charged.
   *
   * Accepted values: CHARGE_STATUS_UNSPECIFIED, CHARGED, NOT_CHARGED
   *
   * @param self::CHARGE_STATUS_* $chargeStatus
   */
  public function setChargeStatus($chargeStatus)
  {
    $this->chargeStatus = $chargeStatus;
  }
  /**
   * @return self::CHARGE_STATUS_*
   */
  public function getChargeStatus()
  {
    return $this->chargeStatus;
  }
  /**
   * Currency code.
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
   * Dispute status related to the lead.
   *
   * @param string $disputeStatus
   */
  public function setDisputeStatus($disputeStatus)
  {
    $this->disputeStatus = $disputeStatus;
  }
  /**
   * @return string
   */
  public function getDisputeStatus()
  {
    return $this->disputeStatus;
  }
  /**
   * Location of the associated account's home city.
   *
   * @param string $geo
   */
  public function setGeo($geo)
  {
    $this->geo = $geo;
  }
  /**
   * @return string
   */
  public function getGeo()
  {
    return $this->geo;
  }
  /**
   * Unique identifier of a Detailed Lead Report.
   *
   * @param string $googleAdsLeadId
   */
  public function setGoogleAdsLeadId($googleAdsLeadId)
  {
    $this->googleAdsLeadId = $googleAdsLeadId;
  }
  /**
   * @return string
   */
  public function getGoogleAdsLeadId()
  {
    return $this->googleAdsLeadId;
  }
  /**
   * Lead category (e.g. hvac, plumber)
   *
   * @param string $leadCategory
   */
  public function setLeadCategory($leadCategory)
  {
    $this->leadCategory = $leadCategory;
  }
  /**
   * @return string
   */
  public function getLeadCategory()
  {
    return $this->leadCategory;
  }
  /**
   * Timestamp of when the lead was created.
   *
   * @param string $leadCreationTimestamp
   */
  public function setLeadCreationTimestamp($leadCreationTimestamp)
  {
    $this->leadCreationTimestamp = $leadCreationTimestamp;
  }
  /**
   * @return string
   */
  public function getLeadCreationTimestamp()
  {
    return $this->leadCreationTimestamp;
  }
  /**
   * Deprecated in favor of google_ads_lead_id. Unique identifier of a Detailed
   * Lead Report.
   *
   * @param string $leadId
   */
  public function setLeadId($leadId)
  {
    $this->leadId = $leadId;
  }
  /**
   * @return string
   */
  public function getLeadId()
  {
    return $this->leadId;
  }
  public function setLeadPrice($leadPrice)
  {
    $this->leadPrice = $leadPrice;
  }
  public function getLeadPrice()
  {
    return $this->leadPrice;
  }
  /**
   * Lead type.
   *
   * Accepted values: LEAD_TYPE_UNSPECIFIED, MESSAGE, PHONE_CALL, BOOKING
   *
   * @param self::LEAD_TYPE_* $leadType
   */
  public function setLeadType($leadType)
  {
    $this->leadType = $leadType;
  }
  /**
   * @return self::LEAD_TYPE_*
   */
  public function getLeadType()
  {
    return $this->leadType;
  }
  /**
   * More information associated to only message leads.
   *
   * @param GoogleAdsHomeservicesLocalservicesV1MessageLead $messageLead
   */
  public function setMessageLead(GoogleAdsHomeservicesLocalservicesV1MessageLead $messageLead)
  {
    $this->messageLead = $messageLead;
  }
  /**
   * @return GoogleAdsHomeservicesLocalservicesV1MessageLead
   */
  public function getMessageLead()
  {
    return $this->messageLead;
  }
  /**
   * More information associated to only phone leads.
   *
   * @param GoogleAdsHomeservicesLocalservicesV1PhoneLead $phoneLead
   */
  public function setPhoneLead(GoogleAdsHomeservicesLocalservicesV1PhoneLead $phoneLead)
  {
    $this->phoneLead = $phoneLead;
  }
  /**
   * @return GoogleAdsHomeservicesLocalservicesV1PhoneLead
   */
  public function getPhoneLead()
  {
    return $this->phoneLead;
  }
  /**
   * Timezone of the particular provider associated to a lead.
   *
   * @param GoogleTypeTimeZone $timezone
   */
  public function setTimezone(GoogleTypeTimeZone $timezone)
  {
    $this->timezone = $timezone;
  }
  /**
   * @return GoogleTypeTimeZone
   */
  public function getTimezone()
  {
    return $this->timezone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsHomeservicesLocalservicesV1DetailedLeadReport::class, 'Google_Service_Localservices_GoogleAdsHomeservicesLocalservicesV1DetailedLeadReport');
