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

namespace Google\Service\ShoppingContent;

class ProductCertification extends \Google\Model
{
  /**
   * The certification authority, for example "European_Commission". Maximum
   * length is 2000 characters.
   *
   * @var string
   */
  public $certificationAuthority;
  /**
   * The certification code, for eaxample "123456". Maximum length is 2000
   * characters.
   *
   * @var string
   */
  public $certificationCode;
  /**
   * The name of the certification, for example "EPREL". Maximum length is 2000
   * characters.
   *
   * @var string
   */
  public $certificationName;
  /**
   * The certification value (also known as class, level or grade), for example
   * "A+", "C", "gold". Maximum length is 2000 characters.
   *
   * @var string
   */
  public $certificationValue;

  /**
   * The certification authority, for example "European_Commission". Maximum
   * length is 2000 characters.
   *
   * @param string $certificationAuthority
   */
  public function setCertificationAuthority($certificationAuthority)
  {
    $this->certificationAuthority = $certificationAuthority;
  }
  /**
   * @return string
   */
  public function getCertificationAuthority()
  {
    return $this->certificationAuthority;
  }
  /**
   * The certification code, for eaxample "123456". Maximum length is 2000
   * characters.
   *
   * @param string $certificationCode
   */
  public function setCertificationCode($certificationCode)
  {
    $this->certificationCode = $certificationCode;
  }
  /**
   * @return string
   */
  public function getCertificationCode()
  {
    return $this->certificationCode;
  }
  /**
   * The name of the certification, for example "EPREL". Maximum length is 2000
   * characters.
   *
   * @param string $certificationName
   */
  public function setCertificationName($certificationName)
  {
    $this->certificationName = $certificationName;
  }
  /**
   * @return string
   */
  public function getCertificationName()
  {
    return $this->certificationName;
  }
  /**
   * The certification value (also known as class, level or grade), for example
   * "A+", "C", "gold". Maximum length is 2000 characters.
   *
   * @param string $certificationValue
   */
  public function setCertificationValue($certificationValue)
  {
    $this->certificationValue = $certificationValue;
  }
  /**
   * @return string
   */
  public function getCertificationValue()
  {
    return $this->certificationValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductCertification::class, 'Google_Service_ShoppingContent_ProductCertification');
