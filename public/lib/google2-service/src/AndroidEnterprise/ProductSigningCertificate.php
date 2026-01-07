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

namespace Google\Service\AndroidEnterprise;

class ProductSigningCertificate extends \Google\Model
{
  /**
   * The base64 urlsafe encoded SHA1 hash of the certificate. (This field is
   * deprecated in favor of SHA2-256. It should not be used and may be removed
   * at any time.)
   *
   * @var string
   */
  public $certificateHashSha1;
  /**
   * The base64 urlsafe encoded SHA2-256 hash of the certificate.
   *
   * @var string
   */
  public $certificateHashSha256;

  /**
   * The base64 urlsafe encoded SHA1 hash of the certificate. (This field is
   * deprecated in favor of SHA2-256. It should not be used and may be removed
   * at any time.)
   *
   * @param string $certificateHashSha1
   */
  public function setCertificateHashSha1($certificateHashSha1)
  {
    $this->certificateHashSha1 = $certificateHashSha1;
  }
  /**
   * @return string
   */
  public function getCertificateHashSha1()
  {
    return $this->certificateHashSha1;
  }
  /**
   * The base64 urlsafe encoded SHA2-256 hash of the certificate.
   *
   * @param string $certificateHashSha256
   */
  public function setCertificateHashSha256($certificateHashSha256)
  {
    $this->certificateHashSha256 = $certificateHashSha256;
  }
  /**
   * @return string
   */
  public function getCertificateHashSha256()
  {
    return $this->certificateHashSha256;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductSigningCertificate::class, 'Google_Service_AndroidEnterprise_ProductSigningCertificate');
