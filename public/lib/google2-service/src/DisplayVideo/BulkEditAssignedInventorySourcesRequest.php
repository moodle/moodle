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

namespace Google\Service\DisplayVideo;

class BulkEditAssignedInventorySourcesRequest extends \Google\Collection
{
  protected $collection_key = 'deletedAssignedInventorySources';
  /**
   * The ID of the advertiser that owns the parent inventory source group. The
   * parent partner does not have access to these assigned inventory sources.
   *
   * @var string
   */
  public $advertiserId;
  protected $createdAssignedInventorySourcesType = AssignedInventorySource::class;
  protected $createdAssignedInventorySourcesDataType = 'array';
  /**
   * The IDs of the assigned inventory sources to delete in bulk, specified as a
   * list of assigned_inventory_source_ids.
   *
   * @var string[]
   */
  public $deletedAssignedInventorySources;
  /**
   * The ID of the partner that owns the inventory source group. Only this
   * partner has write access to these assigned inventory sources.
   *
   * @var string
   */
  public $partnerId;

  /**
   * The ID of the advertiser that owns the parent inventory source group. The
   * parent partner does not have access to these assigned inventory sources.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * The assigned inventory sources to create in bulk, specified as a list of
   * AssignedInventorySources.
   *
   * @param AssignedInventorySource[] $createdAssignedInventorySources
   */
  public function setCreatedAssignedInventorySources($createdAssignedInventorySources)
  {
    $this->createdAssignedInventorySources = $createdAssignedInventorySources;
  }
  /**
   * @return AssignedInventorySource[]
   */
  public function getCreatedAssignedInventorySources()
  {
    return $this->createdAssignedInventorySources;
  }
  /**
   * The IDs of the assigned inventory sources to delete in bulk, specified as a
   * list of assigned_inventory_source_ids.
   *
   * @param string[] $deletedAssignedInventorySources
   */
  public function setDeletedAssignedInventorySources($deletedAssignedInventorySources)
  {
    $this->deletedAssignedInventorySources = $deletedAssignedInventorySources;
  }
  /**
   * @return string[]
   */
  public function getDeletedAssignedInventorySources()
  {
    return $this->deletedAssignedInventorySources;
  }
  /**
   * The ID of the partner that owns the inventory source group. Only this
   * partner has write access to these assigned inventory sources.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkEditAssignedInventorySourcesRequest::class, 'Google_Service_DisplayVideo_BulkEditAssignedInventorySourcesRequest');
