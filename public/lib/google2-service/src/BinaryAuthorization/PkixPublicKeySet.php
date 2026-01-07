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

namespace Google\Service\BinaryAuthorization;

class PkixPublicKeySet extends \Google\Collection
{
  protected $collection_key = 'pkixPublicKeys';
  protected $pkixPublicKeysType = PkixPublicKey::class;
  protected $pkixPublicKeysDataType = 'array';

  /**
   * Required. `pkix_public_keys` must have at least one entry.
   *
   * @param PkixPublicKey[] $pkixPublicKeys
   */
  public function setPkixPublicKeys($pkixPublicKeys)
  {
    $this->pkixPublicKeys = $pkixPublicKeys;
  }
  /**
   * @return PkixPublicKey[]
   */
  public function getPkixPublicKeys()
  {
    return $this->pkixPublicKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PkixPublicKeySet::class, 'Google_Service_BinaryAuthorization_PkixPublicKeySet');
