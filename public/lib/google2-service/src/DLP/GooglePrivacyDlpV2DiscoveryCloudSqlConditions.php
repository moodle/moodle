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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DiscoveryCloudSqlConditions extends \Google\Collection
{
  protected $collection_key = 'types';
  /**
   * Optional. Database engines that should be profiled. Optional. Defaults to
   * ALL_SUPPORTED_DATABASE_ENGINES if unspecified.
   *
   * @var string[]
   */
  public $databaseEngines;
  /**
   * Data profiles will only be generated for the database resource types
   * specified in this field. If not specified, defaults to
   * [DATABASE_RESOURCE_TYPE_ALL_SUPPORTED_TYPES].
   *
   * @var string[]
   */
  public $types;

  /**
   * Optional. Database engines that should be profiled. Optional. Defaults to
   * ALL_SUPPORTED_DATABASE_ENGINES if unspecified.
   *
   * @param string[] $databaseEngines
   */
  public function setDatabaseEngines($databaseEngines)
  {
    $this->databaseEngines = $databaseEngines;
  }
  /**
   * @return string[]
   */
  public function getDatabaseEngines()
  {
    return $this->databaseEngines;
  }
  /**
   * Data profiles will only be generated for the database resource types
   * specified in this field. If not specified, defaults to
   * [DATABASE_RESOURCE_TYPE_ALL_SUPPORTED_TYPES].
   *
   * @param string[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return string[]
   */
  public function getTypes()
  {
    return $this->types;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryCloudSqlConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryCloudSqlConditions');
