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

class ActivateCertificateAuthorityRequest extends \Google\Model
{
  /**
   * Required. The signed CA certificate issued from
   * FetchCertificateAuthorityCsrResponse.pem_csr.
   *
   * @var string
   */
  public $pemCaCertificate;
  /**
   * Optional. An ID to identify requests. Specify a unique request ID so that
   * if you must retry your request, the server will know to ignore the request
   * if it has already been completed. The server will guarantee that for at
   * least 60 minutes since the first request. For example, consider a situation
   * where you make an initial request and the request times out. If you make
   * the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, will
   * ignore the second request. This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;
  protected $subordinateConfigType = SubordinateConfig::class;
  protected $subordinateConfigDataType = '';

  /**
   * Required. The signed CA certificate issued from
   * FetchCertificateAuthorityCsrResponse.pem_csr.
   *
   * @param string $pemCaCertificate
   */
  public function setPemCaCertificate($pemCaCertificate)
  {
    $this->pemCaCertificate = $pemCaCertificate;
  }
  /**
   * @return string
   */
  public function getPemCaCertificate()
  {
    return $this->pemCaCertificate;
  }
  /**
   * Optional. An ID to identify requests. Specify a unique request ID so that
   * if you must retry your request, the server will know to ignore the request
   * if it has already been completed. The server will guarantee that for at
   * least 60 minutes since the first request. For example, consider a situation
   * where you make an initial request and the request times out. If you make
   * the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, will
   * ignore the second request. This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Required. Must include information about the issuer of
   * 'pem_ca_certificate', and any further issuers until the self-signed CA.
   *
   * @param SubordinateConfig $subordinateConfig
   */
  public function setSubordinateConfig(SubordinateConfig $subordinateConfig)
  {
    $this->subordinateConfig = $subordinateConfig;
  }
  /**
   * @return SubordinateConfig
   */
  public function getSubordinateConfig()
  {
    return $this->subordinateConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivateCertificateAuthorityRequest::class, 'Google_Service_CertificateAuthorityService_ActivateCertificateAuthorityRequest');
