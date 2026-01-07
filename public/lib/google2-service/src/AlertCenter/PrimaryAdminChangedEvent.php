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

class PrimaryAdminChangedEvent extends \Google\Model
{
  /**
   * domain in which actioned occurred
   *
   * @var string
   */
  public $domain;
  /**
   * Email of person who was the primary admin before the action
   *
   * @var string
   */
  public $previousAdminEmail;
  /**
   * Email of person who is the primary admin after the action
   *
   * @var string
   */
  public $updatedAdminEmail;

  /**
   * domain in which actioned occurred
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Email of person who was the primary admin before the action
   *
   * @param string $previousAdminEmail
   */
  public function setPreviousAdminEmail($previousAdminEmail)
  {
    $this->previousAdminEmail = $previousAdminEmail;
  }
  /**
   * @return string
   */
  public function getPreviousAdminEmail()
  {
    return $this->previousAdminEmail;
  }
  /**
   * Email of person who is the primary admin after the action
   *
   * @param string $updatedAdminEmail
   */
  public function setUpdatedAdminEmail($updatedAdminEmail)
  {
    $this->updatedAdminEmail = $updatedAdminEmail;
  }
  /**
   * @return string
   */
  public function getUpdatedAdminEmail()
  {
    return $this->updatedAdminEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrimaryAdminChangedEvent::class, 'Google_Service_AlertCenter_PrimaryAdminChangedEvent');
