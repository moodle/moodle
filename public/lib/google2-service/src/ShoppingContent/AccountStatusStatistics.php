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

namespace Google\Service\ShoppingContent;

class AccountStatusStatistics extends \Google\Model
{
  /**
   * Number of active offers.
   *
   * @var string
   */
  public $active;
  /**
   * Number of disapproved offers.
   *
   * @var string
   */
  public $disapproved;
  /**
   * Number of expiring offers.
   *
   * @var string
   */
  public $expiring;
  /**
   * Number of pending offers.
   *
   * @var string
   */
  public $pending;

  /**
   * Number of active offers.
   *
   * @param string $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return string
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Number of disapproved offers.
   *
   * @param string $disapproved
   */
  public function setDisapproved($disapproved)
  {
    $this->disapproved = $disapproved;
  }
  /**
   * @return string
   */
  public function getDisapproved()
  {
    return $this->disapproved;
  }
  /**
   * Number of expiring offers.
   *
   * @param string $expiring
   */
  public function setExpiring($expiring)
  {
    $this->expiring = $expiring;
  }
  /**
   * @return string
   */
  public function getExpiring()
  {
    return $this->expiring;
  }
  /**
   * Number of pending offers.
   *
   * @param string $pending
   */
  public function setPending($pending)
  {
    $this->pending = $pending;
  }
  /**
   * @return string
   */
  public function getPending()
  {
    return $this->pending;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountStatusStatistics::class, 'Google_Service_ShoppingContent_AccountStatusStatistics');
