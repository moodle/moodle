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

namespace Google\Service\CloudAlloyDBAdmin;

class GenerateClientCertificateResponse extends \Google\Collection
{
  protected $collection_key = 'pemCertificateChain';
  /**
   * @var string
   */
  public $caCert;
  /**
   * @var string[]
   */
  public $pemCertificateChain;

  /**
   * @param string
   */
  public function setCaCert($caCert)
  {
    $this->caCert = $caCert;
  }
  /**
   * @return string
   */
  public function getCaCert()
  {
    return $this->caCert;
  }
  /**
   * @param string[]
   */
  public function setPemCertificateChain($pemCertificateChain)
  {
    $this->pemCertificateChain = $pemCertificateChain;
  }
  /**
   * @return string[]
   */
  public function getPemCertificateChain()
  {
    return $this->pemCertificateChain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateClientCertificateResponse::class, 'Google_Service_CloudAlloyDBAdmin_GenerateClientCertificateResponse');
