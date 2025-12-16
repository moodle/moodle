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

namespace Google\Service\Directory;

class UserEmailPublicKeyEncryptionCertificates extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "isDefault" => "is_default",
  ];
  /**
   * X.509 encryption certificate in `PEM` format. Must only be an end-entity
   * (leaf) certificate.
   *
   * @var string
   */
  public $certificate;
  /**
   * Whether this is the default certificate for the given email address.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Denotes the certificate's state in its lifecycle. Possible values are
   * `not_yet_validated`, `valid`, `invalid`, `expired`, and `revoked`.
   *
   * @var string
   */
  public $state;

  /**
   * X.509 encryption certificate in `PEM` format. Must only be an end-entity
   * (leaf) certificate.
   *
   * @param string $certificate
   */
  public function setCertificate($certificate)
  {
    $this->certificate = $certificate;
  }
  /**
   * @return string
   */
  public function getCertificate()
  {
    return $this->certificate;
  }
  /**
   * Whether this is the default certificate for the given email address.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Denotes the certificate's state in its lifecycle. Possible values are
   * `not_yet_validated`, `valid`, `invalid`, `expired`, and `revoked`.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserEmailPublicKeyEncryptionCertificates::class, 'Google_Service_Directory_UserEmailPublicKeyEncryptionCertificates');
