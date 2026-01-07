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

namespace Google\Service\Walletobjects;

class EventVenue extends \Google\Model
{
  protected $addressType = LocalizedString::class;
  protected $addressDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventVenue"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $nameType = LocalizedString::class;
  protected $nameDataType = '';

  /**
   * The address of the venue, such as "24 Willie Mays Plaza\nSan Francisco, CA
   * 94107". Address lines are separated by line feed (`\n`) characters. This is
   * required.
   *
   * @param LocalizedString $address
   */
  public function setAddress(LocalizedString $address)
  {
    $this->address = $address;
  }
  /**
   * @return LocalizedString
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventVenue"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of the venue, such as "AT&T Park". This is required.
   *
   * @param LocalizedString $name
   */
  public function setName(LocalizedString $name)
  {
    $this->name = $name;
  }
  /**
   * @return LocalizedString
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventVenue::class, 'Google_Service_Walletobjects_EventVenue');
