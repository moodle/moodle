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

class AuthenticationKey extends \Google\Model
{
  /**
   * Available only to Smart Tap enabled partners. Contact support for
   * additional guidance.
   *
   * @var int
   */
  public $id;
  /**
   * Available only to Smart Tap enabled partners. Contact support for
   * additional guidance.
   *
   * @var string
   */
  public $publicKeyPem;

  /**
   * Available only to Smart Tap enabled partners. Contact support for
   * additional guidance.
   *
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Available only to Smart Tap enabled partners. Contact support for
   * additional guidance.
   *
   * @param string $publicKeyPem
   */
  public function setPublicKeyPem($publicKeyPem)
  {
    $this->publicKeyPem = $publicKeyPem;
  }
  /**
   * @return string
   */
  public function getPublicKeyPem()
  {
    return $this->publicKeyPem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthenticationKey::class, 'Google_Service_Walletobjects_AuthenticationKey');
