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

class AllowlistedCertificate extends \Google\Model
{
  /**
   * Required. PEM certificate that is allowlisted. The certificate can be up to
   * 5k bytes, and must be a parseable X.509 certificate.
   *
   * @var string
   */
  public $pemCertificate;

  /**
   * Required. PEM certificate that is allowlisted. The certificate can be up to
   * 5k bytes, and must be a parseable X.509 certificate.
   *
   * @param string $pemCertificate
   */
  public function setPemCertificate($pemCertificate)
  {
    $this->pemCertificate = $pemCertificate;
  }
  /**
   * @return string
   */
  public function getPemCertificate()
  {
    return $this->pemCertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllowlistedCertificate::class, 'Google_Service_CertificateManager_AllowlistedCertificate');
