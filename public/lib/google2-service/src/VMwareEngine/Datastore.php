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

class Datastore extends \Google\Collection
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The NFS volume is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The NFS volume is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The NFS volume is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The NFS volume is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  protected $collection_key = 'clusters';
  /**
   * Output only. Clusters to which the datastore is attached.
   *
   * @var string[]
   */
  public $clusters;
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description for this datastore
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Checksum that may be sent on update and delete requests to ensure
   * that the user-provided value is up to date before the server processes a
   * request. The server computes checksums based on the value of other fields
   * in the request.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Identifier. The resource name of this datastore. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastores/datastore`
   *
   * @var string
   */
  public $name;
  protected $nfsDatastoreType = NfsDatastore::class;
  protected $nfsDatastoreDataType = '';
  /**
   * Output only. The state of the Datastore.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Clusters to which the datastore is attached.
   *
   * @param string[] $clusters
   */
  public function setClusters($clusters)
  {
    $this->clusters = $clusters;
  }
  /**
   * @return string[]
   */
  public function getClusters()
  {
    return $this->clusters;
  }
  /**
   * Output only. Creation time of this resource.
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
   * Optional. User-provided description for this datastore
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Checksum that may be sent on update and delete requests to ensure
   * that the user-provided value is up to date before the server processes a
   * request. The server computes checksums based on the value of other fields
   * in the request.
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
   * Output only. Identifier. The resource name of this datastore. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/datastores/datastore`
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
   * Required. Settings for the NFS datastore.
   *
   * @param NfsDatastore $nfsDatastore
   */
  public function setNfsDatastore(NfsDatastore $nfsDatastore)
  {
    $this->nfsDatastore = $nfsDatastore;
  }
  /**
   * @return NfsDatastore
   */
  public function getNfsDatastore()
  {
    return $this->nfsDatastore;
  }
  /**
   * Output only. The state of the Datastore.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, UPDATING, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Datastore::class, 'Google_Service_VMwareEngine_Datastore');
