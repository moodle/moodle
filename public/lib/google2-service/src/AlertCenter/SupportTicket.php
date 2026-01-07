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

namespace Google\Service\AlertCenter;

class SupportTicket extends \Google\Model
{
  /**
   * Support ticket ID
   *
   * @var string
   */
  public $ticketId;
  /**
   * Link to support ticket
   *
   * @var string
   */
  public $ticketUrl;

  /**
   * Support ticket ID
   *
   * @param string $ticketId
   */
  public function setTicketId($ticketId)
  {
    $this->ticketId = $ticketId;
  }
  /**
   * @return string
   */
  public function getTicketId()
  {
    return $this->ticketId;
  }
  /**
   * Link to support ticket
   *
   * @param string $ticketUrl
   */
  public function setTicketUrl($ticketUrl)
  {
    $this->ticketUrl = $ticketUrl;
  }
  /**
   * @return string
   */
  public function getTicketUrl()
  {
    return $this->ticketUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SupportTicket::class, 'Google_Service_AlertCenter_SupportTicket');
