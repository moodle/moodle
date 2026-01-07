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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EntryGroup extends \Google\Model
{
  /**
   * The default value. It is set for resources that were not subject for
   * migration from Data Catalog service.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_UNSPECIFIED = 'TRANSFER_STATUS_UNSPECIFIED';
  /**
   * Indicates that a resource was migrated from Data Catalog service but it
   * hasn't been transferred yet. In particular the resource cannot be updated
   * from Dataplex API.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_MIGRATED = 'TRANSFER_STATUS_MIGRATED';
  /**
   * Indicates that a resource was transferred from Data Catalog service. The
   * resource can only be updated from Dataplex API.
   */
  public const TRANSFER_STATUS_TRANSFER_STATUS_TRANSFERRED = 'TRANSFER_STATUS_TRANSFERRED';
  /**
   * Output only. The time when the EntryGroup was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the EntryGroup.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the service, and might be sent on update and
   * delete requests to ensure the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the EntryGroup.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the EntryGroup, in the format pr
   * ojects/{project_id_or_number}/locations/{location_id}/entryGroups/{entry_gr
   * oup_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Denotes the transfer status of the Entry Group. It is
   * unspecified for Entry Group created from Dataplex API.
   *
   * @var string
   */
  public $transferStatus;
  /**
   * Output only. System generated globally unique ID for the EntryGroup. If you
   * delete and recreate the EntryGroup with the same name, this ID will be
   * different.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the EntryGroup was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the EntryGroup was created.
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
   * Optional. Description of the EntryGroup.
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
   * Optional. User friendly display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * This checksum is computed by the service, and might be sent on update and
   * delete requests to ensure the client has an up-to-date value before
   * proceeding.
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
   * Optional. User-defined labels for the EntryGroup.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The relative resource name of the EntryGroup, in the format pr
   * ojects/{project_id_or_number}/locations/{location_id}/entryGroups/{entry_gr
   * oup_id}.
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
   * Output only. Denotes the transfer status of the Entry Group. It is
   * unspecified for Entry Group created from Dataplex API.
   *
   * Accepted values: TRANSFER_STATUS_UNSPECIFIED, TRANSFER_STATUS_MIGRATED,
   * TRANSFER_STATUS_TRANSFERRED
   *
   * @param self::TRANSFER_STATUS_* $transferStatus
   */
  public function setTransferStatus($transferStatus)
  {
    $this->transferStatus = $transferStatus;
  }
  /**
   * @return self::TRANSFER_STATUS_*
   */
  public function getTransferStatus()
  {
    return $this->transferStatus;
  }
  /**
   * Output only. System generated globally unique ID for the EntryGroup. If you
   * delete and recreate the EntryGroup with the same name, this ID will be
   * different.
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
   * Output only. The time when the EntryGroup was last updated.
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
class_alias(GoogleCloudDataplexV1EntryGroup::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntryGroup');
