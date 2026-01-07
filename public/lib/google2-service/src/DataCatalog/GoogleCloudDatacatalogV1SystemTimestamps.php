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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1SystemTimestamps extends \Google\Model
{
  /**
   * Creation timestamp of the resource within the given system.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Expiration timestamp of the resource within the given system.
   * Currently only applicable to BigQuery resources.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Timestamp of the last modification of the resource or its metadata within a
   * given system. Note: Depending on the source system, not every modification
   * updates this timestamp. For example, BigQuery timestamps every metadata
   * modification but not data or permission changes.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Creation timestamp of the resource within the given system.
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
   * Output only. Expiration timestamp of the resource within the given system.
   * Currently only applicable to BigQuery resources.
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
   * Timestamp of the last modification of the resource or its metadata within a
   * given system. Note: Depending on the source system, not every modification
   * updates this timestamp. For example, BigQuery timestamps every metadata
   * modification but not data or permission changes.
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
class_alias(GoogleCloudDatacatalogV1SystemTimestamps::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SystemTimestamps');
