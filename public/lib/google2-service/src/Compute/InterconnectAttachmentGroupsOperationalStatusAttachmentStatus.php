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

namespace Google\Service\Compute;

class InterconnectAttachmentGroupsOperationalStatusAttachmentStatus extends \Google\Model
{
  public const IS_ACTIVE_ACTIVE = 'ACTIVE';
  public const IS_ACTIVE_INACTIVE = 'INACTIVE';
  public const IS_ACTIVE_UNSPECIFIED = 'UNSPECIFIED';
  public const STATUS_ATTACHMENT_STATUS_UNKNOWN = 'ATTACHMENT_STATUS_UNKNOWN';
  public const STATUS_CONNECTION_DISABLED = 'CONNECTION_DISABLED';
  public const STATUS_CONNECTION_DOWN = 'CONNECTION_DOWN';
  public const STATUS_CONNECTION_UP = 'CONNECTION_UP';
  public const STATUS_DEFUNCT = 'DEFUNCT';
  public const STATUS_IPSEC_CONFIGURATION_NEEDED_STATUS = 'IPSEC_CONFIGURATION_NEEDED_STATUS';
  public const STATUS_IPSEC_READY_TO_RESUME_FLOW_STATUS = 'IPSEC_READY_TO_RESUME_FLOW_STATUS';
  public const STATUS_IPV4_DOWN_IPV6_UP = 'IPV4_DOWN_IPV6_UP';
  public const STATUS_IPV4_UP_IPV6_DOWN = 'IPV4_UP_IPV6_DOWN';
  public const STATUS_PARTNER_REQUEST_RECEIVED = 'PARTNER_REQUEST_RECEIVED';
  public const STATUS_PENDING_CUSTOMER = 'PENDING_CUSTOMER';
  public const STATUS_PENDING_PARTNER = 'PENDING_PARTNER';
  public const STATUS_PROVISIONED = 'PROVISIONED';
  public const STATUS_ROUTER_CONFIGURATION_BROKEN = 'ROUTER_CONFIGURATION_BROKEN';
  public const STATUS_UNPROVISIONED = 'UNPROVISIONED';
  /**
   * Output only. Whether this Attachment is enabled. This becomes false when
   * the customer drains their Attachment.
   *
   * @var bool
   */
  public $adminEnabled;
  /**
   * Output only. The URL of the Attachment being described.
   *
   * @var string
   */
  public $attachment;
  /**
   * Output only. Whether this Attachment is participating in the redundant
   * configuration. This will be ACTIVE if and only if the status below is
   * CONNECTION_UP. Any INACTIVE Attachments are excluded from the analysis that
   * generates operational.availabilitySLA.
   *
   * @var string
   */
  public $isActive;
  /**
   * Output only. Whether this Attachment is active, and if so, whether BGP is
   * up.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. Whether this Attachment is enabled. This becomes false when
   * the customer drains their Attachment.
   *
   * @param bool $adminEnabled
   */
  public function setAdminEnabled($adminEnabled)
  {
    $this->adminEnabled = $adminEnabled;
  }
  /**
   * @return bool
   */
  public function getAdminEnabled()
  {
    return $this->adminEnabled;
  }
  /**
   * Output only. The URL of the Attachment being described.
   *
   * @param string $attachment
   */
  public function setAttachment($attachment)
  {
    $this->attachment = $attachment;
  }
  /**
   * @return string
   */
  public function getAttachment()
  {
    return $this->attachment;
  }
  /**
   * Output only. Whether this Attachment is participating in the redundant
   * configuration. This will be ACTIVE if and only if the status below is
   * CONNECTION_UP. Any INACTIVE Attachments are excluded from the analysis that
   * generates operational.availabilitySLA.
   *
   * Accepted values: ACTIVE, INACTIVE, UNSPECIFIED
   *
   * @param self::IS_ACTIVE_* $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return self::IS_ACTIVE_*
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
  /**
   * Output only. Whether this Attachment is active, and if so, whether BGP is
   * up.
   *
   * Accepted values: ATTACHMENT_STATUS_UNKNOWN, CONNECTION_DISABLED,
   * CONNECTION_DOWN, CONNECTION_UP, DEFUNCT, IPSEC_CONFIGURATION_NEEDED_STATUS,
   * IPSEC_READY_TO_RESUME_FLOW_STATUS, IPV4_DOWN_IPV6_UP, IPV4_UP_IPV6_DOWN,
   * PARTNER_REQUEST_RECEIVED, PENDING_CUSTOMER, PENDING_PARTNER, PROVISIONED,
   * ROUTER_CONFIGURATION_BROKEN, UNPROVISIONED
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
class_alias(InterconnectAttachmentGroupsOperationalStatusAttachmentStatus::class, 'Google_Service_Compute_InterconnectAttachmentGroupsOperationalStatusAttachmentStatus');
