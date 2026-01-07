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

class ConvertRowIdToColumn extends \Google\Model
{
  /**
   * Required. Only work on tables without primary key defined
   *
   * @var bool
   */
  public $onlyIfNoPrimaryKey;

  /**
   * Required. Only work on tables without primary key defined
   *
   * @param bool $onlyIfNoPrimaryKey
   */
  public function setOnlyIfNoPrimaryKey($onlyIfNoPrimaryKey)
  {
    $this->onlyIfNoPrimaryKey = $onlyIfNoPrimaryKey;
  }
  /**
   * @return bool
   */
  public function getOnlyIfNoPrimaryKey()
  {
    return $this->onlyIfNoPrimaryKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConvertRowIdToColumn::class, 'Google_Service_DatabaseMigrationService_ConvertRowIdToColumn');
