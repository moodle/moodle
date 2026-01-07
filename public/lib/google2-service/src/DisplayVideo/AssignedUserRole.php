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

namespace Google\Service\DisplayVideo;

class AssignedUserRole extends \Google\Model
{
  /**
   * Default value when the user role is not specified or is unknown in this
   * version.
   */
  public const USER_ROLE_USER_ROLE_UNSPECIFIED = 'USER_ROLE_UNSPECIFIED';
  /**
   * The user can manage campaigns, creatives, insertion orders, line items, and
   * reports for the entity. They can view and edit billing information, create
   * or modify users, and enable or disable exchanges. This role can only be
   * assigned for a partner entity.
   */
  public const USER_ROLE_ADMIN = 'ADMIN';
  /**
   * The user can manage campaigns, creatives, insertion orders, line items, and
   * reports for the entity. They can create and modify other
   * `ADMIN_PARTNER_CLIENT` users and view billing information. They cannot view
   * revenue models, markups, or any other reseller-sensitive fields. This role
   * can only be assigned for a partner entity.
   */
  public const USER_ROLE_ADMIN_PARTNER_CLIENT = 'ADMIN_PARTNER_CLIENT';
  /**
   * The user can manage campaigns, creatives, insertion orders, line items, and
   * reports for the entity. They cannot create and modify users or view billing
   * information.
   */
  public const USER_ROLE_STANDARD = 'STANDARD';
  /**
   * The user can view all campaigns, creatives, insertion orders, line items,
   * and reports for the entity, including all cost data. They can create and
   * modify planning-related features, including plans and inventory.
   */
  public const USER_ROLE_STANDARD_PLANNER = 'STANDARD_PLANNER';
  /**
   * The user can view all campaigns, creatives, insertion orders, line items,
   * and reports for the entity. They can create or modify planning-related
   * features, including plans and inventory. They have no access to cost data
   * and cannot start, accept, or negotiate deals.
   */
  public const USER_ROLE_STANDARD_PLANNER_LIMITED = 'STANDARD_PLANNER_LIMITED';
  /**
   * The user can manage campaigns, creatives, insertion orders, line items, and
   * reports for the entity. They cannot create or modify other users or view
   * billing information. They cannot view revenue models, markups, or any other
   * reseller-sensitive fields. This role can only be assigned for an advertiser
   * entity.
   */
  public const USER_ROLE_STANDARD_PARTNER_CLIENT = 'STANDARD_PARTNER_CLIENT';
  /**
   * The user can only build reports and view data for the entity.
   */
  public const USER_ROLE_READ_ONLY = 'READ_ONLY';
  /**
   * The user can only create and manage reports.
   */
  public const USER_ROLE_REPORTING_ONLY = 'REPORTING_ONLY';
  /**
   * The user can only create and manage the following client-safe reports:
   * General, Audience Performance, Cross-Partner, Keyword, Order ID, Category,
   * and Third-Party Data Provider.
   */
  public const USER_ROLE_LIMITED_REPORTING_ONLY = 'LIMITED_REPORTING_ONLY';
  /**
   * The user can view media plan information they need to collaborate, but
   * can't view cost-related data or Marketplace.
   */
  public const USER_ROLE_CREATIVE = 'CREATIVE';
  /**
   * The user can view media plan information they need to collaborate, but
   * can't view cost-related data or Marketplace. In addition, they can add
   * other creative admins or creative users to the entity.
   */
  public const USER_ROLE_CREATIVE_ADMIN = 'CREATIVE_ADMIN';
  /**
   * The ID of the advertiser that the assigend user role applies to.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Output only. The ID of the assigned user role.
   *
   * @var string
   */
  public $assignedUserRoleId;
  /**
   * The ID of the partner that the assigned user role applies to.
   *
   * @var string
   */
  public $partnerId;
  /**
   * Required. The user role to assign to a user for the entity.
   *
   * @var string
   */
  public $userRole;

  /**
   * The ID of the advertiser that the assigend user role applies to.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Output only. The ID of the assigned user role.
   *
   * @param string $assignedUserRoleId
   */
  public function setAssignedUserRoleId($assignedUserRoleId)
  {
    $this->assignedUserRoleId = $assignedUserRoleId;
  }
  /**
   * @return string
   */
  public function getAssignedUserRoleId()
  {
    return $this->assignedUserRoleId;
  }
  /**
   * The ID of the partner that the assigned user role applies to.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
  /**
   * Required. The user role to assign to a user for the entity.
   *
   * Accepted values: USER_ROLE_UNSPECIFIED, ADMIN, ADMIN_PARTNER_CLIENT,
   * STANDARD, STANDARD_PLANNER, STANDARD_PLANNER_LIMITED,
   * STANDARD_PARTNER_CLIENT, READ_ONLY, REPORTING_ONLY, LIMITED_REPORTING_ONLY,
   * CREATIVE, CREATIVE_ADMIN
   *
   * @param self::USER_ROLE_* $userRole
   */
  public function setUserRole($userRole)
  {
    $this->userRole = $userRole;
  }
  /**
   * @return self::USER_ROLE_*
   */
  public function getUserRole()
  {
    return $this->userRole;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssignedUserRole::class, 'Google_Service_DisplayVideo_AssignedUserRole');
