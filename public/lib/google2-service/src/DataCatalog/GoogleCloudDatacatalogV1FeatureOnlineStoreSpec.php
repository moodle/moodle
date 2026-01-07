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

class GoogleCloudDatacatalogV1FeatureOnlineStoreSpec extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const STORAGE_TYPE_STORAGE_TYPE_UNSPECIFIED = 'STORAGE_TYPE_UNSPECIFIED';
  /**
   * Underlsying storgae is Bigtable.
   */
  public const STORAGE_TYPE_BIGTABLE = 'BIGTABLE';
  /**
   * Underlying is optimized online server (Lightning).
   */
  public const STORAGE_TYPE_OPTIMIZED = 'OPTIMIZED';
  /**
   * Output only. Type of underlying storage for the FeatureOnlineStore.
   *
   * @var string
   */
  public $storageType;

  /**
   * Output only. Type of underlying storage for the FeatureOnlineStore.
   *
   * Accepted values: STORAGE_TYPE_UNSPECIFIED, BIGTABLE, OPTIMIZED
   *
   * @param self::STORAGE_TYPE_* $storageType
   */
  public function setStorageType($storageType)
  {
    $this->storageType = $storageType;
  }
  /**
   * @return self::STORAGE_TYPE_*
   */
  public function getStorageType()
  {
    return $this->storageType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1FeatureOnlineStoreSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1FeatureOnlineStoreSpec');
