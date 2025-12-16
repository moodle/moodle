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

namespace Google\Service\ContainerAnalysis;

class Digest extends \Google\Model
{
  /**
   * `SHA1`, `SHA512` etc.
   *
   * @var string
   */
  public $algo;
  /**
   * Value of the digest.
   *
   * @var string
   */
  public $digestBytes;

  /**
   * `SHA1`, `SHA512` etc.
   *
   * @param string $algo
   */
  public function setAlgo($algo)
  {
    $this->algo = $algo;
  }
  /**
   * @return string
   */
  public function getAlgo()
  {
    return $this->algo;
  }
  /**
   * Value of the digest.
   *
   * @param string $digestBytes
   */
  public function setDigestBytes($digestBytes)
  {
    $this->digestBytes = $digestBytes;
  }
  /**
   * @return string
   */
  public function getDigestBytes()
  {
    return $this->digestBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Digest::class, 'Google_Service_ContainerAnalysis_Digest');
