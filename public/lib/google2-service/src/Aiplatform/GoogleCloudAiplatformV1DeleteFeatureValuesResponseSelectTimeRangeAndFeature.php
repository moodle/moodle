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

class GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature extends \Google\Model
{
  /**
   * The count of the features or columns impacted. This is the same as the
   * feature count in the request.
   *
   * @var string
   */
  public $impactedFeatureCount;
  /**
   * The count of modified entity rows in the offline storage. Each row
   * corresponds to the combination of an entity ID and a timestamp. One entity
   * ID can have multiple rows in the offline storage. Within each row, only the
   * features specified in the request are deleted.
   *
   * @var string
   */
  public $offlineStorageModifiedEntityRowCount;
  /**
   * The count of modified entities in the online storage. Each entity ID
   * corresponds to one entity. Within each entity, only the features specified
   * in the request are deleted.
   *
   * @var string
   */
  public $onlineStorageModifiedEntityCount;

  /**
   * The count of the features or columns impacted. This is the same as the
   * feature count in the request.
   *
   * @param string $impactedFeatureCount
   */
  public function setImpactedFeatureCount($impactedFeatureCount)
  {
    $this->impactedFeatureCount = $impactedFeatureCount;
  }
  /**
   * @return string
   */
  public function getImpactedFeatureCount()
  {
    return $this->impactedFeatureCount;
  }
  /**
   * The count of modified entity rows in the offline storage. Each row
   * corresponds to the combination of an entity ID and a timestamp. One entity
   * ID can have multiple rows in the offline storage. Within each row, only the
   * features specified in the request are deleted.
   *
   * @param string $offlineStorageModifiedEntityRowCount
   */
  public function setOfflineStorageModifiedEntityRowCount($offlineStorageModifiedEntityRowCount)
  {
    $this->offlineStorageModifiedEntityRowCount = $offlineStorageModifiedEntityRowCount;
  }
  /**
   * @return string
   */
  public function getOfflineStorageModifiedEntityRowCount()
  {
    return $this->offlineStorageModifiedEntityRowCount;
  }
  /**
   * The count of modified entities in the online storage. Each entity ID
   * corresponds to one entity. Within each entity, only the features specified
   * in the request are deleted.
   *
   * @param string $onlineStorageModifiedEntityCount
   */
  public function setOnlineStorageModifiedEntityCount($onlineStorageModifiedEntityCount)
  {
    $this->onlineStorageModifiedEntityCount = $onlineStorageModifiedEntityCount;
  }
  /**
   * @return string
   */
  public function getOnlineStorageModifiedEntityCount()
  {
    return $this->onlineStorageModifiedEntityCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeleteFeatureValuesResponseSelectTimeRangeAndFeature');
