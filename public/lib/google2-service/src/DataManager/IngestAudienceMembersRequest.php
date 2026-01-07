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

class IngestAudienceMembersRequest extends \Google\Collection
{
  /**
   * Unspecified Encoding type. Should never be used.
   */
  public const ENCODING_ENCODING_UNSPECIFIED = 'ENCODING_UNSPECIFIED';
  /**
   * Hex encoding.
   */
  public const ENCODING_HEX = 'HEX';
  /**
   * Base 64 encoding.
   */
  public const ENCODING_BASE64 = 'BASE64';
  protected $collection_key = 'destinations';
  protected $audienceMembersType = AudienceMember::class;
  protected $audienceMembersDataType = 'array';
  protected $consentType = Consent::class;
  protected $consentDataType = '';
  protected $destinationsType = Destination::class;
  protected $destinationsDataType = 'array';
  /**
   * Optional. Required for UserData uploads. The encoding type of the user
   * identifiers. For hashed user identifiers, this is the encoding type of the
   * hashed string. For encrypted hashed user identifiers, this is the encoding
   * type of the outer encrypted string, but not necessarily the inner hashed
   * string, meaning the inner hashed string could be encoded in a different way
   * than the outer encrypted string. For non `UserData` uploads, this field is
   * ignored.
   *
   * @var string
   */
  public $encoding;
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  protected $termsOfServiceType = TermsOfService::class;
  protected $termsOfServiceDataType = '';
  /**
   * Optional. For testing purposes. If `true`, the request is validated but not
   * executed. Only errors are returned, not results.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Required. The list of users to send to the specified destinations. At most
   * 10000 AudienceMember resources can be sent in a single request.
   *
   * @param AudienceMember[] $audienceMembers
   */
  public function setAudienceMembers($audienceMembers)
  {
    $this->audienceMembers = $audienceMembers;
  }
  /**
   * @return AudienceMember[]
   */
  public function getAudienceMembers()
  {
    return $this->audienceMembers;
  }
  /**
   * Optional. Request-level consent to apply to all users in the request. User-
   * level consent overrides request-level consent, and can be specified in each
   * AudienceMember.
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
  /**
   * Required. The list of destinations to send the audience members to.
   *
   * @param Destination[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return Destination[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Optional. Required for UserData uploads. The encoding type of the user
   * identifiers. For hashed user identifiers, this is the encoding type of the
   * hashed string. For encrypted hashed user identifiers, this is the encoding
   * type of the outer encrypted string, but not necessarily the inner hashed
   * string, meaning the inner hashed string could be encoded in a different way
   * than the outer encrypted string. For non `UserData` uploads, this field is
   * ignored.
   *
   * Accepted values: ENCODING_UNSPECIFIED, HEX, BASE64
   *
   * @param self::ENCODING_* $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return self::ENCODING_*
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Optional. Encryption information for UserData uploads. If not set, it's
   * assumed that uploaded identifying information is hashed but not encrypted.
   * For non `UserData` uploads, this field is ignored.
   *
   * @param EncryptionInfo $encryptionInfo
   */
  public function setEncryptionInfo(EncryptionInfo $encryptionInfo)
  {
    $this->encryptionInfo = $encryptionInfo;
  }
  /**
   * @return EncryptionInfo
   */
  public function getEncryptionInfo()
  {
    return $this->encryptionInfo;
  }
  /**
   * Optional. The terms of service that the user has accepted/rejected.
   *
   * @param TermsOfService $termsOfService
   */
  public function setTermsOfService(TermsOfService $termsOfService)
  {
    $this->termsOfService = $termsOfService;
  }
  /**
   * @return TermsOfService
   */
  public function getTermsOfService()
  {
    return $this->termsOfService;
  }
  /**
   * Optional. For testing purposes. If `true`, the request is validated but not
   * executed. Only errors are returned, not results.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestAudienceMembersRequest::class, 'Google_Service_DataManager_IngestAudienceMembersRequest');
