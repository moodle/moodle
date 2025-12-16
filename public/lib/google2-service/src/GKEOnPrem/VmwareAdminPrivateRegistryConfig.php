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

namespace Google\Service\GKEOnPrem;

class VmwareAdminPrivateRegistryConfig extends \Google\Model
{
  /**
   * The registry address.
   *
   * @var string
   */
  public $address;
  /**
   * When the container runtime pulls an image from private registry, the
   * registry must prove its identity by presenting a certificate. The
   * registry's certificate is signed by a certificate authority (CA). The
   * container runtime uses the CA's certificate to validate the registry's
   * certificate.
   *
   * @var string
   */
  public $caCert;

  /**
   * The registry address.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * When the container runtime pulls an image from private registry, the
   * registry must prove its identity by presenting a certificate. The
   * registry's certificate is signed by a certificate authority (CA). The
   * container runtime uses the CA's certificate to validate the registry's
   * certificate.
   *
   * @param string $caCert
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminPrivateRegistryConfig::class, 'Google_Service_GKEOnPrem_VmwareAdminPrivateRegistryConfig');
