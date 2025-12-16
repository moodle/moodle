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

namespace Google\Service\Dfareporting;

class PostalCode extends \Google\Model
{
  /**
   * Postal code. This is equivalent to the id field.
   *
   * @var string
   */
  public $code;
  /**
   * Country code of the country to which this postal code belongs.
   *
   * @var string
   */
  public $countryCode;
  /**
   * DART ID of the country to which this postal code belongs.
   *
   * @var string
   */
  public $countryDartId;
  /**
   * ID of this postal code.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#postalCode".
   *
   * @var string
   */
  public $kind;

  /**
   * Postal code. This is equivalent to the id field.
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
   * Country code of the country to which this postal code belongs.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * DART ID of the country to which this postal code belongs.
   *
   * @param string $countryDartId
   */
  public function setCountryDartId($countryDartId)
  {
    $this->countryDartId = $countryDartId;
  }
  /**
   * @return string
   */
  public function getCountryDartId()
  {
    return $this->countryDartId;
  }
  /**
   * ID of this postal code.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#postalCode".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostalCode::class, 'Google_Service_Dfareporting_PostalCode');
