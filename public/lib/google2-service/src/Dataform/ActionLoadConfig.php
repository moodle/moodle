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

namespace Google\Service\Dataform;

class ActionLoadConfig extends \Google\Model
{
  protected $appendType = ActionSimpleLoadMode::class;
  protected $appendDataType = '';
  protected $maximumType = ActionIncrementalLoadMode::class;
  protected $maximumDataType = '';
  protected $replaceType = ActionSimpleLoadMode::class;
  protected $replaceDataType = '';
  protected $uniqueType = ActionIncrementalLoadMode::class;
  protected $uniqueDataType = '';

  /**
   * Append into destination table
   *
   * @param ActionSimpleLoadMode $append
   */
  public function setAppend(ActionSimpleLoadMode $append)
  {
    $this->append = $append;
  }
  /**
   * @return ActionSimpleLoadMode
   */
  public function getAppend()
  {
    return $this->append;
  }
  /**
   * Insert records where the value exceeds the previous maximum value for a
   * column in the destination table
   *
   * @param ActionIncrementalLoadMode $maximum
   */
  public function setMaximum(ActionIncrementalLoadMode $maximum)
  {
    $this->maximum = $maximum;
  }
  /**
   * @return ActionIncrementalLoadMode
   */
  public function getMaximum()
  {
    return $this->maximum;
  }
  /**
   * Replace destination table
   *
   * @param ActionSimpleLoadMode $replace
   */
  public function setReplace(ActionSimpleLoadMode $replace)
  {
    $this->replace = $replace;
  }
  /**
   * @return ActionSimpleLoadMode
   */
  public function getReplace()
  {
    return $this->replace;
  }
  /**
   * Insert records where the value of a column is not already present in the
   * destination table
   *
   * @param ActionIncrementalLoadMode $unique
   */
  public function setUnique(ActionIncrementalLoadMode $unique)
  {
    $this->unique = $unique;
  }
  /**
   * @return ActionIncrementalLoadMode
   */
  public function getUnique()
  {
    return $this->unique;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionLoadConfig::class, 'Google_Service_Dataform_ActionLoadConfig');
