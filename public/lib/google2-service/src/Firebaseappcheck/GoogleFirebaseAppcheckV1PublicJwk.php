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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1PublicJwk extends \Google\Model
{
  /**
   * See [section 4.4 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.4).
   *
   * @var string
   */
  public $alg;
  /**
   * See [section 6.3.1.2 of RFC
   * 7518](https://tools.ietf.org/html/rfc7518#section-6.3.1.2).
   *
   * @var string
   */
  public $e;
  /**
   * See [section 4.5 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.5).
   *
   * @var string
   */
  public $kid;
  /**
   * See [section 4.1 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.1).
   *
   * @var string
   */
  public $kty;
  /**
   * See [section 6.3.1.1 of RFC
   * 7518](https://tools.ietf.org/html/rfc7518#section-6.3.1.1).
   *
   * @var string
   */
  public $n;
  /**
   * See [section 4.2 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.2).
   *
   * @var string
   */
  public $use;

  /**
   * See [section 4.4 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.4).
   *
   * @param string $alg
   */
  public function setAlg($alg)
  {
    $this->alg = $alg;
  }
  /**
   * @return string
   */
  public function getAlg()
  {
    return $this->alg;
  }
  /**
   * See [section 6.3.1.2 of RFC
   * 7518](https://tools.ietf.org/html/rfc7518#section-6.3.1.2).
   *
   * @param string $e
   */
  public function setE($e)
  {
    $this->e = $e;
  }
  /**
   * @return string
   */
  public function getE()
  {
    return $this->e;
  }
  /**
   * See [section 4.5 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.5).
   *
   * @param string $kid
   */
  public function setKid($kid)
  {
    $this->kid = $kid;
  }
  /**
   * @return string
   */
  public function getKid()
  {
    return $this->kid;
  }
  /**
   * See [section 4.1 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.1).
   *
   * @param string $kty
   */
  public function setKty($kty)
  {
    $this->kty = $kty;
  }
  /**
   * @return string
   */
  public function getKty()
  {
    return $this->kty;
  }
  /**
   * See [section 6.3.1.1 of RFC
   * 7518](https://tools.ietf.org/html/rfc7518#section-6.3.1.1).
   *
   * @param string $n
   */
  public function setN($n)
  {
    $this->n = $n;
  }
  /**
   * @return string
   */
  public function getN()
  {
    return $this->n;
  }
  /**
   * See [section 4.2 of RFC
   * 7517](https://tools.ietf.org/html/rfc7517#section-4.2).
   *
   * @param string $use
   */
  public function setUse($use)
  {
    $this->use = $use;
  }
  /**
   * @return string
   */
  public function getUse()
  {
    return $this->use;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1PublicJwk::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1PublicJwk');
