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

namespace Google\Service\Looker;

class ServiceAttachment extends \Google\Collection
{
  /**
   * Connection status is unspecified.
   */
  public const CONNECTION_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Connection is established and functioning normally.
   */
  public const CONNECTION_STATUS_ACCEPTED = 'ACCEPTED';
  /**
   * Connection is not established (Looker tenant project hasn't been
   * allowlisted).
   */
  public const CONNECTION_STATUS_PENDING = 'PENDING';
  /**
   * Connection is not established (Looker tenant project is explicitly in
   * reject list).
   */
  public const CONNECTION_STATUS_REJECTED = 'REJECTED';
  /**
   * Issue with target service attachment, e.g. NAT subnet is exhausted.
   */
  public const CONNECTION_STATUS_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * Target service attachment does not exist. This status is a terminal state.
   */
  public const CONNECTION_STATUS_CLOSED = 'CLOSED';
  protected $collection_key = 'localFqdns';
  /**
   * Output only. Connection status.
   *
   * @var string
   */
  public $connectionStatus;
  /**
   * Output only. Reason the service attachment creation failed. This value will
   * only be populated if the service attachment encounters an issue during
   * provisioning.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Optional. Fully qualified domain name that will be used in the private DNS
   * record created for the service attachment.
   *
   * @var string
   */
  public $localFqdn;
  /**
   * Optional. List of fully qualified domain names that will be used in the
   * private DNS record created for the service attachment.
   *
   * @var string[]
   */
  public $localFqdns;
  /**
   * Required. URI of the service attachment to connect to. Format:
   * projects/{project}/regions/{region}/serviceAttachments/{service_attachment}
   *
   * @var string
   */
  public $targetServiceAttachmentUri;

  /**
   * Output only. Connection status.
   *
   * Accepted values: UNKNOWN, ACCEPTED, PENDING, REJECTED, NEEDS_ATTENTION,
   * CLOSED
   *
   * @param self::CONNECTION_STATUS_* $connectionStatus
   */
  public function setConnectionStatus($connectionStatus)
  {
    $this->connectionStatus = $connectionStatus;
  }
  /**
   * @return self::CONNECTION_STATUS_*
   */
  public function getConnectionStatus()
  {
    return $this->connectionStatus;
  }
  /**
   * Output only. Reason the service attachment creation failed. This value will
   * only be populated if the service attachment encounters an issue during
   * provisioning.
   *
   * @param string $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return string
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Optional. Fully qualified domain name that will be used in the private DNS
   * record created for the service attachment.
   *
   * @param string $localFqdn
   */
  public function setLocalFqdn($localFqdn)
  {
    $this->localFqdn = $localFqdn;
  }
  /**
   * @return string
   */
  public function getLocalFqdn()
  {
    return $this->localFqdn;
  }
  /**
   * Optional. List of fully qualified domain names that will be used in the
   * private DNS record created for the service attachment.
   *
   * @param string[] $localFqdns
   */
  public function setLocalFqdns($localFqdns)
  {
    $this->localFqdns = $localFqdns;
  }
  /**
   * @return string[]
   */
  public function getLocalFqdns()
  {
    return $this->localFqdns;
  }
  /**
   * Required. URI of the service attachment to connect to. Format:
   * projects/{project}/regions/{region}/serviceAttachments/{service_attachment}
   *
   * @param string $targetServiceAttachmentUri
   */
  public function setTargetServiceAttachmentUri($targetServiceAttachmentUri)
  {
    $this->targetServiceAttachmentUri = $targetServiceAttachmentUri;
  }
  /**
   * @return string
   */
  public function getTargetServiceAttachmentUri()
  {
    return $this->targetServiceAttachmentUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAttachment::class, 'Google_Service_Looker_ServiceAttachment');
