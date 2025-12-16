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

namespace Google\Service\CertificateAuthorityService;

class SubordinateConfig extends \Google\Model
{
  /**
   * Required. This can refer to a CertificateAuthority that was used to create
   * a subordinate CertificateAuthority. This field is used for information and
   * usability purposes only. The resource name is in the format
   * `projects/locations/caPools/certificateAuthorities`.
   *
   * @var string
   */
  public $certificateAuthority;
  protected $pemIssuerChainType = SubordinateConfigChain::class;
  protected $pemIssuerChainDataType = '';

  /**
   * Required. This can refer to a CertificateAuthority that was used to create
   * a subordinate CertificateAuthority. This field is used for information and
   * usability purposes only. The resource name is in the format
   * `projects/locations/caPools/certificateAuthorities`.
   *
   * @param string $certificateAuthority
   */
  public function setCertificateAuthority($certificateAuthority)
  {
    $this->certificateAuthority = $certificateAuthority;
  }
  /**
   * @return string
   */
  public function getCertificateAuthority()
  {
    return $this->certificateAuthority;
  }
  /**
   * Required. Contains the PEM certificate chain for the issuers of this
   * CertificateAuthority, but not pem certificate for this CA itself.
   *
   * @param SubordinateConfigChain $pemIssuerChain
   */
  public function setPemIssuerChain(SubordinateConfigChain $pemIssuerChain)
  {
    $this->pemIssuerChain = $pemIssuerChain;
  }
  /**
   * @return SubordinateConfigChain
   */
  public function getPemIssuerChain()
  {
    return $this->pemIssuerChain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubordinateConfig::class, 'Google_Service_CertificateAuthorityService_SubordinateConfig');
