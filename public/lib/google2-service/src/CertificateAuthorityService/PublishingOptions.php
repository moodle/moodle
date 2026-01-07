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

class PublishingOptions extends \Google\Model
{
  /**
   * Not specified. By default, PEM format will be used.
   */
  public const ENCODING_FORMAT_ENCODING_FORMAT_UNSPECIFIED = 'ENCODING_FORMAT_UNSPECIFIED';
  /**
   * The CertificateAuthority's CA certificate and CRLs will be published in PEM
   * format.
   */
  public const ENCODING_FORMAT_PEM = 'PEM';
  /**
   * The CertificateAuthority's CA certificate and CRLs will be published in DER
   * format.
   */
  public const ENCODING_FORMAT_DER = 'DER';
  /**
   * Optional. Specifies the encoding format of each CertificateAuthority
   * resource's CA certificate and CRLs. If this is omitted, CA certificates and
   * CRLs will be published in PEM.
   *
   * @var string
   */
  public $encodingFormat;
  /**
   * Optional. When true, publishes each CertificateAuthority's CA certificate
   * and includes its URL in the "Authority Information Access" X.509 extension
   * in all issued Certificates. If this is false, the CA certificate will not
   * be published and the corresponding X.509 extension will not be written in
   * issued certificates.
   *
   * @var bool
   */
  public $publishCaCert;
  /**
   * Optional. When true, publishes each CertificateAuthority's CRL and includes
   * its URL in the "CRL Distribution Points" X.509 extension in all issued
   * Certificates. If this is false, CRLs will not be published and the
   * corresponding X.509 extension will not be written in issued certificates.
   * CRLs will expire 7 days from their creation. However, we will rebuild
   * daily. CRLs are also rebuilt shortly after a certificate is revoked.
   *
   * @var bool
   */
  public $publishCrl;

  /**
   * Optional. Specifies the encoding format of each CertificateAuthority
   * resource's CA certificate and CRLs. If this is omitted, CA certificates and
   * CRLs will be published in PEM.
   *
   * Accepted values: ENCODING_FORMAT_UNSPECIFIED, PEM, DER
   *
   * @param self::ENCODING_FORMAT_* $encodingFormat
   */
  public function setEncodingFormat($encodingFormat)
  {
    $this->encodingFormat = $encodingFormat;
  }
  /**
   * @return self::ENCODING_FORMAT_*
   */
  public function getEncodingFormat()
  {
    return $this->encodingFormat;
  }
  /**
   * Optional. When true, publishes each CertificateAuthority's CA certificate
   * and includes its URL in the "Authority Information Access" X.509 extension
   * in all issued Certificates. If this is false, the CA certificate will not
   * be published and the corresponding X.509 extension will not be written in
   * issued certificates.
   *
   * @param bool $publishCaCert
   */
  public function setPublishCaCert($publishCaCert)
  {
    $this->publishCaCert = $publishCaCert;
  }
  /**
   * @return bool
   */
  public function getPublishCaCert()
  {
    return $this->publishCaCert;
  }
  /**
   * Optional. When true, publishes each CertificateAuthority's CRL and includes
   * its URL in the "CRL Distribution Points" X.509 extension in all issued
   * Certificates. If this is false, CRLs will not be published and the
   * corresponding X.509 extension will not be written in issued certificates.
   * CRLs will expire 7 days from their creation. However, we will rebuild
   * daily. CRLs are also rebuilt shortly after a certificate is revoked.
   *
   * @param bool $publishCrl
   */
  public function setPublishCrl($publishCrl)
  {
    $this->publishCrl = $publishCrl;
  }
  /**
   * @return bool
   */
  public function getPublishCrl()
  {
    return $this->publishCrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishingOptions::class, 'Google_Service_CertificateAuthorityService_PublishingOptions');
