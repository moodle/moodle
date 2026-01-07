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

namespace Google\Service\Spanner;

class SplitPoints extends \Google\Collection
{
  protected $collection_key = 'keys';
  /**
   * Optional. The expiration timestamp of the split points. A timestamp in the
   * past means immediate expiration. The maximum value can be 30 days in the
   * future. Defaults to 10 days in the future if not specified.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The index to split. If specified, the `table` field must refer to the
   * index's base table.
   *
   * @var string
   */
  public $index;
  protected $keysType = Key::class;
  protected $keysDataType = 'array';
  /**
   * The table to split.
   *
   * @var string
   */
  public $table;

  /**
   * Optional. The expiration timestamp of the split points. A timestamp in the
   * past means immediate expiration. The maximum value can be 30 days in the
   * future. Defaults to 10 days in the future if not specified.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * The index to split. If specified, the `table` field must refer to the
   * index's base table.
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Required. The list of split keys. In essence, the split boundaries.
   *
   * @param Key[] $keys
   */
  public function setKeys($keys)
  {
    $this->keys = $keys;
  }
  /**
   * @return Key[]
   */
  public function getKeys()
  {
    return $this->keys;
  }
  /**
   * The table to split.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SplitPoints::class, 'Google_Service_Spanner_SplitPoints');
