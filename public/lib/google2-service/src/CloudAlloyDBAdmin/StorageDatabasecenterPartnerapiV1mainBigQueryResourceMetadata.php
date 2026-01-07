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

namespace Google\Service\CloudAlloyDBAdmin;

class StorageDatabasecenterPartnerapiV1mainBigQueryResourceMetadata extends \Google\Model
{
  /**
   * The creation time of the resource, i.e. the time when resource is created
   * and recorded in partner service.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Full resource name of this instance.
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * Required. location of the resource
   *
   * @var string
   */
  public $location;
  protected $productType = StorageDatabasecenterProtoCommonProduct::class;
  protected $productDataType = '';
  /**
   * Closest parent Cloud Resource Manager container of this resource. It must
   * be resource name of a Cloud Resource Manager project with the format of
   * "/", such as "projects/123". For GCP provided resources, number should be
   * project number.
   *
   * @var string
   */
  public $resourceContainer;
  protected $resourceIdType = StorageDatabasecenterPartnerapiV1mainDatabaseResourceId::class;
  protected $resourceIdDataType = '';
  /**
   * The time at which the resource was updated and recorded at partner service.
   *
   * @var string
   */
  public $updateTime;
  protected $userLabelSetType = StorageDatabasecenterPartnerapiV1mainUserLabels::class;
  protected $userLabelSetDataType = '';

  /**
   * The creation time of the resource, i.e. the time when resource is created
   * and recorded in partner service.
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
   * Required. Full resource name of this instance.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * Required. location of the resource
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The product this resource represents.
   *
   * @param StorageDatabasecenterProtoCommonProduct $product
   */
  public function setProduct(StorageDatabasecenterProtoCommonProduct $product)
  {
    $this->product = $product;
  }
  /**
   * @return StorageDatabasecenterProtoCommonProduct
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Closest parent Cloud Resource Manager container of this resource. It must
   * be resource name of a Cloud Resource Manager project with the format of
   * "/", such as "projects/123". For GCP provided resources, number should be
   * project number.
   *
   * @param string $resourceContainer
   */
  public function setResourceContainer($resourceContainer)
  {
    $this->resourceContainer = $resourceContainer;
  }
  /**
   * @return string
   */
  public function getResourceContainer()
  {
    return $this->resourceContainer;
  }
  /**
   * Required. Database resource id.
   *
   * @param StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId
   */
  public function setResourceId(StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainDatabaseResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * The time at which the resource was updated and recorded at partner service.
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
  /**
   * User-provided labels associated with the resource
   *
   * @param StorageDatabasecenterPartnerapiV1mainUserLabels $userLabelSet
   */
  public function setUserLabelSet(StorageDatabasecenterPartnerapiV1mainUserLabels $userLabelSet)
  {
    $this->userLabelSet = $userLabelSet;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainUserLabels
   */
  public function getUserLabelSet()
  {
    return $this->userLabelSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainBigQueryResourceMetadata::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainBigQueryResourceMetadata');
