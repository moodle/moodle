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

namespace Google\Service\BigLakeService;

class Table extends \Google\Model
{
  /**
   * The type is not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Represents a table compatible with Hive Metastore tables.
   */
  public const TYPE_HIVE = 'HIVE';
  /**
   * Output only. The creation time of the table.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The deletion time of the table. Only set after the table is
   * deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * The checksum of a table object computed by the server based on the value of
   * other fields. It may be sent on update requests to ensure the client has an
   * up-to-date value before proceeding. It is only checked for update table
   * operations.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The time when this table is considered expired. Only set after
   * the table is deleted.
   *
   * @var string
   */
  public $expireTime;
  protected $hiveOptionsType = HiveTableOptions::class;
  protected $hiveOptionsDataType = '';
  /**
   * Output only. The resource name. Format: projects/{project_id_or_number}/loc
   * ations/{location_id}/catalogs/{catalog_id}/databases/{database_id}/tables/{
   * table_id}
   *
   * @var string
   */
  public $name;
  /**
   * The table type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The last modification time of the table.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation time of the table.
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
   * Output only. The deletion time of the table. Only set after the table is
   * deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * The checksum of a table object computed by the server based on the value of
   * other fields. It may be sent on update requests to ensure the client has an
   * up-to-date value before proceeding. It is only checked for update table
   * operations.
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
   * Output only. The time when this table is considered expired. Only set after
   * the table is deleted.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Options of a Hive table.
   *
   * @param HiveTableOptions $hiveOptions
   */
  public function setHiveOptions(HiveTableOptions $hiveOptions)
  {
    $this->hiveOptions = $hiveOptions;
  }
  /**
   * @return HiveTableOptions
   */
  public function getHiveOptions()
  {
    return $this->hiveOptions;
  }
  /**
   * Output only. The resource name. Format: projects/{project_id_or_number}/loc
   * ations/{location_id}/catalogs/{catalog_id}/databases/{database_id}/tables/{
   * table_id}
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
   * The table type.
   *
   * Accepted values: TYPE_UNSPECIFIED, HIVE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The last modification time of the table.
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
class_alias(Table::class, 'Google_Service_BigLakeService_Table');
