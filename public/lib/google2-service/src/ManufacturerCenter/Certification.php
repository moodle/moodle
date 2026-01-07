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

namespace Google\Service\ManufacturerCenter;

class Certification extends \Google\Model
{
  /**
   * Required. Name of the certification body.
   *
   * @var string
   */
  public $authority;
  /**
   * Optional. A unique code to identify the certification.
   *
   * @var string
   */
  public $code;
  /**
   * Optional. A URL link to the certification.
   *
   * @var string
   */
  public $link;
  /**
   * Optional. A URL link to the certification logo.
   *
   * @var string
   */
  public $logo;
  /**
   * Required. Name of the certification.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The expiration date (UTC).
   *
   * @var string
   */
  public $validUntil;
  /**
   * Optional. A custom value of the certification.
   *
   * @var string
   */
  public $value;

  /**
   * Required. Name of the certification body.
   *
   * @param string $authority
   */
  public function setAuthority($authority)
  {
    $this->authority = $authority;
  }
  /**
   * @return string
   */
  public function getAuthority()
  {
    return $this->authority;
  }
  /**
   * Optional. A unique code to identify the certification.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Optional. A URL link to the certification.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * Optional. A URL link to the certification logo.
   *
   * @param string $logo
   */
  public function setLogo($logo)
  {
    $this->logo = $logo;
  }
  /**
   * @return string
   */
  public function getLogo()
  {
    return $this->logo;
  }
  /**
   * Required. Name of the certification.
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
   * Optional. The expiration date (UTC).
   *
   * @param string $validUntil
   */
  public function setValidUntil($validUntil)
  {
    $this->validUntil = $validUntil;
  }
  /**
   * @return string
   */
  public function getValidUntil()
  {
    return $this->validUntil;
  }
  /**
   * Optional. A custom value of the certification.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Certification::class, 'Google_Service_ManufacturerCenter_Certification');
