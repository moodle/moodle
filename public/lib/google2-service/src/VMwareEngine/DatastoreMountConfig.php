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

namespace Google\Service\VMwareEngine;

class DatastoreMountConfig extends \Google\Collection
{
  /**
   * The default value. This value should never be used.
   */
  public const ACCESS_MODE_ACCESS_MODE_UNSPECIFIED = 'ACCESS_MODE_UNSPECIFIED';
  /**
   * NFS is accessed by hosts in read mode
   */
  public const ACCESS_MODE_READ_ONLY = 'READ_ONLY';
  /**
   * NFS is accessed by hosts in read and write mode
   */
  public const ACCESS_MODE_READ_WRITE = 'READ_WRITE';
  /**
   * The default value. This value should never be used.
   */
  public const NFS_VERSION_NFS_VERSION_UNSPECIFIED = 'NFS_VERSION_UNSPECIFIED';
  /**
   * NFS 3
   */
  public const NFS_VERSION_NFS_V3 = 'NFS_V3';
  /**
   * The default value. This value should never be used.
   */
  public const SECURITY_TYPE_SECURITY_TYPE_UNSPECIFIED = 'SECURITY_TYPE_UNSPECIFIED';
  protected $collection_key = 'servers';
  /**
   * Optional. NFS is accessed by hosts in read mode Optional. Default value
   * used will be READ_WRITE
   *
   * @var string
   */
  public $accessMode;
  /**
   * Required. The resource name of the datastore to unmount. The datastore
   * requested to be mounted should be in same region/zone as the cluster.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastores/my-datastore`
   *
   * @var string
   */
  public $datastore;
  protected $datastoreNetworkType = DatastoreNetwork::class;
  protected $datastoreNetworkDataType = '';
  /**
   * Output only. File share name.
   *
   * @var string
   */
  public $fileShare;
  /**
   * Optional. The NFS protocol supported by the NFS volume. Default value used
   * will be NFS_V3
   *
   * @var string
   */
  public $nfsVersion;
  /**
   * Optional. ONLY required when NFS 4.1 version is used
   *
   * @var string
   */
  public $securityType;
  /**
   * Output only. Server IP addresses of the NFS volume. For NFS 3, you can only
   * provide a single server IP address or DNS names.
   *
   * @var string[]
   */
  public $servers;

  /**
   * Optional. NFS is accessed by hosts in read mode Optional. Default value
   * used will be READ_WRITE
   *
   * Accepted values: ACCESS_MODE_UNSPECIFIED, READ_ONLY, READ_WRITE
   *
   * @param self::ACCESS_MODE_* $accessMode
   */
  public function setAccessMode($accessMode)
  {
    $this->accessMode = $accessMode;
  }
  /**
   * @return self::ACCESS_MODE_*
   */
  public function getAccessMode()
  {
    return $this->accessMode;
  }
  /**
   * Required. The resource name of the datastore to unmount. The datastore
   * requested to be mounted should be in same region/zone as the cluster.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastores/my-datastore`
   *
   * @param string $datastore
   */
  public function setDatastore($datastore)
  {
    $this->datastore = $datastore;
  }
  /**
   * @return string
   */
  public function getDatastore()
  {
    return $this->datastore;
  }
  /**
   * Required. The network configuration for the datastore.
   *
   * @param DatastoreNetwork $datastoreNetwork
   */
  public function setDatastoreNetwork(DatastoreNetwork $datastoreNetwork)
  {
    $this->datastoreNetwork = $datastoreNetwork;
  }
  /**
   * @return DatastoreNetwork
   */
  public function getDatastoreNetwork()
  {
    return $this->datastoreNetwork;
  }
  /**
   * Output only. File share name.
   *
   * @param string $fileShare
   */
  public function setFileShare($fileShare)
  {
    $this->fileShare = $fileShare;
  }
  /**
   * @return string
   */
  public function getFileShare()
  {
    return $this->fileShare;
  }
  /**
   * Optional. The NFS protocol supported by the NFS volume. Default value used
   * will be NFS_V3
   *
   * Accepted values: NFS_VERSION_UNSPECIFIED, NFS_V3
   *
   * @param self::NFS_VERSION_* $nfsVersion
   */
  public function setNfsVersion($nfsVersion)
  {
    $this->nfsVersion = $nfsVersion;
  }
  /**
   * @return self::NFS_VERSION_*
   */
  public function getNfsVersion()
  {
    return $this->nfsVersion;
  }
  /**
   * Optional. ONLY required when NFS 4.1 version is used
   *
   * Accepted values: SECURITY_TYPE_UNSPECIFIED
   *
   * @param self::SECURITY_TYPE_* $securityType
   */
  public function setSecurityType($securityType)
  {
    $this->securityType = $securityType;
  }
  /**
   * @return self::SECURITY_TYPE_*
   */
  public function getSecurityType()
  {
    return $this->securityType;
  }
  /**
   * Output only. Server IP addresses of the NFS volume. For NFS 3, you can only
   * provide a single server IP address or DNS names.
   *
   * @param string[] $servers
   */
  public function setServers($servers)
  {
    $this->servers = $servers;
  }
  /**
   * @return string[]
   */
  public function getServers()
  {
    return $this->servers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatastoreMountConfig::class, 'Google_Service_VMwareEngine_DatastoreMountConfig');
