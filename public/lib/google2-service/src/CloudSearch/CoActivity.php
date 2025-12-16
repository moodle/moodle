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

class CoActivity extends \Google\Model
{
  /**
   * @var string
   */
  public $activityTitle;
  /**
   * @var string
   */
  public $addOnId;
  protected $addOnStartingStateType = AddOnStartingState::class;
  protected $addOnStartingStateDataType = '';
  /**
   * @var string
   */
  public $coActivityApp;
  /**
   * @var string
   */
  public $initiatorDeviceId;
  /**
   * @var string
   */
  public $presentationDeviceId;
  /**
   * @var string
   */
  public $projectNumber;

  /**
   * @param string
   */
  public function setActivityTitle($activityTitle)
  {
    $this->activityTitle = $activityTitle;
  }
  /**
   * @return string
   */
  public function getActivityTitle()
  {
    return $this->activityTitle;
  }
  /**
   * @param string
   */
  public function setAddOnId($addOnId)
  {
    $this->addOnId = $addOnId;
  }
  /**
   * @return string
   */
  public function getAddOnId()
  {
    return $this->addOnId;
  }
  /**
   * @param AddOnStartingState
   */
  public function setAddOnStartingState(AddOnStartingState $addOnStartingState)
  {
    $this->addOnStartingState = $addOnStartingState;
  }
  /**
   * @return AddOnStartingState
   */
  public function getAddOnStartingState()
  {
    return $this->addOnStartingState;
  }
  /**
   * @param string
   */
  public function setCoActivityApp($coActivityApp)
  {
    $this->coActivityApp = $coActivityApp;
  }
  /**
   * @return string
   */
  public function getCoActivityApp()
  {
    return $this->coActivityApp;
  }
  /**
   * @param string
   */
  public function setInitiatorDeviceId($initiatorDeviceId)
  {
    $this->initiatorDeviceId = $initiatorDeviceId;
  }
  /**
   * @return string
   */
  public function getInitiatorDeviceId()
  {
    return $this->initiatorDeviceId;
  }
  /**
   * @param string
   */
  public function setPresentationDeviceId($presentationDeviceId)
  {
    $this->presentationDeviceId = $presentationDeviceId;
  }
  /**
   * @return string
   */
  public function getPresentationDeviceId()
  {
    return $this->presentationDeviceId;
  }
  /**
   * @param string
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CoActivity::class, 'Google_Service_CloudSearch_CoActivity');
