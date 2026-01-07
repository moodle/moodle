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

namespace Google\Service\SecurityCommandCenter;

class TicketInfo extends \Google\Model
{
  /**
   * The assignee of the ticket in the ticket system.
   *
   * @var string
   */
  public $assignee;
  /**
   * The description of the ticket in the ticket system.
   *
   * @var string
   */
  public $description;
  /**
   * The identifier of the ticket in the ticket system.
   *
   * @var string
   */
  public $id;
  /**
   * The latest status of the ticket, as reported by the ticket system.
   *
   * @var string
   */
  public $status;
  /**
   * The time when the ticket was last updated, as reported by the ticket
   * system.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The link to the ticket in the ticket system.
   *
   * @var string
   */
  public $uri;

  /**
   * The assignee of the ticket in the ticket system.
   *
   * @param string $assignee
   */
  public function setAssignee($assignee)
  {
    $this->assignee = $assignee;
  }
  /**
   * @return string
   */
  public function getAssignee()
  {
    return $this->assignee;
  }
  /**
   * The description of the ticket in the ticket system.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The identifier of the ticket in the ticket system.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The latest status of the ticket, as reported by the ticket system.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The time when the ticket was last updated, as reported by the ticket
   * system.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The link to the ticket in the ticket system.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TicketInfo::class, 'Google_Service_SecurityCommandCenter_TicketInfo');
