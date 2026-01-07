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

namespace Google\Service\Dfareporting;

class DfpSettings extends \Google\Model
{
  /**
   * Ad Manager network code for this directory site.
   *
   * @var string
   */
  public $dfpNetworkCode;
  /**
   * Ad Manager network name for this directory site.
   *
   * @var string
   */
  public $dfpNetworkName;
  /**
   * Whether this directory site accepts programmatic placements.
   *
   * @var bool
   */
  public $programmaticPlacementAccepted;
  /**
   * Whether this directory site accepts publisher-paid tags.
   *
   * @var bool
   */
  public $pubPaidPlacementAccepted;
  /**
   * Whether this directory site is available only via Publisher Portal.
   *
   * @var bool
   */
  public $publisherPortalOnly;

  /**
   * Ad Manager network code for this directory site.
   *
   * @param string $dfpNetworkCode
   */
  public function setDfpNetworkCode($dfpNetworkCode)
  {
    $this->dfpNetworkCode = $dfpNetworkCode;
  }
  /**
   * @return string
   */
  public function getDfpNetworkCode()
  {
    return $this->dfpNetworkCode;
  }
  /**
   * Ad Manager network name for this directory site.
   *
   * @param string $dfpNetworkName
   */
  public function setDfpNetworkName($dfpNetworkName)
  {
    $this->dfpNetworkName = $dfpNetworkName;
  }
  /**
   * @return string
   */
  public function getDfpNetworkName()
  {
    return $this->dfpNetworkName;
  }
  /**
   * Whether this directory site accepts programmatic placements.
   *
   * @param bool $programmaticPlacementAccepted
   */
  public function setProgrammaticPlacementAccepted($programmaticPlacementAccepted)
  {
    $this->programmaticPlacementAccepted = $programmaticPlacementAccepted;
  }
  /**
   * @return bool
   */
  public function getProgrammaticPlacementAccepted()
  {
    return $this->programmaticPlacementAccepted;
  }
  /**
   * Whether this directory site accepts publisher-paid tags.
   *
   * @param bool $pubPaidPlacementAccepted
   */
  public function setPubPaidPlacementAccepted($pubPaidPlacementAccepted)
  {
    $this->pubPaidPlacementAccepted = $pubPaidPlacementAccepted;
  }
  /**
   * @return bool
   */
  public function getPubPaidPlacementAccepted()
  {
    return $this->pubPaidPlacementAccepted;
  }
  /**
   * Whether this directory site is available only via Publisher Portal.
   *
   * @param bool $publisherPortalOnly
   */
  public function setPublisherPortalOnly($publisherPortalOnly)
  {
    $this->publisherPortalOnly = $publisherPortalOnly;
  }
  /**
   * @return bool
   */
  public function getPublisherPortalOnly()
  {
    return $this->publisherPortalOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DfpSettings::class, 'Google_Service_Dfareporting_DfpSettings');
