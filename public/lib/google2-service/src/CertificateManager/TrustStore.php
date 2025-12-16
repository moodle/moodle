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

namespace Google\Service\CertificateManager;

class TrustStore extends \Google\Collection
{
  protected $collection_key = 'trustAnchors';
  protected $intermediateCasType = IntermediateCA::class;
  protected $intermediateCasDataType = 'array';
  protected $trustAnchorsType = TrustAnchor::class;
  protected $trustAnchorsDataType = 'array';

  /**
   * Optional. Set of intermediate CA certificates used for the path building
   * phase of chain validation. The field is currently not supported if
   * TrustConfig is used for the workload certificate feature.
   *
   * @param IntermediateCA[] $intermediateCas
   */
  public function setIntermediateCas($intermediateCas)
  {
    $this->intermediateCas = $intermediateCas;
  }
  /**
   * @return IntermediateCA[]
   */
  public function getIntermediateCas()
  {
    return $this->intermediateCas;
  }
  /**
   * Optional. List of Trust Anchors to be used while performing validation
   * against a given TrustStore.
   *
   * @param TrustAnchor[] $trustAnchors
   */
  public function setTrustAnchors($trustAnchors)
  {
    $this->trustAnchors = $trustAnchors;
  }
  /**
   * @return TrustAnchor[]
   */
  public function getTrustAnchors()
  {
    return $this->trustAnchors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrustStore::class, 'Google_Service_CertificateManager_TrustStore');
