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

class RotatingBarcodeTotpDetailsTotpParameters extends \Google\Model
{
  /**
   * The secret key used for the TOTP value generation, encoded as a Base16
   * string.
   *
   * @var string
   */
  public $key;
  /**
   * The length of the TOTP value in decimal digits.
   *
   * @var int
   */
  public $valueLength;

  /**
   * The secret key used for the TOTP value generation, encoded as a Base16
   * string.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The length of the TOTP value in decimal digits.
   *
   * @param int $valueLength
   */
  public function setValueLength($valueLength)
  {
    $this->valueLength = $valueLength;
  }
  /**
   * @return int
   */
  public function getValueLength()
  {
    return $this->valueLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RotatingBarcodeTotpDetailsTotpParameters::class, 'Google_Service_Walletobjects_RotatingBarcodeTotpDetailsTotpParameters');
