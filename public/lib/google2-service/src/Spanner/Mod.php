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

class Mod extends \Google\Collection
{
  protected $collection_key = 'oldValues';
  protected $keysType = ModValue::class;
  protected $keysDataType = 'array';
  protected $newValuesType = ModValue::class;
  protected $newValuesDataType = 'array';
  protected $oldValuesType = ModValue::class;
  protected $oldValuesDataType = 'array';

  /**
   * Returns the value of the primary key of the modified row.
   *
   * @param ModValue[] $keys
   */
  public function setKeys($keys)
  {
    $this->keys = $keys;
  }
  /**
   * @return ModValue[]
   */
  public function getKeys()
  {
    return $this->keys;
  }
  /**
   * Returns the new values after the change for the modified columns. Always
   * empty for DELETE.
   *
   * @param ModValue[] $newValues
   */
  public function setNewValues($newValues)
  {
    $this->newValues = $newValues;
  }
  /**
   * @return ModValue[]
   */
  public function getNewValues()
  {
    return $this->newValues;
  }
  /**
   * Returns the old values before the change for the modified columns. Always
   * empty for INSERT, or if old values are not being captured specified by
   * value_capture_type.
   *
   * @param ModValue[] $oldValues
   */
  public function setOldValues($oldValues)
  {
    $this->oldValues = $oldValues;
  }
  /**
   * @return ModValue[]
   */
  public function getOldValues()
  {
    return $this->oldValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Mod::class, 'Google_Service_Spanner_Mod');
