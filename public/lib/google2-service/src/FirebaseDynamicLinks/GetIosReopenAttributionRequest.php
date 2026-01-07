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

namespace Google\Service\FirebaseDynamicLinks;

class GetIosReopenAttributionRequest extends \Google\Model
{
  /**
   * APP bundle ID.
   *
   * @var string
   */
  public $bundleId;
  /**
   * FDL link to be verified from an app universal link open. The FDL link can
   * be one of: 1) short FDL. e.g. .page.link/, or 2) long FDL. e.g.
   * .page.link/?{query params}, or 3) Invite FDL. e.g. .page.link/i/
   *
   * @var string
   */
  public $requestedLink;
  /**
   * Google SDK version. Version takes the form "$major.$minor.$patch"
   *
   * @var string
   */
  public $sdkVersion;

  /**
   * APP bundle ID.
   *
   * @param string $bundleId
   */
  public function setBundleId($bundleId)
  {
    $this->bundleId = $bundleId;
  }
  /**
   * @return string
   */
  public function getBundleId()
  {
    return $this->bundleId;
  }
  /**
   * FDL link to be verified from an app universal link open. The FDL link can
   * be one of: 1) short FDL. e.g. .page.link/, or 2) long FDL. e.g.
   * .page.link/?{query params}, or 3) Invite FDL. e.g. .page.link/i/
   *
   * @param string $requestedLink
   */
  public function setRequestedLink($requestedLink)
  {
    $this->requestedLink = $requestedLink;
  }
  /**
   * @return string
   */
  public function getRequestedLink()
  {
    return $this->requestedLink;
  }
  /**
   * Google SDK version. Version takes the form "$major.$minor.$patch"
   *
   * @param string $sdkVersion
   */
  public function setSdkVersion($sdkVersion)
  {
    $this->sdkVersion = $sdkVersion;
  }
  /**
   * @return string
   */
  public function getSdkVersion()
  {
    return $this->sdkVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetIosReopenAttributionRequest::class, 'Google_Service_FirebaseDynamicLinks_GetIosReopenAttributionRequest');
