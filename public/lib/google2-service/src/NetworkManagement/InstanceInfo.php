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

class InstanceInfo extends \Google\Collection
{
  /**
   * Default unspecified value.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The instance is running.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The instance has any status other than "RUNNING".
   */
  public const STATUS_NOT_RUNNING = 'NOT_RUNNING';
  protected $collection_key = 'networkTags';
  /**
   * Name of a Compute Engine instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * External IP address of the network interface.
   *
   * @var string
   */
  public $externalIp;
  /**
   * Name of the network interface of a Compute Engine instance.
   *
   * @var string
   */
  public $interface;
  /**
   * Internal IP address of the network interface.
   *
   * @var string
   */
  public $internalIp;
  /**
   * Network tags configured on the instance.
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * URI of a Compute Engine network.
   *
   * @var string
   */
  public $networkUri;
  /**
   * URI of the PSC network attachment the NIC is attached to (if relevant).
   *
   * @var string
   */
  public $pscNetworkAttachmentUri;
  /**
   * Indicates whether the Compute Engine instance is running. Deprecated: use
   * the `status` field instead.
   *
   * @deprecated
   * @var bool
   */
  public $running;
  /**
   * Service account authorized for the instance.
   *
   * @deprecated
   * @var string
   */
  public $serviceAccount;
  /**
   * The status of the instance.
   *
   * @var string
   */
  public $status;
  /**
   * URI of a Compute Engine instance.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a Compute Engine instance.
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
   * External IP address of the network interface.
   *
   * @param string $externalIp
   */
  public function setExternalIp($externalIp)
  {
    $this->externalIp = $externalIp;
  }
  /**
   * @return string
   */
  public function getExternalIp()
  {
    return $this->externalIp;
  }
  /**
   * Name of the network interface of a Compute Engine instance.
   *
   * @param string $interface
   */
  public function setInterface($interface)
  {
    $this->interface = $interface;
  }
  /**
   * @return string
   */
  public function getInterface()
  {
    return $this->interface;
  }
  /**
   * Internal IP address of the network interface.
   *
   * @param string $internalIp
   */
  public function setInternalIp($internalIp)
  {
    $this->internalIp = $internalIp;
  }
  /**
   * @return string
   */
  public function getInternalIp()
  {
    return $this->internalIp;
  }
  /**
   * Network tags configured on the instance.
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * URI of a Compute Engine network.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * URI of the PSC network attachment the NIC is attached to (if relevant).
   *
   * @param string $pscNetworkAttachmentUri
   */
  public function setPscNetworkAttachmentUri($pscNetworkAttachmentUri)
  {
    $this->pscNetworkAttachmentUri = $pscNetworkAttachmentUri;
  }
  /**
   * @return string
   */
  public function getPscNetworkAttachmentUri()
  {
    return $this->pscNetworkAttachmentUri;
  }
  /**
   * Indicates whether the Compute Engine instance is running. Deprecated: use
   * the `status` field instead.
   *
   * @deprecated
   * @param bool $running
   */
  public function setRunning($running)
  {
    $this->running = $running;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getRunning()
  {
    return $this->running;
  }
  /**
   * Service account authorized for the instance.
   *
   * @deprecated
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * The status of the instance.
   *
   * Accepted values: STATUS_UNSPECIFIED, RUNNING, NOT_RUNNING
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
   * URI of a Compute Engine instance.
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
class_alias(InstanceInfo::class, 'Google_Service_NetworkManagement_InstanceInfo');
