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

namespace Google\Service\Vault;

class Export extends \Google\Model
{
  /**
   * The status is unspecified.
   */
  public const STATUS_EXPORT_STATUS_UNSPECIFIED = 'EXPORT_STATUS_UNSPECIFIED';
  /**
   * The export completed.
   */
  public const STATUS_COMPLETED = 'COMPLETED';
  /**
   * The export failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * The export is in progress.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  protected $cloudStorageSinkType = CloudStorageSink::class;
  protected $cloudStorageSinkDataType = '';
  /**
   * Output only. The time when the export was created.
   *
   * @var string
   */
  public $createTime;
  protected $exportOptionsType = ExportOptions::class;
  protected $exportOptionsDataType = '';
  /**
   * Output only. The generated export ID.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The matter ID.
   *
   * @var string
   */
  public $matterId;
  /**
   * The export name. Don't use special characters (~!$'(),;@:/?) in the name,
   * they can prevent you from downloading exports.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Identifies the parent export that spawned this child export.
   * This is only set on child exports.
   *
   * @var string
   */
  public $parentExportId;
  protected $queryType = Query::class;
  protected $queryDataType = '';
  protected $requesterType = UserInfo::class;
  protected $requesterDataType = '';
  protected $statsType = ExportStats::class;
  protected $statsDataType = '';
  /**
   * Output only. The status of the export.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. The sink for export files in Cloud Storage.
   *
   * @param CloudStorageSink $cloudStorageSink
   */
  public function setCloudStorageSink(CloudStorageSink $cloudStorageSink)
  {
    $this->cloudStorageSink = $cloudStorageSink;
  }
  /**
   * @return CloudStorageSink
   */
  public function getCloudStorageSink()
  {
    return $this->cloudStorageSink;
  }
  /**
   * Output only. The time when the export was created.
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
   * Additional export options.
   *
   * @param ExportOptions $exportOptions
   */
  public function setExportOptions(ExportOptions $exportOptions)
  {
    $this->exportOptions = $exportOptions;
  }
  /**
   * @return ExportOptions
   */
  public function getExportOptions()
  {
    return $this->exportOptions;
  }
  /**
   * Output only. The generated export ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The matter ID.
   *
   * @param string $matterId
   */
  public function setMatterId($matterId)
  {
    $this->matterId = $matterId;
  }
  /**
   * @return string
   */
  public function getMatterId()
  {
    return $this->matterId;
  }
  /**
   * The export name. Don't use special characters (~!$'(),;@:/?) in the name,
   * they can prevent you from downloading exports.
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
   * Output only. Identifies the parent export that spawned this child export.
   * This is only set on child exports.
   *
   * @param string $parentExportId
   */
  public function setParentExportId($parentExportId)
  {
    $this->parentExportId = $parentExportId;
  }
  /**
   * @return string
   */
  public function getParentExportId()
  {
    return $this->parentExportId;
  }
  /**
   * The query parameters used to create the export.
   *
   * @param Query $query
   */
  public function setQuery(Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Output only. The requester of the export.
   *
   * @param UserInfo $requester
   */
  public function setRequester(UserInfo $requester)
  {
    $this->requester = $requester;
  }
  /**
   * @return UserInfo
   */
  public function getRequester()
  {
    return $this->requester;
  }
  /**
   * Output only. Details about the export progress and size.
   *
   * @param ExportStats $stats
   */
  public function setStats(ExportStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return ExportStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * Output only. The status of the export.
   *
   * Accepted values: EXPORT_STATUS_UNSPECIFIED, COMPLETED, FAILED, IN_PROGRESS
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Export::class, 'Google_Service_Vault_Export');
