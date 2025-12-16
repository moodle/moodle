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

namespace Google\Service\Container;

class ReleaseChannelConfig extends \Google\Collection
{
  /**
   * No channel specified.
   */
  public const CHANNEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * RAPID channel is offered on an early access basis for customers who want to
   * test new releases. WARNING: Versions available in the RAPID Channel may be
   * subject to unresolved issues with no known workaround and are not subject
   * to any SLAs.
   */
  public const CHANNEL_RAPID = 'RAPID';
  /**
   * Clusters subscribed to REGULAR receive versions that are considered GA
   * quality. REGULAR is intended for production users who want to take
   * advantage of new features.
   */
  public const CHANNEL_REGULAR = 'REGULAR';
  /**
   * Clusters subscribed to STABLE receive versions that are known to be stable
   * and reliable in production.
   */
  public const CHANNEL_STABLE = 'STABLE';
  /**
   * Clusters subscribed to EXTENDED receive extended support and availability
   * for versions which are known to be stable and reliable in production.
   */
  public const CHANNEL_EXTENDED = 'EXTENDED';
  protected $collection_key = 'validVersions';
  /**
   * The release channel this configuration applies to.
   *
   * @var string
   */
  public $channel;
  /**
   * The default version for newly created clusters on the channel.
   *
   * @var string
   */
  public $defaultVersion;
  /**
   * The auto upgrade target version for clusters on the channel.
   *
   * @var string
   */
  public $upgradeTargetVersion;
  /**
   * List of valid versions for the channel.
   *
   * @var string[]
   */
  public $validVersions;

  /**
   * The release channel this configuration applies to.
   *
   * Accepted values: UNSPECIFIED, RAPID, REGULAR, STABLE, EXTENDED
   *
   * @param self::CHANNEL_* $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return self::CHANNEL_*
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * The default version for newly created clusters on the channel.
   *
   * @param string $defaultVersion
   */
  public function setDefaultVersion($defaultVersion)
  {
    $this->defaultVersion = $defaultVersion;
  }
  /**
   * @return string
   */
  public function getDefaultVersion()
  {
    return $this->defaultVersion;
  }
  /**
   * The auto upgrade target version for clusters on the channel.
   *
   * @param string $upgradeTargetVersion
   */
  public function setUpgradeTargetVersion($upgradeTargetVersion)
  {
    $this->upgradeTargetVersion = $upgradeTargetVersion;
  }
  /**
   * @return string
   */
  public function getUpgradeTargetVersion()
  {
    return $this->upgradeTargetVersion;
  }
  /**
   * List of valid versions for the channel.
   *
   * @param string[] $validVersions
   */
  public function setValidVersions($validVersions)
  {
    $this->validVersions = $validVersions;
  }
  /**
   * @return string[]
   */
  public function getValidVersions()
  {
    return $this->validVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReleaseChannelConfig::class, 'Google_Service_Container_ReleaseChannelConfig');
