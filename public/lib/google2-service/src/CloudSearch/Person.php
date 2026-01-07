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

namespace Google\Service\CloudSearch;

class Person extends \Google\Collection
{
  protected $collection_key = 'photos';
  protected $emailAddressesType = EmailAddress::class;
  protected $emailAddressesDataType = 'array';
  /**
   * The resource name of the person to provide information about. See
   * [`People.get`](https://developers.google.com/people/api/rest/v1/people/get)
   * from the Google People API.
   *
   * @var string
   */
  public $name;
  /**
   * Obfuscated ID of a person.
   *
   * @var string
   */
  public $obfuscatedId;
  protected $personNamesType = Name::class;
  protected $personNamesDataType = 'array';
  protected $phoneNumbersType = PhoneNumber::class;
  protected $phoneNumbersDataType = 'array';
  protected $photosType = Photo::class;
  protected $photosDataType = 'array';

  /**
   * The person's email addresses
   *
   * @param EmailAddress[] $emailAddresses
   */
  public function setEmailAddresses($emailAddresses)
  {
    $this->emailAddresses = $emailAddresses;
  }
  /**
   * @return EmailAddress[]
   */
  public function getEmailAddresses()
  {
    return $this->emailAddresses;
  }
  /**
   * The resource name of the person to provide information about. See
   * [`People.get`](https://developers.google.com/people/api/rest/v1/people/get)
   * from the Google People API.
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
   * Obfuscated ID of a person.
   *
   * @param string $obfuscatedId
   */
  public function setObfuscatedId($obfuscatedId)
  {
    $this->obfuscatedId = $obfuscatedId;
  }
  /**
   * @return string
   */
  public function getObfuscatedId()
  {
    return $this->obfuscatedId;
  }
  /**
   * The person's name
   *
   * @param Name[] $personNames
   */
  public function setPersonNames($personNames)
  {
    $this->personNames = $personNames;
  }
  /**
   * @return Name[]
   */
  public function getPersonNames()
  {
    return $this->personNames;
  }
  /**
   * The person's phone numbers
   *
   * @param PhoneNumber[] $phoneNumbers
   */
  public function setPhoneNumbers($phoneNumbers)
  {
    $this->phoneNumbers = $phoneNumbers;
  }
  /**
   * @return PhoneNumber[]
   */
  public function getPhoneNumbers()
  {
    return $this->phoneNumbers;
  }
  /**
   * A person's read-only photo. A picture shown next to the person's name to
   * help others recognize the person in search results.
   *
   * @param Photo[] $photos
   */
  public function setPhotos($photos)
  {
    $this->photos = $photos;
  }
  /**
   * @return Photo[]
   */
  public function getPhotos()
  {
    return $this->photos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Person::class, 'Google_Service_CloudSearch_Person');
