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

class PublicAdvertisedPrefixPublicDelegatedPrefix extends \Google\Model
{
  /**
   * The IP address range of the public delegated prefix
   *
   * @var string
   */
  public $ipRange;
  /**
   * The name of the public delegated prefix
   *
   * @var string
   */
  public $name;
  /**
   * The project number of the public delegated prefix
   *
   * @var string
   */
  public $project;
  /**
   * The region of the public delegated prefix if it is regional. If absent, the
   * prefix is global.
   *
   * @var string
   */
  public $region;
  /**
   * The status of the public delegated prefix. Possible values are:
   * INITIALIZING: The public delegated prefix is being initialized and
   * addresses cannot be created yet.   ANNOUNCED: The public delegated prefix
   * is active.
   *
   * @var string
   */
  public $status;

  /**
   * The IP address range of the public delegated prefix
   *
   * @param string $ipRange
   */
  public function setIpRange($ipRange)
  {
    $this->ipRange = $ipRange;
  }
  /**
   * @return string
   */
  public function getIpRange()
  {
    return $this->ipRange;
  }
  /**
   * The name of the public delegated prefix
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
   * The project number of the public delegated prefix
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * The region of the public delegated prefix if it is regional. If absent, the
   * prefix is global.
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
   * The status of the public delegated prefix. Possible values are:
   * INITIALIZING: The public delegated prefix is being initialized and
   * addresses cannot be created yet.   ANNOUNCED: The public delegated prefix
   * is active.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublicAdvertisedPrefixPublicDelegatedPrefix::class, 'Google_Service_Compute_PublicAdvertisedPrefixPublicDelegatedPrefix');
