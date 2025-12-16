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

namespace Google\Service\Firestore;

class Cursor extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * If the position is just before or just after the given values, relative to
   * the sort order defined by the query.
   *
   * @var bool
   */
  public $before;
  protected $valuesType = Value::class;
  protected $valuesDataType = 'array';

  /**
   * If the position is just before or just after the given values, relative to
   * the sort order defined by the query.
   *
   * @param bool $before
   */
  public function setBefore($before)
  {
    $this->before = $before;
  }
  /**
   * @return bool
   */
  public function getBefore()
  {
    return $this->before;
  }
  /**
   * The values that represent a position, in the order they appear in the order
   * by clause of a query. Can contain fewer values than specified in the order
   * by clause.
   *
   * @param Value[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return Value[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cursor::class, 'Google_Service_Firestore_Cursor');
