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

namespace Google\Service\BigtableAdmin;

class TieredStorageConfig extends \Google\Model
{
  protected $infrequentAccessType = TieredStorageRule::class;
  protected $infrequentAccessDataType = '';

  /**
   * Rule to specify what data is stored in the infrequent access(IA) tier. The
   * IA tier allows storing more data per node with reduced performance.
   *
   * @param TieredStorageRule $infrequentAccess
   */
  public function setInfrequentAccess(TieredStorageRule $infrequentAccess)
  {
    $this->infrequentAccess = $infrequentAccess;
  }
  /**
   * @return TieredStorageRule
   */
  public function getInfrequentAccess()
  {
    return $this->infrequentAccess;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TieredStorageConfig::class, 'Google_Service_BigtableAdmin_TieredStorageConfig');
