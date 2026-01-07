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

class GoogleCloudDatacatalogV1ImportEntriesResponse extends \Google\Model
{
  /**
   * Number of entries deleted as a result of import operation.
   *
   * @var string
   */
  public $deletedEntriesCount;
  /**
   * Cumulative number of entries created and entries updated as a result of
   * import operation.
   *
   * @var string
   */
  public $upsertedEntriesCount;

  /**
   * Number of entries deleted as a result of import operation.
   *
   * @param string $deletedEntriesCount
   */
  public function setDeletedEntriesCount($deletedEntriesCount)
  {
    $this->deletedEntriesCount = $deletedEntriesCount;
  }
  /**
   * @return string
   */
  public function getDeletedEntriesCount()
  {
    return $this->deletedEntriesCount;
  }
  /**
   * Cumulative number of entries created and entries updated as a result of
   * import operation.
   *
   * @param string $upsertedEntriesCount
   */
  public function setUpsertedEntriesCount($upsertedEntriesCount)
  {
    $this->upsertedEntriesCount = $upsertedEntriesCount;
  }
  /**
   * @return string
   */
  public function getUpsertedEntriesCount()
  {
    return $this->upsertedEntriesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1ImportEntriesResponse::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ImportEntriesResponse');
