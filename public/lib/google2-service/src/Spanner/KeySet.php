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

class KeySet extends \Google\Collection
{
  protected $collection_key = 'ranges';
  /**
   * For convenience `all` can be set to `true` to indicate that this `KeySet`
   * matches all keys in the table or index. Note that any keys specified in
   * `keys` or `ranges` are only yielded once.
   *
   * @var bool
   */
  public $all;
  /**
   * A list of specific keys. Entries in `keys` should have exactly as many
   * elements as there are columns in the primary or index key with which this
   * `KeySet` is used. Individual key values are encoded as described here.
   *
   * @var array[]
   */
  public $keys;
  protected $rangesType = KeyRange::class;
  protected $rangesDataType = 'array';

  /**
   * For convenience `all` can be set to `true` to indicate that this `KeySet`
   * matches all keys in the table or index. Note that any keys specified in
   * `keys` or `ranges` are only yielded once.
   *
   * @param bool $all
   */
  public function setAll($all)
  {
    $this->all = $all;
  }
  /**
   * @return bool
   */
  public function getAll()
  {
    return $this->all;
  }
  /**
   * A list of specific keys. Entries in `keys` should have exactly as many
   * elements as there are columns in the primary or index key with which this
   * `KeySet` is used. Individual key values are encoded as described here.
   *
   * @param array[] $keys
   */
  public function setKeys($keys)
  {
    $this->keys = $keys;
  }
  /**
   * @return array[]
   */
  public function getKeys()
  {
    return $this->keys;
  }
  /**
   * A list of key ranges. See KeyRange for more information about key range
   * specifications.
   *
   * @param KeyRange[] $ranges
   */
  public function setRanges($ranges)
  {
    $this->ranges = $ranges;
  }
  /**
   * @return KeyRange[]
   */
  public function getRanges()
  {
    return $this->ranges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeySet::class, 'Google_Service_Spanner_KeySet');
