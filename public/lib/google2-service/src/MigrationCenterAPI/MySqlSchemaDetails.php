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

namespace Google\Service\MigrationCenterAPI;

class MySqlSchemaDetails extends \Google\Collection
{
  protected $collection_key = 'storageEngines';
  protected $storageEnginesType = MySqlStorageEngineDetails::class;
  protected $storageEnginesDataType = 'array';

  /**
   * Optional. Mysql storage engine tables.
   *
   * @param MySqlStorageEngineDetails[] $storageEngines
   */
  public function setStorageEngines($storageEngines)
  {
    $this->storageEngines = $storageEngines;
  }
  /**
   * @return MySqlStorageEngineDetails[]
   */
  public function getStorageEngines()
  {
    return $this->storageEngines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MySqlSchemaDetails::class, 'Google_Service_MigrationCenterAPI_MySqlSchemaDetails');
