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

namespace Google\Service\Compute;

class ShieldedInstanceIdentityEntry extends \Google\Model
{
  /**
   * A PEM-encoded X.509 certificate. This field can be empty.
   *
   * @var string
   */
  public $ekCert;
  /**
   * A PEM-encoded public key.
   *
   * @var string
   */
  public $ekPub;

  /**
   * A PEM-encoded X.509 certificate. This field can be empty.
   *
   * @param string $ekCert
   */
  public function setEkCert($ekCert)
  {
    $this->ekCert = $ekCert;
  }
  /**
   * @return string
   */
  public function getEkCert()
  {
    return $this->ekCert;
  }
  /**
   * A PEM-encoded public key.
   *
   * @param string $ekPub
   */
  public function setEkPub($ekPub)
  {
    $this->ekPub = $ekPub;
  }
  /**
   * @return string
   */
  public function getEkPub()
  {
    return $this->ekPub;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShieldedInstanceIdentityEntry::class, 'Google_Service_Compute_ShieldedInstanceIdentityEntry');
