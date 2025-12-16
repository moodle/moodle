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

namespace Google\Service\Directory;

class ChromeOsDeviceAction extends \Google\Model
{
  /**
   * Action to be taken on the Chrome OS device.
   *
   * @var string
   */
  public $action;
  /**
   * Only used when the action is `deprovision`. With the `deprovision` action,
   * this field is required. *Note*: The deprovision reason is audited because
   * it might have implications on licenses for perpetual subscription
   * customers.
   *
   * @var string
   */
  public $deprovisionReason;

  /**
   * Action to be taken on the Chrome OS device.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Only used when the action is `deprovision`. With the `deprovision` action,
   * this field is required. *Note*: The deprovision reason is audited because
   * it might have implications on licenses for perpetual subscription
   * customers.
   *
   * @param string $deprovisionReason
   */
  public function setDeprovisionReason($deprovisionReason)
  {
    $this->deprovisionReason = $deprovisionReason;
  }
  /**
   * @return string
   */
  public function getDeprovisionReason()
  {
    return $this->deprovisionReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDeviceAction::class, 'Google_Service_Directory_ChromeOsDeviceAction');
