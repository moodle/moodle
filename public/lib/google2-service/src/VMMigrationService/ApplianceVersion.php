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

namespace Google\Service\VMMigrationService;

class ApplianceVersion extends \Google\Model
{
  /**
   * Determine whether it's critical to upgrade the appliance to this version.
   *
   * @var bool
   */
  public $critical;
  /**
   * Link to a page that contains the version release notes.
   *
   * @var string
   */
  public $releaseNotesUri;
  /**
   * A link for downloading the version.
   *
   * @var string
   */
  public $uri;
  /**
   * The appliance version.
   *
   * @var string
   */
  public $version;

  /**
   * Determine whether it's critical to upgrade the appliance to this version.
   *
   * @param bool $critical
   */
  public function setCritical($critical)
  {
    $this->critical = $critical;
  }
  /**
   * @return bool
   */
  public function getCritical()
  {
    return $this->critical;
  }
  /**
   * Link to a page that contains the version release notes.
   *
   * @param string $releaseNotesUri
   */
  public function setReleaseNotesUri($releaseNotesUri)
  {
    $this->releaseNotesUri = $releaseNotesUri;
  }
  /**
   * @return string
   */
  public function getReleaseNotesUri()
  {
    return $this->releaseNotesUri;
  }
  /**
   * A link for downloading the version.
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
  /**
   * The appliance version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplianceVersion::class, 'Google_Service_VMMigrationService_ApplianceVersion');
