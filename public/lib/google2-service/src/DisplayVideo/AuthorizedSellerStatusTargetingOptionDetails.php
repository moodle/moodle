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

class AuthorizedSellerStatusTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when authorized seller status is not specified in this
   * version. This enum is a placeholder for the default value, or "Authorized
   * Direct Sellers and Resellers" in the UI.
   */
  public const AUTHORIZED_SELLER_STATUS_AUTHORIZED_SELLER_STATUS_UNSPECIFIED = 'AUTHORIZED_SELLER_STATUS_UNSPECIFIED';
  /**
   * Only authorized sellers that directly own the inventory being monetized, as
   * indicated by a DIRECT declaration in the ads.txt file. This value is
   * equivalent to "Authorized Direct Sellers" in the UI.
   */
  public const AUTHORIZED_SELLER_STATUS_AUTHORIZED_SELLER_STATUS_AUTHORIZED_DIRECT_SELLERS_ONLY = 'AUTHORIZED_SELLER_STATUS_AUTHORIZED_DIRECT_SELLERS_ONLY';
  /**
   * All authorized sellers, including publishers that have not posted an
   * ads.txt file. Display & Video 360 automatically disallows unauthorized
   * sellers. This value is equivalent to "Authorized and Non-Participating
   * Publishers" in the UI.
   */
  public const AUTHORIZED_SELLER_STATUS_AUTHORIZED_SELLER_STATUS_AUTHORIZED_AND_NON_PARTICIPATING_PUBLISHERS = 'AUTHORIZED_SELLER_STATUS_AUTHORIZED_AND_NON_PARTICIPATING_PUBLISHERS';
  /**
   * Output only. The authorized seller status.
   *
   * @var string
   */
  public $authorizedSellerStatus;

  /**
   * Output only. The authorized seller status.
   *
   * Accepted values: AUTHORIZED_SELLER_STATUS_UNSPECIFIED,
   * AUTHORIZED_SELLER_STATUS_AUTHORIZED_DIRECT_SELLERS_ONLY,
   * AUTHORIZED_SELLER_STATUS_AUTHORIZED_AND_NON_PARTICIPATING_PUBLISHERS
   *
   * @param self::AUTHORIZED_SELLER_STATUS_* $authorizedSellerStatus
   */
  public function setAuthorizedSellerStatus($authorizedSellerStatus)
  {
    $this->authorizedSellerStatus = $authorizedSellerStatus;
  }
  /**
   * @return self::AUTHORIZED_SELLER_STATUS_*
   */
  public function getAuthorizedSellerStatus()
  {
    return $this->authorizedSellerStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthorizedSellerStatusTargetingOptionDetails::class, 'Google_Service_DisplayVideo_AuthorizedSellerStatusTargetingOptionDetails');
