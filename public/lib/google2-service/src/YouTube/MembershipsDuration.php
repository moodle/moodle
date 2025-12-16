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

namespace Google\Service\YouTube;

class MembershipsDuration extends \Google\Model
{
  /**
   * The date and time when the user became a continuous member across all
   * levels.
   *
   * @var string
   */
  public $memberSince;
  /**
   * The cumulative time the user has been a member across all levels in
   * complete months (the time is rounded down to the nearest integer).
   *
   * @var int
   */
  public $memberTotalDurationMonths;

  /**
   * The date and time when the user became a continuous member across all
   * levels.
   *
   * @param string $memberSince
   */
  public function setMemberSince($memberSince)
  {
    $this->memberSince = $memberSince;
  }
  /**
   * @return string
   */
  public function getMemberSince()
  {
    return $this->memberSince;
  }
  /**
   * The cumulative time the user has been a member across all levels in
   * complete months (the time is rounded down to the nearest integer).
   *
   * @param int $memberTotalDurationMonths
   */
  public function setMemberTotalDurationMonths($memberTotalDurationMonths)
  {
    $this->memberTotalDurationMonths = $memberTotalDurationMonths;
  }
  /**
   * @return int
   */
  public function getMemberTotalDurationMonths()
  {
    return $this->memberTotalDurationMonths;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MembershipsDuration::class, 'Google_Service_YouTube_MembershipsDuration');
