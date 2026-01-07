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

namespace Google\Service\DatabaseMigrationService;

class ApplyHash extends \Google\Model
{
  protected $uuidFromBytesType = DatamigrationEmpty::class;
  protected $uuidFromBytesDataType = '';

  /**
   * Optional. Generate UUID from the data's byte array
   *
   * @param DatamigrationEmpty $uuidFromBytes
   */
  public function setUuidFromBytes(DatamigrationEmpty $uuidFromBytes)
  {
    $this->uuidFromBytes = $uuidFromBytes;
  }
  /**
   * @return DatamigrationEmpty
   */
  public function getUuidFromBytes()
  {
    return $this->uuidFromBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplyHash::class, 'Google_Service_DatabaseMigrationService_ApplyHash');
