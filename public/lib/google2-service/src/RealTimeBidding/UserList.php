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

namespace Google\Service\RealTimeBidding;

class UserList extends \Google\Model
{
  /**
   * Default value that should never be used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * New users can be added to the user list.
   */
  public const STATUS_OPEN = 'OPEN';
  /**
   * New users cannot be added to the user list.
   */
  public const STATUS_CLOSED = 'CLOSED';
  /**
   * The description for the user list.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name of the user list. This must be unique across all
   * user lists for a given account.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The number of days a user's cookie stays on the user list. The
   * field must be between 0 and 540 inclusive.
   *
   * @var string
   */
  public $membershipDurationDays;
  /**
   * Output only. Name of the user list that must follow the pattern
   * `buyers/{buyer}/userLists/{user_list}`, where `{buyer}` represents the
   * account ID of the buyer who owns the user list. For a bidder accessing user
   * lists on behalf of a child seat buyer, `{buyer}` represents the account ID
   * of the child seat buyer. `{user_list}` is an int64 identifier assigned by
   * Google to uniquely identify a user list.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The status of the user list. A new user list starts out as
   * open.
   *
   * @var string
   */
  public $status;
  protected $urlRestrictionType = UrlRestriction::class;
  protected $urlRestrictionDataType = '';

  /**
   * The description for the user list.
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
   * Required. Display name of the user list. This must be unique across all
   * user lists for a given account.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The number of days a user's cookie stays on the user list. The
   * field must be between 0 and 540 inclusive.
   *
   * @param string $membershipDurationDays
   */
  public function setMembershipDurationDays($membershipDurationDays)
  {
    $this->membershipDurationDays = $membershipDurationDays;
  }
  /**
   * @return string
   */
  public function getMembershipDurationDays()
  {
    return $this->membershipDurationDays;
  }
  /**
   * Output only. Name of the user list that must follow the pattern
   * `buyers/{buyer}/userLists/{user_list}`, where `{buyer}` represents the
   * account ID of the buyer who owns the user list. For a bidder accessing user
   * lists on behalf of a child seat buyer, `{buyer}` represents the account ID
   * of the child seat buyer. `{user_list}` is an int64 identifier assigned by
   * Google to uniquely identify a user list.
   *
   * @param string $name
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
   * Output only. The status of the user list. A new user list starts out as
   * open.
   *
   * Accepted values: STATUS_UNSPECIFIED, OPEN, CLOSED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Required. Deprecated. This will be removed in October 2023. For more
   * information, see the release notes:
   * https://developers.google.com/authorized-buyers/apis/relnotes#real-time-
   * bidding-api The URL restriction for the user list.
   *
   * @param UrlRestriction $urlRestriction
   */
  public function setUrlRestriction(UrlRestriction $urlRestriction)
  {
    $this->urlRestriction = $urlRestriction;
  }
  /**
   * @return UrlRestriction
   */
  public function getUrlRestriction()
  {
    return $this->urlRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserList::class, 'Google_Service_RealTimeBidding_UserList');
