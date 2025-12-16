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

namespace Google\Service\CloudIdentity;

class DsaPublicKeyInfo extends \Google\Model
{
  /**
   * Key size in bits (size of parameter P).
   *
   * @var int
   */
  public $keySize;

  /**
   * Key size in bits (size of parameter P).
   *
   * @param int $keySize
   */
  public function setKeySize($keySize)
  {
    $this->keySize = $keySize;
  }
  /**
   * @return int
   */
  public function getKeySize()
  {
    return $this->keySize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DsaPublicKeyInfo::class, 'Google_Service_CloudIdentity_DsaPublicKeyInfo');
