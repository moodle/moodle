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

namespace Google\Service\NetworkManagement;

class InterconnectAttachmentInfo extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Attachment to a dedicated interconnect.
   */
  public const TYPE_DEDICATED = 'DEDICATED';
  /**
   * Attachment to a partner interconnect, created by the customer.
   */
  public const TYPE_PARTNER = 'PARTNER';
  /**
   * Attachment to a partner interconnect, created by the partner.
   */
  public const TYPE_PARTNER_PROVIDER = 'PARTNER_PROVIDER';
  /**
   * Attachment to a L2 interconnect, created by the customer.
   */
  public const TYPE_L2_DEDICATED = 'L2_DEDICATED';
  /**
   * URI of the Cloud Router to be used for dynamic routing.
   *
   * @var string
   */
  public $cloudRouterUri;
  /**
   * Name of an Interconnect attachment.
   *
   * @var string
   */
  public $displayName;
  /**
   * URI of the Interconnect where the Interconnect attachment is configured.
   *
   * @var string
   */
  public $interconnectUri;
  /**
   * Appliance IP address that was matched for L2_DEDICATED attachments.
   *
   * @var string
   */
  public $l2AttachmentMatchedIpAddress;
  /**
   * Name of a Google Cloud region where the Interconnect attachment is
   * configured.
   *
   * @var string
   */
  public $region;
  /**
   * The type of interconnect attachment this is.
   *
   * @var string
   */
  public $type;
  /**
   * URI of an Interconnect attachment.
   *
   * @var string
   */
  public $uri;

  /**
   * URI of the Cloud Router to be used for dynamic routing.
   *
   * @param string $cloudRouterUri
   */
  public function setCloudRouterUri($cloudRouterUri)
  {
    $this->cloudRouterUri = $cloudRouterUri;
  }
  /**
   * @return string
   */
  public function getCloudRouterUri()
  {
    return $this->cloudRouterUri;
  }
  /**
   * Name of an Interconnect attachment.
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
   * URI of the Interconnect where the Interconnect attachment is configured.
   *
   * @param string $interconnectUri
   */
  public function setInterconnectUri($interconnectUri)
  {
    $this->interconnectUri = $interconnectUri;
  }
  /**
   * @return string
   */
  public function getInterconnectUri()
  {
    return $this->interconnectUri;
  }
  /**
   * Appliance IP address that was matched for L2_DEDICATED attachments.
   *
   * @param string $l2AttachmentMatchedIpAddress
   */
  public function setL2AttachmentMatchedIpAddress($l2AttachmentMatchedIpAddress)
  {
    $this->l2AttachmentMatchedIpAddress = $l2AttachmentMatchedIpAddress;
  }
  /**
   * @return string
   */
  public function getL2AttachmentMatchedIpAddress()
  {
    return $this->l2AttachmentMatchedIpAddress;
  }
  /**
   * Name of a Google Cloud region where the Interconnect attachment is
   * configured.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * The type of interconnect attachment this is.
   *
   * Accepted values: TYPE_UNSPECIFIED, DEDICATED, PARTNER, PARTNER_PROVIDER,
   * L2_DEDICATED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * URI of an Interconnect attachment.
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
class_alias(InterconnectAttachmentInfo::class, 'Google_Service_NetworkManagement_InterconnectAttachmentInfo');
