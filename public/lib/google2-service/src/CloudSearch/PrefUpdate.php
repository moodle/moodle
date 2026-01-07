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

namespace Google\Service\CloudSearch;

class PrefUpdate extends \Google\Model
{
  /**
   * @var string
   */
  public $name;
  protected $preStateType = FuseboxPrefUpdatePreState::class;
  protected $preStateDataType = '';
  protected $prefDeletedType = PrefDeleted::class;
  protected $prefDeletedDataType = '';
  protected $prefWrittenType = PrefWritten::class;
  protected $prefWrittenDataType = '';

  /**
   * @param string
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param FuseboxPrefUpdatePreState
   */
  public function setPreState(FuseboxPrefUpdatePreState $preState)
  {
    $this->preState = $preState;
  }
  /**
   * @return FuseboxPrefUpdatePreState
   */
  public function getPreState()
  {
    return $this->preState;
  }
  /**
   * @param PrefDeleted
   */
  public function setPrefDeleted(PrefDeleted $prefDeleted)
  {
    $this->prefDeleted = $prefDeleted;
  }
  /**
   * @return PrefDeleted
   */
  public function getPrefDeleted()
  {
    return $this->prefDeleted;
  }
  /**
   * @param PrefWritten
   */
  public function setPrefWritten(PrefWritten $prefWritten)
  {
    $this->prefWritten = $prefWritten;
  }
  /**
   * @return PrefWritten
   */
  public function getPrefWritten()
  {
    return $this->prefWritten;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrefUpdate::class, 'Google_Service_CloudSearch_PrefUpdate');
