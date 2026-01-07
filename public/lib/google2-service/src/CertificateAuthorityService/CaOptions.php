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

class CaOptions extends \Google\Model
{
  /**
   * Optional. Refers to the "CA" boolean field in the X.509 extension. When
   * this value is missing, the basic constraints extension will be omitted from
   * the certificate.
   *
   * @var bool
   */
  public $isCa;
  /**
   * Optional. Refers to the path length constraint field in the X.509
   * extension. For a CA certificate, this value describes the depth of
   * subordinate CA certificates that are allowed. If this value is less than 0,
   * the request will fail. If this value is missing, the max path length will
   * be omitted from the certificate.
   *
   * @var int
   */
  public $maxIssuerPathLength;

  /**
   * Optional. Refers to the "CA" boolean field in the X.509 extension. When
   * this value is missing, the basic constraints extension will be omitted from
   * the certificate.
   *
   * @param bool $isCa
   */
  public function setIsCa($isCa)
  {
    $this->isCa = $isCa;
  }
  /**
   * @return bool
   */
  public function getIsCa()
  {
    return $this->isCa;
  }
  /**
   * Optional. Refers to the path length constraint field in the X.509
   * extension. For a CA certificate, this value describes the depth of
   * subordinate CA certificates that are allowed. If this value is less than 0,
   * the request will fail. If this value is missing, the max path length will
   * be omitted from the certificate.
   *
   * @param int $maxIssuerPathLength
   */
  public function setMaxIssuerPathLength($maxIssuerPathLength)
  {
    $this->maxIssuerPathLength = $maxIssuerPathLength;
  }
  /**
   * @return int
   */
  public function getMaxIssuerPathLength()
  {
    return $this->maxIssuerPathLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CaOptions::class, 'Google_Service_CertificateAuthorityService_CaOptions');
