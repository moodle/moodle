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

namespace Google\Service\CloudKMS;

class EkmConnection extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const KEY_MANAGEMENT_MODE_KEY_MANAGEMENT_MODE_UNSPECIFIED = 'KEY_MANAGEMENT_MODE_UNSPECIFIED';
  /**
   * EKM-side key management operations on CryptoKeys created with this
   * EkmConnection must be initiated from the EKM directly and cannot be
   * performed from Cloud KMS. This means that: * When creating a
   * CryptoKeyVersion associated with this EkmConnection, the caller must supply
   * the key path of pre-existing external key material that will be linked to
   * the CryptoKeyVersion. * Destruction of external key material cannot be
   * requested via the Cloud KMS API and must be performed directly in the EKM.
   * * Automatic rotation of key material is not supported.
   */
  public const KEY_MANAGEMENT_MODE_MANUAL = 'MANUAL';
  /**
   * All CryptoKeys created with this EkmConnection use EKM-side key management
   * operations initiated from Cloud KMS. This means that: * When a
   * CryptoKeyVersion associated with this EkmConnection is created, the EKM
   * automatically generates new key material and a new key path. The caller
   * cannot supply the key path of pre-existing external key material. *
   * Destruction of external key material associated with this EkmConnection can
   * be requested by calling DestroyCryptoKeyVersion. * Automatic rotation of
   * key material is supported.
   */
  public const KEY_MANAGEMENT_MODE_CLOUD_KMS = 'CLOUD_KMS';
  protected $collection_key = 'serviceResolvers';
  /**
   * Output only. The time at which the EkmConnection was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Identifies the EKM Crypto Space that this EkmConnection maps to.
   * Note: This field is required if KeyManagementMode is CLOUD_KMS.
   *
   * @var string
   */
  public $cryptoSpacePath;
  /**
   * Optional. Etag of the currently stored EkmConnection.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Describes who can perform control plane operations on the EKM. If
   * unset, this defaults to MANUAL.
   *
   * @var string
   */
  public $keyManagementMode;
  /**
   * Output only. The resource name for the EkmConnection in the format
   * `projects/locations/ekmConnections`.
   *
   * @var string
   */
  public $name;
  protected $serviceResolversType = ServiceResolver::class;
  protected $serviceResolversDataType = 'array';

  /**
   * Output only. The time at which the EkmConnection was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Identifies the EKM Crypto Space that this EkmConnection maps to.
   * Note: This field is required if KeyManagementMode is CLOUD_KMS.
   *
   * @param string $cryptoSpacePath
   */
  public function setCryptoSpacePath($cryptoSpacePath)
  {
    $this->cryptoSpacePath = $cryptoSpacePath;
  }
  /**
   * @return string
   */
  public function getCryptoSpacePath()
  {
    return $this->cryptoSpacePath;
  }
  /**
   * Optional. Etag of the currently stored EkmConnection.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Describes who can perform control plane operations on the EKM. If
   * unset, this defaults to MANUAL.
   *
   * Accepted values: KEY_MANAGEMENT_MODE_UNSPECIFIED, MANUAL, CLOUD_KMS
   *
   * @param self::KEY_MANAGEMENT_MODE_* $keyManagementMode
   */
  public function setKeyManagementMode($keyManagementMode)
  {
    $this->keyManagementMode = $keyManagementMode;
  }
  /**
   * @return self::KEY_MANAGEMENT_MODE_*
   */
  public function getKeyManagementMode()
  {
    return $this->keyManagementMode;
  }
  /**
   * Output only. The resource name for the EkmConnection in the format
   * `projects/locations/ekmConnections`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. A list of ServiceResolvers where the EKM can be reached. There
   * should be one ServiceResolver per EKM replica. Currently, only a single
   * ServiceResolver is supported.
   *
   * @param ServiceResolver[] $serviceResolvers
   */
  public function setServiceResolvers($serviceResolvers)
  {
    $this->serviceResolvers = $serviceResolvers;
  }
  /**
   * @return ServiceResolver[]
   */
  public function getServiceResolvers()
  {
    return $this->serviceResolvers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EkmConnection::class, 'Google_Service_CloudKMS_EkmConnection');
