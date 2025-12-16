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

namespace Google\Service\Datastream;

class MysqlGtidPosition extends \Google\Model
{
  /**
   * Required. The gtid set to start replication from.
   *
   * @var string
   */
  public $gtidSet;

  /**
   * Required. The gtid set to start replication from.
   *
   * @param string $gtidSet
   */
  public function setGtidSet($gtidSet)
  {
    $this->gtidSet = $gtidSet;
  }
  /**
   * @return string
   */
  public function getGtidSet()
  {
    return $this->gtidSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MysqlGtidPosition::class, 'Google_Service_Datastream_MysqlGtidPosition');
