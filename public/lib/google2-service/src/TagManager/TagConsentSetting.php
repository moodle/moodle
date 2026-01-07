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

namespace Google\Service\TagManager;

class TagConsentSetting extends \Google\Model
{
  /**
   * Default value where user has not specified any setting on it.
   */
  public const CONSENT_STATUS_notSet = 'notSet';
  /**
   * Tag doesn't require any additional consent settings.
   */
  public const CONSENT_STATUS_notNeeded = 'notNeeded';
  /**
   * Tag requires additional consent settings.
   */
  public const CONSENT_STATUS_needed = 'needed';
  /**
   * The tag's consent status. If set to NEEDED, the runtime will check that the
   * consent types specified by the consent_type field have been granted.
   *
   * @var string
   */
  public $consentStatus;
  protected $consentTypeType = Parameter::class;
  protected $consentTypeDataType = '';

  /**
   * The tag's consent status. If set to NEEDED, the runtime will check that the
   * consent types specified by the consent_type field have been granted.
   *
   * Accepted values: notSet, notNeeded, needed
   *
   * @param self::CONSENT_STATUS_* $consentStatus
   */
  public function setConsentStatus($consentStatus)
  {
    $this->consentStatus = $consentStatus;
  }
  /**
   * @return self::CONSENT_STATUS_*
   */
  public function getConsentStatus()
  {
    return $this->consentStatus;
  }
  /**
   * The type of consents to check for during tag firing if in the consent
   * NEEDED state. This parameter must be of type LIST where each list item is
   * of type STRING.
   *
   * @param Parameter $consentType
   */
  public function setConsentType(Parameter $consentType)
  {
    $this->consentType = $consentType;
  }
  /**
   * @return Parameter
   */
  public function getConsentType()
  {
    return $this->consentType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagConsentSetting::class, 'Google_Service_TagManager_TagConsentSetting');
