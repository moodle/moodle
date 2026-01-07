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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity extends \Google\Model
{
  /**
   * The count of deleted entity rows in the offline storage. Each row
   * corresponds to the combination of an entity ID and a timestamp. One entity
   * ID can have multiple rows in the offline storage.
   *
   * @var string
   */
  public $offlineStorageDeletedEntityRowCount;
  /**
   * The count of deleted entities in the online storage. Each entity ID
   * corresponds to one entity.
   *
   * @var string
   */
  public $onlineStorageDeletedEntityCount;

  /**
   * The count of deleted entity rows in the offline storage. Each row
   * corresponds to the combination of an entity ID and a timestamp. One entity
   * ID can have multiple rows in the offline storage.
   *
   * @param string $offlineStorageDeletedEntityRowCount
   */
  public function setOfflineStorageDeletedEntityRowCount($offlineStorageDeletedEntityRowCount)
  {
    $this->offlineStorageDeletedEntityRowCount = $offlineStorageDeletedEntityRowCount;
  }
  /**
   * @return string
   */
  public function getOfflineStorageDeletedEntityRowCount()
  {
    return $this->offlineStorageDeletedEntityRowCount;
  }
  /**
   * The count of deleted entities in the online storage. Each entity ID
   * corresponds to one entity.
   *
   * @param string $onlineStorageDeletedEntityCount
   */
  public function setOnlineStorageDeletedEntityCount($onlineStorageDeletedEntityCount)
  {
    $this->onlineStorageDeletedEntityCount = $onlineStorageDeletedEntityCount;
  }
  /**
   * @return string
   */
  public function getOnlineStorageDeletedEntityCount()
  {
    return $this->onlineStorageDeletedEntityCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectEntity');
