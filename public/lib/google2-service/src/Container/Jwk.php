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

namespace Google\Service\Container;

class Jwk extends \Google\Model
{
  /**
   * Algorithm.
   *
   * @var string
   */
  public $alg;
  /**
   * Used for ECDSA keys.
   *
   * @var string
   */
  public $crv;
  /**
   * Used for RSA keys.
   *
   * @var string
   */
  public $e;
  /**
   * Key ID.
   *
   * @var string
   */
  public $kid;
  /**
   * Key Type.
   *
   * @var string
   */
  public $kty;
  /**
   * Used for RSA keys.
   *
   * @var string
   */
  public $n;
  /**
   * Permitted uses for the public keys.
   *
   * @var string
   */
  public $use;
  /**
   * Used for ECDSA keys.
   *
   * @var string
   */
  public $x;
  /**
   * Used for ECDSA keys.
   *
   * @var string
   */
  public $y;

  /**
   * Algorithm.
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
   * Used for ECDSA keys.
   *
   * @param string $crv
   */
  public function setCrv($crv)
  {
    $this->crv = $crv;
  }
  /**
   * @return string
   */
  public function getCrv()
  {
    return $this->crv;
  }
  /**
   * Used for RSA keys.
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
   * Key ID.
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
   * Key Type.
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
   * Used for RSA keys.
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
   * Permitted uses for the public keys.
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
  /**
   * Used for ECDSA keys.
   *
   * @param string $x
   */
  public function setX($x)
  {
    $this->x = $x;
  }
  /**
   * @return string
   */
  public function getX()
  {
    return $this->x;
  }
  /**
   * Used for ECDSA keys.
   *
   * @param string $y
   */
  public function setY($y)
  {
    $this->y = $y;
  }
  /**
   * @return string
   */
  public function getY()
  {
    return $this->y;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Jwk::class, 'Google_Service_Container_Jwk');
