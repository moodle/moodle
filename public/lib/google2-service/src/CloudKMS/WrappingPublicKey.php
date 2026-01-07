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

namespace Google\Service\CloudKMS;

class WrappingPublicKey extends \Google\Model
{
  /**
   * The public key, encoded in PEM format. For more information, see the [RFC
   * 7468](https://tools.ietf.org/html/rfc7468) sections for [General
   * Considerations](https://tools.ietf.org/html/rfc7468#section-2) and [Textual
   * Encoding of Subject Public Key Info]
   * (https://tools.ietf.org/html/rfc7468#section-13).
   *
   * @var string
   */
  public $pem;

  /**
   * The public key, encoded in PEM format. For more information, see the [RFC
   * 7468](https://tools.ietf.org/html/rfc7468) sections for [General
   * Considerations](https://tools.ietf.org/html/rfc7468#section-2) and [Textual
   * Encoding of Subject Public Key Info]
   * (https://tools.ietf.org/html/rfc7468#section-13).
   *
   * @param string $pem
   */
  public function setPem($pem)
  {
    $this->pem = $pem;
  }
  /**
   * @return string
   */
  public function getPem()
  {
    return $this->pem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WrappingPublicKey::class, 'Google_Service_CloudKMS_WrappingPublicKey');
