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

namespace Google\Service\Appengine;

class ProjectState extends \Google\Model
{
  protected $currentReasonsType = Reasons::class;
  protected $currentReasonsDataType = '';
  protected $previousReasonsType = Reasons::class;
  protected $previousReasonsDataType = '';
  /**
   * @var string
   */
  public $state;

  /**
   * @param Reasons
   */
  public function setCurrentReasons(Reasons $currentReasons)
  {
    $this->currentReasons = $currentReasons;
  }
  /**
   * @return Reasons
   */
  public function getCurrentReasons()
  {
    return $this->currentReasons;
  }
  /**
   * @param Reasons
   */
  public function setPreviousReasons(Reasons $previousReasons)
  {
    $this->previousReasons = $previousReasons;
  }
  /**
   * @return Reasons
   */
  public function getPreviousReasons()
  {
    return $this->previousReasons;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectState::class, 'Google_Service_Appengine_ProjectState');
