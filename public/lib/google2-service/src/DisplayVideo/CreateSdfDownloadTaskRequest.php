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

class CreateSdfDownloadTaskRequest extends \Google\Model
{
  /**
   * SDF version value is not specified or is unknown in this version.
   */
  public const VERSION_SDF_VERSION_UNSPECIFIED = 'SDF_VERSION_UNSPECIFIED';
  /**
   * SDF version 3.1
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_3_1 = 'SDF_VERSION_3_1';
  /**
   * SDF version 4
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_4 = 'SDF_VERSION_4';
  /**
   * SDF version 4.1
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_4_1 = 'SDF_VERSION_4_1';
  /**
   * SDF version 4.2
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_4_2 = 'SDF_VERSION_4_2';
  /**
   * SDF version 5.
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5 = 'SDF_VERSION_5';
  /**
   * SDF version 5.1
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_1 = 'SDF_VERSION_5_1';
  /**
   * SDF version 5.2
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_2 = 'SDF_VERSION_5_2';
  /**
   * SDF version 5.3
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_3 = 'SDF_VERSION_5_3';
  /**
   * SDF version 5.4
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_4 = 'SDF_VERSION_5_4';
  /**
   * SDF version 5.5
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_5 = 'SDF_VERSION_5_5';
  /**
   * SDF version 6
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_6 = 'SDF_VERSION_6';
  /**
   * SDF version 7. Read the [v7 migration guide](/display-video/api/structured-
   * data-file/v7-migration-guide) before migrating to this version.
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_7 = 'SDF_VERSION_7';
  /**
   * SDF version 7.1. Read the [v7 migration guide](/display-
   * video/api/structured-data-file/v7-migration-guide) before migrating to this
   * version.
   */
  public const VERSION_SDF_VERSION_7_1 = 'SDF_VERSION_7_1';
  /**
   * SDF version 8. Read the [v8 migration guide](/display-video/api/structured-
   * data-file/v8-migration-guide) before migrating to this version.
   */
  public const VERSION_SDF_VERSION_8 = 'SDF_VERSION_8';
  /**
   * SDF version 8.1.
   */
  public const VERSION_SDF_VERSION_8_1 = 'SDF_VERSION_8_1';
  /**
   * SDF version 9. Read the [v9 migration guide](/display-video/api/structured-
   * data-file/v9-migration-guide) before migrating to this version.
   */
  public const VERSION_SDF_VERSION_9 = 'SDF_VERSION_9';
  /**
   * SDF version 9.1.
   */
  public const VERSION_SDF_VERSION_9_1 = 'SDF_VERSION_9_1';
  /**
   * SDF version 9.2.
   */
  public const VERSION_SDF_VERSION_9_2 = 'SDF_VERSION_9_2';
  /**
   * The ID of the advertiser to download SDF for.
   *
   * @var string
   */
  public $advertiserId;
  protected $idFilterType = IdFilter::class;
  protected $idFilterDataType = '';
  protected $inventorySourceFilterType = InventorySourceFilter::class;
  protected $inventorySourceFilterDataType = '';
  protected $parentEntityFilterType = ParentEntityFilter::class;
  protected $parentEntityFilterDataType = '';
  /**
   * The ID of the partner to download SDF for.
   *
   * @var string
   */
  public $partnerId;
  /**
   * Required. The SDF version of the downloaded file. If set to
   * `SDF_VERSION_UNSPECIFIED`, this will default to the version specified by
   * the advertiser or partner identified by `root_id`. An advertiser inherits
   * its SDF version from its partner unless configured otherwise.
   *
   * @var string
   */
  public $version;

  /**
   * The ID of the advertiser to download SDF for.
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
   * Filters on entities by their entity IDs.
   *
   * @param IdFilter $idFilter
   */
  public function setIdFilter(IdFilter $idFilter)
  {
    $this->idFilter = $idFilter;
  }
  /**
   * @return IdFilter
   */
  public function getIdFilter()
  {
    return $this->idFilter;
  }
  /**
   * Filters on Inventory Sources by their IDs.
   *
   * @param InventorySourceFilter $inventorySourceFilter
   */
  public function setInventorySourceFilter(InventorySourceFilter $inventorySourceFilter)
  {
    $this->inventorySourceFilter = $inventorySourceFilter;
  }
  /**
   * @return InventorySourceFilter
   */
  public function getInventorySourceFilter()
  {
    return $this->inventorySourceFilter;
  }
  /**
   * Filters on selected file types. The entities in each file are filtered by a
   * chosen set of filter entities. The filter entities must be the same type
   * as, or a parent type of, the selected file types.
   *
   * @param ParentEntityFilter $parentEntityFilter
   */
  public function setParentEntityFilter(ParentEntityFilter $parentEntityFilter)
  {
    $this->parentEntityFilter = $parentEntityFilter;
  }
  /**
   * @return ParentEntityFilter
   */
  public function getParentEntityFilter()
  {
    return $this->parentEntityFilter;
  }
  /**
   * The ID of the partner to download SDF for.
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
  /**
   * Required. The SDF version of the downloaded file. If set to
   * `SDF_VERSION_UNSPECIFIED`, this will default to the version specified by
   * the advertiser or partner identified by `root_id`. An advertiser inherits
   * its SDF version from its partner unless configured otherwise.
   *
   * Accepted values: SDF_VERSION_UNSPECIFIED, SDF_VERSION_3_1, SDF_VERSION_4,
   * SDF_VERSION_4_1, SDF_VERSION_4_2, SDF_VERSION_5, SDF_VERSION_5_1,
   * SDF_VERSION_5_2, SDF_VERSION_5_3, SDF_VERSION_5_4, SDF_VERSION_5_5,
   * SDF_VERSION_6, SDF_VERSION_7, SDF_VERSION_7_1, SDF_VERSION_8,
   * SDF_VERSION_8_1, SDF_VERSION_9, SDF_VERSION_9_1, SDF_VERSION_9_2
   *
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateSdfDownloadTaskRequest::class, 'Google_Service_DisplayVideo_CreateSdfDownloadTaskRequest');
