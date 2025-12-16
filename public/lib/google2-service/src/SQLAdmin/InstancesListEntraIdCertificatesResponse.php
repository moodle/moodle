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

namespace Google\Service\SQLAdmin;

class InstancesListEntraIdCertificatesResponse extends \Google\Collection
{
  protected $collection_key = 'certs';
  /**
   * The `sha1_fingerprint` of the active certificate from `certs`.
   *
   * @var string
   */
  public $activeVersion;
  protected $certsType = SslCert::class;
  protected $certsDataType = 'array';
  /**
   * This is always `sql#instancesListEntraIdCertificates`.
   *
   * @var string
   */
  public $kind;

  /**
   * The `sha1_fingerprint` of the active certificate from `certs`.
   *
   * @param string $activeVersion
   */
  public function setActiveVersion($activeVersion)
  {
    $this->activeVersion = $activeVersion;
  }
  /**
   * @return string
   */
  public function getActiveVersion()
  {
    return $this->activeVersion;
  }
  /**
   * List of Entra ID certificates for the instance.
   *
   * @param SslCert[] $certs
   */
  public function setCerts($certs)
  {
    $this->certs = $certs;
  }
  /**
   * @return SslCert[]
   */
  public function getCerts()
  {
    return $this->certs;
  }
  /**
   * This is always `sql#instancesListEntraIdCertificates`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesListEntraIdCertificatesResponse::class, 'Google_Service_SQLAdmin_InstancesListEntraIdCertificatesResponse');
