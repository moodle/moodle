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

namespace Google\Service\Container;

class UserManagedKeysConfig extends \Google\Collection
{
  protected $collection_key = 'serviceAccountVerificationKeys';
  /**
   * The Certificate Authority Service caPool to use for the aggregation CA in
   * this cluster.
   *
   * @var string
   */
  public $aggregationCa;
  /**
   * The Certificate Authority Service caPool to use for the cluster CA in this
   * cluster.
   *
   * @var string
   */
  public $clusterCa;
  /**
   * The Cloud KMS cryptoKey to use for Confidential Hyperdisk on the control
   * plane nodes.
   *
   * @var string
   */
  public $controlPlaneDiskEncryptionKey;
  /**
   * Output only. All of the versions of the Cloud KMS cryptoKey that are used
   * by Confidential Hyperdisks on the control plane nodes.
   *
   * @var string[]
   */
  public $controlPlaneDiskEncryptionKeyVersions;
  /**
   * Resource path of the Certificate Authority Service caPool to use for the
   * etcd API CA in this cluster.
   *
   * @var string
   */
  public $etcdApiCa;
  /**
   * Resource path of the Certificate Authority Service caPool to use for the
   * etcd peer CA in this cluster.
   *
   * @var string
   */
  public $etcdPeerCa;
  /**
   * Resource path of the Cloud KMS cryptoKey to use for encryption of internal
   * etcd backups.
   *
   * @var string
   */
  public $gkeopsEtcdBackupEncryptionKey;
  /**
   * The Cloud KMS cryptoKeyVersions to use for signing service account JWTs
   * issued by this cluster. Format: `projects/{project}/locations/{location}/ke
   * yRings/{keyring}/cryptoKeys/{cryptoKey}/cryptoKeyVersions/{cryptoKeyVersion
   * }`
   *
   * @var string[]
   */
  public $serviceAccountSigningKeys;
  /**
   * The Cloud KMS cryptoKeyVersions to use for verifying service account JWTs
   * issued by this cluster. Format: `projects/{project}/locations/{location}/ke
   * yRings/{keyring}/cryptoKeys/{cryptoKey}/cryptoKeyVersions/{cryptoKeyVersion
   * }`
   *
   * @var string[]
   */
  public $serviceAccountVerificationKeys;

  /**
   * The Certificate Authority Service caPool to use for the aggregation CA in
   * this cluster.
   *
   * @param string $aggregationCa
   */
  public function setAggregationCa($aggregationCa)
  {
    $this->aggregationCa = $aggregationCa;
  }
  /**
   * @return string
   */
  public function getAggregationCa()
  {
    return $this->aggregationCa;
  }
  /**
   * The Certificate Authority Service caPool to use for the cluster CA in this
   * cluster.
   *
   * @param string $clusterCa
   */
  public function setClusterCa($clusterCa)
  {
    $this->clusterCa = $clusterCa;
  }
  /**
   * @return string
   */
  public function getClusterCa()
  {
    return $this->clusterCa;
  }
  /**
   * The Cloud KMS cryptoKey to use for Confidential Hyperdisk on the control
   * plane nodes.
   *
   * @param string $controlPlaneDiskEncryptionKey
   */
  public function setControlPlaneDiskEncryptionKey($controlPlaneDiskEncryptionKey)
  {
    $this->controlPlaneDiskEncryptionKey = $controlPlaneDiskEncryptionKey;
  }
  /**
   * @return string
   */
  public function getControlPlaneDiskEncryptionKey()
  {
    return $this->controlPlaneDiskEncryptionKey;
  }
  /**
   * Output only. All of the versions of the Cloud KMS cryptoKey that are used
   * by Confidential Hyperdisks on the control plane nodes.
   *
   * @param string[] $controlPlaneDiskEncryptionKeyVersions
   */
  public function setControlPlaneDiskEncryptionKeyVersions($controlPlaneDiskEncryptionKeyVersions)
  {
    $this->controlPlaneDiskEncryptionKeyVersions = $controlPlaneDiskEncryptionKeyVersions;
  }
  /**
   * @return string[]
   */
  public function getControlPlaneDiskEncryptionKeyVersions()
  {
    return $this->controlPlaneDiskEncryptionKeyVersions;
  }
  /**
   * Resource path of the Certificate Authority Service caPool to use for the
   * etcd API CA in this cluster.
   *
   * @param string $etcdApiCa
   */
  public function setEtcdApiCa($etcdApiCa)
  {
    $this->etcdApiCa = $etcdApiCa;
  }
  /**
   * @return string
   */
  public function getEtcdApiCa()
  {
    return $this->etcdApiCa;
  }
  /**
   * Resource path of the Certificate Authority Service caPool to use for the
   * etcd peer CA in this cluster.
   *
   * @param string $etcdPeerCa
   */
  public function setEtcdPeerCa($etcdPeerCa)
  {
    $this->etcdPeerCa = $etcdPeerCa;
  }
  /**
   * @return string
   */
  public function getEtcdPeerCa()
  {
    return $this->etcdPeerCa;
  }
  /**
   * Resource path of the Cloud KMS cryptoKey to use for encryption of internal
   * etcd backups.
   *
   * @param string $gkeopsEtcdBackupEncryptionKey
   */
  public function setGkeopsEtcdBackupEncryptionKey($gkeopsEtcdBackupEncryptionKey)
  {
    $this->gkeopsEtcdBackupEncryptionKey = $gkeopsEtcdBackupEncryptionKey;
  }
  /**
   * @return string
   */
  public function getGkeopsEtcdBackupEncryptionKey()
  {
    return $this->gkeopsEtcdBackupEncryptionKey;
  }
  /**
   * The Cloud KMS cryptoKeyVersions to use for signing service account JWTs
   * issued by this cluster. Format: `projects/{project}/locations/{location}/ke
   * yRings/{keyring}/cryptoKeys/{cryptoKey}/cryptoKeyVersions/{cryptoKeyVersion
   * }`
   *
   * @param string[] $serviceAccountSigningKeys
   */
  public function setServiceAccountSigningKeys($serviceAccountSigningKeys)
  {
    $this->serviceAccountSigningKeys = $serviceAccountSigningKeys;
  }
  /**
   * @return string[]
   */
  public function getServiceAccountSigningKeys()
  {
    return $this->serviceAccountSigningKeys;
  }
  /**
   * The Cloud KMS cryptoKeyVersions to use for verifying service account JWTs
   * issued by this cluster. Format: `projects/{project}/locations/{location}/ke
   * yRings/{keyring}/cryptoKeys/{cryptoKey}/cryptoKeyVersions/{cryptoKeyVersion
   * }`
   *
   * @param string[] $serviceAccountVerificationKeys
   */
  public function setServiceAccountVerificationKeys($serviceAccountVerificationKeys)
  {
    $this->serviceAccountVerificationKeys = $serviceAccountVerificationKeys;
  }
  /**
   * @return string[]
   */
  public function getServiceAccountVerificationKeys()
  {
    return $this->serviceAccountVerificationKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserManagedKeysConfig::class, 'Google_Service_Container_UserManagedKeysConfig');
