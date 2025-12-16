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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesCustomerManagerLink extends \Google\Model
{
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Indicates current in-effect relationship
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * Indicates terminated relationship
   */
  public const STATUS_INACTIVE = 'INACTIVE';
  /**
   * Indicates relationship has been requested by manager, but the client hasn't
   * accepted yet.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * Relationship was requested by the manager, but the client has refused.
   */
  public const STATUS_REFUSED = 'REFUSED';
  /**
   * Indicates relationship has been requested by manager, but manager canceled
   * it.
   */
  public const STATUS_CANCELED = 'CANCELED';
  /**
   * Output only. The manager customer linked to the customer.
   *
   * @var string
   */
  public $managerCustomer;
  /**
   * Output only. ID of the customer-manager link. This field is read only.
   *
   * @var string
   */
  public $managerLinkId;
  /**
   * Immutable. Name of the resource. CustomerManagerLink resource names have
   * the form: `customers/{customer_id}/customerManagerLinks/{manager_customer_i
   * d}~{manager_link_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The timestamp when the CustomerManagerLink was created. The
   * timestamp is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss"
   * format.
   *
   * @var string
   */
  public $startTime;
  /**
   * Status of the link between the customer and the manager.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. The manager customer linked to the customer.
   *
   * @param string $managerCustomer
   */
  public function setManagerCustomer($managerCustomer)
  {
    $this->managerCustomer = $managerCustomer;
  }
  /**
   * @return string
   */
  public function getManagerCustomer()
  {
    return $this->managerCustomer;
  }
  /**
   * Output only. ID of the customer-manager link. This field is read only.
   *
   * @param string $managerLinkId
   */
  public function setManagerLinkId($managerLinkId)
  {
    $this->managerLinkId = $managerLinkId;
  }
  /**
   * @return string
   */
  public function getManagerLinkId()
  {
    return $this->managerLinkId;
  }
  /**
   * Immutable. Name of the resource. CustomerManagerLink resource names have
   * the form: `customers/{customer_id}/customerManagerLinks/{manager_customer_i
   * d}~{manager_link_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. The timestamp when the CustomerManagerLink was created. The
   * timestamp is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss"
   * format.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Status of the link between the customer and the manager.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ACTIVE, INACTIVE, PENDING, REFUSED,
   * CANCELED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCustomerManagerLink::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCustomerManagerLink');
