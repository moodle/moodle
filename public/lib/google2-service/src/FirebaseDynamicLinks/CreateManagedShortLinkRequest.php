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

class CreateManagedShortLinkRequest extends \Google\Model
{
  protected $dynamicLinkInfoType = DynamicLinkInfo::class;
  protected $dynamicLinkInfoDataType = '';
  /**
   * Full long Dynamic Link URL with desired query parameters specified. For
   * example,
   * "https://sample.app.goo.gl/?link=http://www.google.com&apn=com.sample",
   * [Learn more](https://firebase.google.com/docs/reference/dynamic-links/link-
   * shortener).
   *
   * @var string
   */
  public $longDynamicLink;
  /**
   * Link name to associate with the link. It's used for marketer to identify
   * manually-created links in the Firebase console
   * (https://console.firebase.google.com/). Links must be named to be tracked.
   *
   * @var string
   */
  public $name;
  /**
   * Google SDK version. Version takes the form "$major.$minor.$patch"
   *
   * @var string
   */
  public $sdkVersion;
  protected $suffixType = Suffix::class;
  protected $suffixDataType = '';

  /**
   * Information about the Dynamic Link to be shortened. [Learn
   * more](https://firebase.google.com/docs/reference/dynamic-links/link-
   * shortener).
   *
   * @param DynamicLinkInfo $dynamicLinkInfo
   */
  public function setDynamicLinkInfo(DynamicLinkInfo $dynamicLinkInfo)
  {
    $this->dynamicLinkInfo = $dynamicLinkInfo;
  }
  /**
   * @return DynamicLinkInfo
   */
  public function getDynamicLinkInfo()
  {
    return $this->dynamicLinkInfo;
  }
  /**
   * Full long Dynamic Link URL with desired query parameters specified. For
   * example,
   * "https://sample.app.goo.gl/?link=http://www.google.com&apn=com.sample",
   * [Learn more](https://firebase.google.com/docs/reference/dynamic-links/link-
   * shortener).
   *
   * @param string $longDynamicLink
   */
  public function setLongDynamicLink($longDynamicLink)
  {
    $this->longDynamicLink = $longDynamicLink;
  }
  /**
   * @return string
   */
  public function getLongDynamicLink()
  {
    return $this->longDynamicLink;
  }
  /**
   * Link name to associate with the link. It's used for marketer to identify
   * manually-created links in the Firebase console
   * (https://console.firebase.google.com/). Links must be named to be tracked.
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
  /**
   * Short Dynamic Link suffix. Optional.
   *
   * @param Suffix $suffix
   */
  public function setSuffix(Suffix $suffix)
  {
    $this->suffix = $suffix;
  }
  /**
   * @return Suffix
   */
  public function getSuffix()
  {
    return $this->suffix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateManagedShortLinkRequest::class, 'Google_Service_FirebaseDynamicLinks_CreateManagedShortLinkRequest');
