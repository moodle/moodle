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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaDataTypeEndpointEvidence extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_UNSPECIFIED = 'EXFILTRATED_DATA_TYPE_UNSPECIFIED';
  /**
   * The user's phone number.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_PHONE_NUMBER = 'EXFILTRATED_DATA_TYPE_PHONE_NUMBER';
  /**
   * The user's precise location.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_PRECISE_LOCATION = 'EXFILTRATED_DATA_TYPE_PRECISE_LOCATION';
  /**
   * Name of one or more contacts from the user's phone.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_CONTACT_NAME = 'EXFILTRATED_DATA_TYPE_CONTACT_NAME';
  /**
   * Email of one or more contacts from the user's phone.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_CONTACT_EMAIL = 'EXFILTRATED_DATA_TYPE_CONTACT_EMAIL';
  /**
   * Phone number of one or more contacts from the user's phone.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_CONTACT_PHONE_NUMBER = 'EXFILTRATED_DATA_TYPE_CONTACT_PHONE_NUMBER';
  /**
   * Phone number of an incoming text message.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_INCOMING_TEXT_NUMBER = 'EXFILTRATED_DATA_TYPE_INCOMING_TEXT_NUMBER';
  /**
   * Content of an incoming text message.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_INCOMING_TEXT_MESSAGE = 'EXFILTRATED_DATA_TYPE_INCOMING_TEXT_MESSAGE';
  /**
   * Phone number of an outgoing text message.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_OUTGOING_TEXT_NUMBER = 'EXFILTRATED_DATA_TYPE_OUTGOING_TEXT_NUMBER';
  /**
   * Content of an outgoing text message.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_OUTGOING_TEXT_MESSAGE = 'EXFILTRATED_DATA_TYPE_OUTGOING_TEXT_MESSAGE';
  /**
   * Advertising ID.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_ADVERTISING_ID = 'EXFILTRATED_DATA_TYPE_ADVERTISING_ID';
  /**
   * Android ID.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_ANDROID_ID = 'EXFILTRATED_DATA_TYPE_ANDROID_ID';
  /**
   * IMEI.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_IMEI = 'EXFILTRATED_DATA_TYPE_IMEI';
  /**
   * IMSI.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_IMSI = 'EXFILTRATED_DATA_TYPE_IMSI';
  /**
   * Sim serial number.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_SIM_SERIAL_NUMBER = 'EXFILTRATED_DATA_TYPE_SIM_SERIAL_NUMBER';
  /**
   * SSID: Service Set IDentifier, i.e. the network's name.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_SSID = 'EXFILTRATED_DATA_TYPE_SSID';
  /**
   * Information about the main account of the device.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_ACCOUNT = 'EXFILTRATED_DATA_TYPE_ACCOUNT';
  /**
   * Information about an external account, e.g. Facebook, Twitter.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_EXTERNAL_ACCOUNT = 'EXFILTRATED_DATA_TYPE_EXTERNAL_ACCOUNT';
  /**
   * One or more of the package names of apps on the device.
   */
  public const EXFILTRATED_DATA_TYPE_EXFILTRATED_DATA_TYPE_INSTALLED_PACKAGES = 'EXFILTRATED_DATA_TYPE_INSTALLED_PACKAGES';
  protected $collection_key = 'endpointDetails';
  protected $attributedSdksType = GoogleChecksReportV1alphaDataTypeEndpointEvidenceAttributedSdk::class;
  protected $attributedSdksDataType = 'array';
  protected $endpointDetailsType = GoogleChecksReportV1alphaDataTypeEndpointEvidenceEndpointDetails::class;
  protected $endpointDetailsDataType = 'array';
  /**
   * Type of data that was exfiltrated.
   *
   * @var string
   */
  public $exfiltratedDataType;

  /**
   * Set of SDKs that are attributed to the exfiltration.
   *
   * @param GoogleChecksReportV1alphaDataTypeEndpointEvidenceAttributedSdk[] $attributedSdks
   */
  public function setAttributedSdks($attributedSdks)
  {
    $this->attributedSdks = $attributedSdks;
  }
  /**
   * @return GoogleChecksReportV1alphaDataTypeEndpointEvidenceAttributedSdk[]
   */
  public function getAttributedSdks()
  {
    return $this->attributedSdks;
  }
  /**
   * Endpoints the data type was sent to.
   *
   * @param GoogleChecksReportV1alphaDataTypeEndpointEvidenceEndpointDetails[] $endpointDetails
   */
  public function setEndpointDetails($endpointDetails)
  {
    $this->endpointDetails = $endpointDetails;
  }
  /**
   * @return GoogleChecksReportV1alphaDataTypeEndpointEvidenceEndpointDetails[]
   */
  public function getEndpointDetails()
  {
    return $this->endpointDetails;
  }
  /**
   * Type of data that was exfiltrated.
   *
   * Accepted values: EXFILTRATED_DATA_TYPE_UNSPECIFIED,
   * EXFILTRATED_DATA_TYPE_PHONE_NUMBER, EXFILTRATED_DATA_TYPE_PRECISE_LOCATION,
   * EXFILTRATED_DATA_TYPE_CONTACT_NAME, EXFILTRATED_DATA_TYPE_CONTACT_EMAIL,
   * EXFILTRATED_DATA_TYPE_CONTACT_PHONE_NUMBER,
   * EXFILTRATED_DATA_TYPE_INCOMING_TEXT_NUMBER,
   * EXFILTRATED_DATA_TYPE_INCOMING_TEXT_MESSAGE,
   * EXFILTRATED_DATA_TYPE_OUTGOING_TEXT_NUMBER,
   * EXFILTRATED_DATA_TYPE_OUTGOING_TEXT_MESSAGE,
   * EXFILTRATED_DATA_TYPE_ADVERTISING_ID, EXFILTRATED_DATA_TYPE_ANDROID_ID,
   * EXFILTRATED_DATA_TYPE_IMEI, EXFILTRATED_DATA_TYPE_IMSI,
   * EXFILTRATED_DATA_TYPE_SIM_SERIAL_NUMBER, EXFILTRATED_DATA_TYPE_SSID,
   * EXFILTRATED_DATA_TYPE_ACCOUNT, EXFILTRATED_DATA_TYPE_EXTERNAL_ACCOUNT,
   * EXFILTRATED_DATA_TYPE_INSTALLED_PACKAGES
   *
   * @param self::EXFILTRATED_DATA_TYPE_* $exfiltratedDataType
   */
  public function setExfiltratedDataType($exfiltratedDataType)
  {
    $this->exfiltratedDataType = $exfiltratedDataType;
  }
  /**
   * @return self::EXFILTRATED_DATA_TYPE_*
   */
  public function getExfiltratedDataType()
  {
    return $this->exfiltratedDataType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaDataTypeEndpointEvidence::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaDataTypeEndpointEvidence');
