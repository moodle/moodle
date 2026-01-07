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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1EntitlementEvent extends \Google\Model
{
  /**
   * Not used.
   */
  public const EVENT_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A new entitlement was created.
   */
  public const EVENT_TYPE_CREATED = 'CREATED';
  /**
   * The offer type associated with an entitlement was changed. This is not
   * triggered if an entitlement converts from a commit offer to a flexible
   * offer as part of a renewal.
   */
  public const EVENT_TYPE_PRICE_PLAN_SWITCHED = 'PRICE_PLAN_SWITCHED';
  /**
   * Annual commitment for a commit plan was changed.
   */
  public const EVENT_TYPE_COMMITMENT_CHANGED = 'COMMITMENT_CHANGED';
  /**
   * An annual entitlement was renewed.
   */
  public const EVENT_TYPE_RENEWED = 'RENEWED';
  /**
   * Entitlement was suspended.
   */
  public const EVENT_TYPE_SUSPENDED = 'SUSPENDED';
  /**
   * Entitlement was unsuspended.
   */
  public const EVENT_TYPE_ACTIVATED = 'ACTIVATED';
  /**
   * Entitlement was cancelled.
   */
  public const EVENT_TYPE_CANCELLED = 'CANCELLED';
  /**
   * Entitlement was upgraded or downgraded (e.g. from Google Workspace Business
   * Standard to Google Workspace Business Plus).
   */
  public const EVENT_TYPE_SKU_CHANGED = 'SKU_CHANGED';
  /**
   * The renewal settings of an entitlement has changed.
   */
  public const EVENT_TYPE_RENEWAL_SETTING_CHANGED = 'RENEWAL_SETTING_CHANGED';
  /**
   * Paid service has started on trial entitlement.
   */
  public const EVENT_TYPE_PAID_SERVICE_STARTED = 'PAID_SERVICE_STARTED';
  /**
   * License was assigned to or revoked from a user.
   */
  public const EVENT_TYPE_LICENSE_ASSIGNMENT_CHANGED = 'LICENSE_ASSIGNMENT_CHANGED';
  /**
   * License cap was changed for the entitlement.
   */
  public const EVENT_TYPE_LICENSE_CAP_CHANGED = 'LICENSE_CAP_CHANGED';
  /**
   * Resource name of an entitlement of the form:
   * accounts/{account_id}/customers/{customer_id}/entitlements/{entitlement_id}
   *
   * @var string
   */
  public $entitlement;
  /**
   * Type of event which happened for the entitlement.
   *
   * @var string
   */
  public $eventType;

  /**
   * Resource name of an entitlement of the form:
   * accounts/{account_id}/customers/{customer_id}/entitlements/{entitlement_id}
   *
   * @param string $entitlement
   */
  public function setEntitlement($entitlement)
  {
    $this->entitlement = $entitlement;
  }
  /**
   * @return string
   */
  public function getEntitlement()
  {
    return $this->entitlement;
  }
  /**
   * Type of event which happened for the entitlement.
   *
   * Accepted values: TYPE_UNSPECIFIED, CREATED, PRICE_PLAN_SWITCHED,
   * COMMITMENT_CHANGED, RENEWED, SUSPENDED, ACTIVATED, CANCELLED, SKU_CHANGED,
   * RENEWAL_SETTING_CHANGED, PAID_SERVICE_STARTED, LICENSE_ASSIGNMENT_CHANGED,
   * LICENSE_CAP_CHANGED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1EntitlementEvent::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1EntitlementEvent');
