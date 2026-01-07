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

namespace Google\Service\CloudDomains;

class ContactSettings extends \Google\Model
{
  /**
   * The contact privacy settings are undefined.
   */
  public const PRIVACY_CONTACT_PRIVACY_UNSPECIFIED = 'CONTACT_PRIVACY_UNSPECIFIED';
  /**
   * All the data from `ContactSettings` is publicly available. When setting
   * this option, you must also provide a `PUBLIC_CONTACT_DATA_ACKNOWLEDGEMENT`
   * in the `contact_notices` field of the request.
   */
  public const PRIVACY_PUBLIC_CONTACT_DATA = 'PUBLIC_CONTACT_DATA';
  /**
   * Deprecated: For more information, see [Cloud Domains feature
   * deprecation](https://cloud.google.com/domains/docs/deprecations/feature-
   * deprecations). None of the data from `ContactSettings` is publicly
   * available. Instead, proxy contact data is published for your domain. Email
   * sent to the proxy email address is forwarded to the registrant's email
   * address. Cloud Domains provides this privacy proxy service at no additional
   * cost.
   *
   * @deprecated
   */
  public const PRIVACY_PRIVATE_CONTACT_DATA = 'PRIVATE_CONTACT_DATA';
  /**
   * The organization name (if provided) and limited non-identifying data from
   * `ContactSettings` is available to the public (e.g. country and state). The
   * remaining data is marked as `REDACTED FOR PRIVACY` in the WHOIS database.
   * The actual information redacted depends on the domain. For details, see
   * [the registration privacy
   * article](https://support.google.com/domains/answer/3251242).
   */
  public const PRIVACY_REDACTED_CONTACT_DATA = 'REDACTED_CONTACT_DATA';
  protected $adminContactType = Contact::class;
  protected $adminContactDataType = '';
  /**
   * Required. Privacy setting for the contacts associated with the
   * `Registration`.
   *
   * @var string
   */
  public $privacy;
  protected $registrantContactType = Contact::class;
  protected $registrantContactDataType = '';
  protected $technicalContactType = Contact::class;
  protected $technicalContactDataType = '';

  /**
   * Required. The administrative contact for the `Registration`.
   *
   * @param Contact $adminContact
   */
  public function setAdminContact(Contact $adminContact)
  {
    $this->adminContact = $adminContact;
  }
  /**
   * @return Contact
   */
  public function getAdminContact()
  {
    return $this->adminContact;
  }
  /**
   * Required. Privacy setting for the contacts associated with the
   * `Registration`.
   *
   * Accepted values: CONTACT_PRIVACY_UNSPECIFIED, PUBLIC_CONTACT_DATA,
   * PRIVATE_CONTACT_DATA, REDACTED_CONTACT_DATA
   *
   * @param self::PRIVACY_* $privacy
   */
  public function setPrivacy($privacy)
  {
    $this->privacy = $privacy;
  }
  /**
   * @return self::PRIVACY_*
   */
  public function getPrivacy()
  {
    return $this->privacy;
  }
  /**
   * Required. The registrant contact for the `Registration`. *Caution: Anyone
   * with access to this email address, phone number, and/or postal address can
   * take control of the domain.* *Warning: For new `Registration`s, the
   * registrant receives an email confirmation that they must complete within 15
   * days to avoid domain suspension.*
   *
   * @param Contact $registrantContact
   */
  public function setRegistrantContact(Contact $registrantContact)
  {
    $this->registrantContact = $registrantContact;
  }
  /**
   * @return Contact
   */
  public function getRegistrantContact()
  {
    return $this->registrantContact;
  }
  /**
   * Required. The technical contact for the `Registration`.
   *
   * @param Contact $technicalContact
   */
  public function setTechnicalContact(Contact $technicalContact)
  {
    $this->technicalContact = $technicalContact;
  }
  /**
   * @return Contact
   */
  public function getTechnicalContact()
  {
    return $this->technicalContact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactSettings::class, 'Google_Service_CloudDomains_ContactSettings');
