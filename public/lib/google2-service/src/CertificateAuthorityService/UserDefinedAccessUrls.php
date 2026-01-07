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

class UserDefinedAccessUrls extends \Google\Collection
{
  protected $collection_key = 'crlAccessUrls';
  /**
   * Optional. A list of URLs where the issuer CA certificate may be downloaded,
   * which appears in the "Authority Information Access" extension in the
   * certificate. If specified, the default Cloud Storage URLs will be omitted.
   *
   * @var string[]
   */
  public $aiaIssuingCertificateUrls;
  /**
   * Optional. A list of URLs where to obtain CRL information, i.e. the
   * DistributionPoint.fullName described by
   * https://tools.ietf.org/html/rfc5280#section-4.2.1.13. If specified, the
   * default Cloud Storage URLs will be omitted.
   *
   * @var string[]
   */
  public $crlAccessUrls;

  /**
   * Optional. A list of URLs where the issuer CA certificate may be downloaded,
   * which appears in the "Authority Information Access" extension in the
   * certificate. If specified, the default Cloud Storage URLs will be omitted.
   *
   * @param string[] $aiaIssuingCertificateUrls
   */
  public function setAiaIssuingCertificateUrls($aiaIssuingCertificateUrls)
  {
    $this->aiaIssuingCertificateUrls = $aiaIssuingCertificateUrls;
  }
  /**
   * @return string[]
   */
  public function getAiaIssuingCertificateUrls()
  {
    return $this->aiaIssuingCertificateUrls;
  }
  /**
   * Optional. A list of URLs where to obtain CRL information, i.e. the
   * DistributionPoint.fullName described by
   * https://tools.ietf.org/html/rfc5280#section-4.2.1.13. If specified, the
   * default Cloud Storage URLs will be omitted.
   *
   * @param string[] $crlAccessUrls
   */
  public function setCrlAccessUrls($crlAccessUrls)
  {
    $this->crlAccessUrls = $crlAccessUrls;
  }
  /**
   * @return string[]
   */
  public function getCrlAccessUrls()
  {
    return $this->crlAccessUrls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserDefinedAccessUrls::class, 'Google_Service_CertificateAuthorityService_UserDefinedAccessUrls');
