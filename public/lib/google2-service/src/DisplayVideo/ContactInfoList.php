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

namespace Google\Service\DisplayVideo;

class ContactInfoList extends \Google\Collection
{
  protected $collection_key = 'contactInfos';
  protected $consentType = Consent::class;
  protected $consentDataType = '';
  protected $contactInfosType = ContactInfo::class;
  protected $contactInfosDataType = 'array';

  /**
   * Input only. The consent setting for the users in contact_infos. Leaving
   * this field unset indicates that consent is not specified. If ad_user_data
   * or ad_personalization fields are set to `CONSENT_STATUS_DENIED`, the
   * request will return an error.
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
   * A list of ContactInfo objects defining Customer Match audience members. The
   * size of members after splitting the contact_infos mustn't be greater than
   * 500,000.
   *
   * @param ContactInfo[] $contactInfos
   */
  public function setContactInfos($contactInfos)
  {
    $this->contactInfos = $contactInfos;
  }
  /**
   * @return ContactInfo[]
   */
  public function getContactInfos()
  {
    return $this->contactInfos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactInfoList::class, 'Google_Service_DisplayVideo_ContactInfoList');
