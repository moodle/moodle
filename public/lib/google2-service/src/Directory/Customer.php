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

namespace Google\Service\Directory;

class Customer extends \Google\Model
{
  /**
   * The customer's secondary contact email address. This email address cannot
   * be on the same domain as the `customerDomain`
   *
   * @var string
   */
  public $alternateEmail;
  /**
   * The customer's creation time (Readonly)
   *
   * @var string
   */
  public $customerCreationTime;
  /**
   * The customer's primary domain name string. Do not include the `www` prefix
   * when creating a new customer.
   *
   * @var string
   */
  public $customerDomain;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The unique ID for the customer's Google Workspace account. (Readonly)
   *
   * @var string
   */
  public $id;
  /**
   * Identifies the resource as a customer. Value: `admin#directory#customer`
   *
   * @var string
   */
  public $kind;
  /**
   * The customer's ISO 639-2 language code. See the [Language Codes](https://de
   * velopers.google.com/workspace/admin/directory/v1/languages) page for the
   * list of supported codes. Valid language codes outside the supported set
   * will be accepted by the API but may lead to unexpected behavior. The
   * default value is `en`.
   *
   * @var string
   */
  public $language;
  /**
   * The customer's contact phone number in
   * [E.164](https://en.wikipedia.org/wiki/E.164) format.
   *
   * @var string
   */
  public $phoneNumber;
  protected $postalAddressType = CustomerPostalAddress::class;
  protected $postalAddressDataType = '';

  /**
   * The customer's secondary contact email address. This email address cannot
   * be on the same domain as the `customerDomain`
   *
   * @param string $alternateEmail
   */
  public function setAlternateEmail($alternateEmail)
  {
    $this->alternateEmail = $alternateEmail;
  }
  /**
   * @return string
   */
  public function getAlternateEmail()
  {
    return $this->alternateEmail;
  }
  /**
   * The customer's creation time (Readonly)
   *
   * @param string $customerCreationTime
   */
  public function setCustomerCreationTime($customerCreationTime)
  {
    $this->customerCreationTime = $customerCreationTime;
  }
  /**
   * @return string
   */
  public function getCustomerCreationTime()
  {
    return $this->customerCreationTime;
  }
  /**
   * The customer's primary domain name string. Do not include the `www` prefix
   * when creating a new customer.
   *
   * @param string $customerDomain
   */
  public function setCustomerDomain($customerDomain)
  {
    $this->customerDomain = $customerDomain;
  }
  /**
   * @return string
   */
  public function getCustomerDomain()
  {
    return $this->customerDomain;
  }
  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The unique ID for the customer's Google Workspace account. (Readonly)
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies the resource as a customer. Value: `admin#directory#customer`
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
   * The customer's ISO 639-2 language code. See the [Language Codes](https://de
   * velopers.google.com/workspace/admin/directory/v1/languages) page for the
   * list of supported codes. Valid language codes outside the supported set
   * will be accepted by the API but may lead to unexpected behavior. The
   * default value is `en`.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The customer's contact phone number in
   * [E.164](https://en.wikipedia.org/wiki/E.164) format.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * The customer's postal address information.
   *
   * @param CustomerPostalAddress $postalAddress
   */
  public function setPostalAddress(CustomerPostalAddress $postalAddress)
  {
    $this->postalAddress = $postalAddress;
  }
  /**
   * @return CustomerPostalAddress
   */
  public function getPostalAddress()
  {
    return $this->postalAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Customer::class, 'Google_Service_Directory_Customer');
