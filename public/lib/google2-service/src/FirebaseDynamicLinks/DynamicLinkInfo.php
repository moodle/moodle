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

class DynamicLinkInfo extends \Google\Model
{
  protected $analyticsInfoType = AnalyticsInfo::class;
  protected $analyticsInfoDataType = '';
  protected $androidInfoType = AndroidInfo::class;
  protected $androidInfoDataType = '';
  protected $desktopInfoType = DesktopInfo::class;
  protected $desktopInfoDataType = '';
  /**
   * E.g. https://maps.app.goo.gl, https://maps.page.link, https://g.co/maps
   * More examples can be found in description of getNormalizedUriPrefix in
   * j/c/g/firebase/dynamiclinks/uri/DdlDomain.java Will fallback to
   * dynamic_link_domain is this field is missing
   *
   * @var string
   */
  public $domainUriPrefix;
  /**
   * Dynamic Links domain that the project owns, e.g. abcd.app.goo.gl [Learn
   * more](https://firebase.google.com/docs/dynamic-links/android/receive) on
   * how to set up Dynamic Link domain associated with your Firebase project.
   * Required if missing domain_uri_prefix.
   *
   * @deprecated
   * @var string
   */
  public $dynamicLinkDomain;
  protected $iosInfoType = IosInfo::class;
  protected $iosInfoDataType = '';
  /**
   * The link your app will open, You can specify any URL your app can handle.
   * This link must be a well-formatted URL, be properly URL-encoded, and use
   * the HTTP or HTTPS scheme. See 'link' parameters in the
   * [documentation](https://firebase.google.com/docs/dynamic-links/create-
   * manually). Required.
   *
   * @var string
   */
  public $link;
  protected $navigationInfoType = NavigationInfo::class;
  protected $navigationInfoDataType = '';
  protected $socialMetaTagInfoType = SocialMetaTagInfo::class;
  protected $socialMetaTagInfoDataType = '';

  /**
   * Parameters used for tracking. See all tracking parameters in the
   * [documentation](https://firebase.google.com/docs/dynamic-links/create-
   * manually).
   *
   * @param AnalyticsInfo $analyticsInfo
   */
  public function setAnalyticsInfo(AnalyticsInfo $analyticsInfo)
  {
    $this->analyticsInfo = $analyticsInfo;
  }
  /**
   * @return AnalyticsInfo
   */
  public function getAnalyticsInfo()
  {
    return $this->analyticsInfo;
  }
  /**
   * Android related information. See Android related parameters in the
   * [documentation](https://firebase.google.com/docs/dynamic-links/create-
   * manually).
   *
   * @param AndroidInfo $androidInfo
   */
  public function setAndroidInfo(AndroidInfo $androidInfo)
  {
    $this->androidInfo = $androidInfo;
  }
  /**
   * @return AndroidInfo
   */
  public function getAndroidInfo()
  {
    return $this->androidInfo;
  }
  /**
   * Desktop related information. See desktop related parameters in the
   * [documentation](https://firebase.google.com/docs/dynamic-links/create-
   * manually).
   *
   * @param DesktopInfo $desktopInfo
   */
  public function setDesktopInfo(DesktopInfo $desktopInfo)
  {
    $this->desktopInfo = $desktopInfo;
  }
  /**
   * @return DesktopInfo
   */
  public function getDesktopInfo()
  {
    return $this->desktopInfo;
  }
  /**
   * E.g. https://maps.app.goo.gl, https://maps.page.link, https://g.co/maps
   * More examples can be found in description of getNormalizedUriPrefix in
   * j/c/g/firebase/dynamiclinks/uri/DdlDomain.java Will fallback to
   * dynamic_link_domain is this field is missing
   *
   * @param string $domainUriPrefix
   */
  public function setDomainUriPrefix($domainUriPrefix)
  {
    $this->domainUriPrefix = $domainUriPrefix;
  }
  /**
   * @return string
   */
  public function getDomainUriPrefix()
  {
    return $this->domainUriPrefix;
  }
  /**
   * Dynamic Links domain that the project owns, e.g. abcd.app.goo.gl [Learn
   * more](https://firebase.google.com/docs/dynamic-links/android/receive) on
   * how to set up Dynamic Link domain associated with your Firebase project.
   * Required if missing domain_uri_prefix.
   *
   * @deprecated
   * @param string $dynamicLinkDomain
   */
  public function setDynamicLinkDomain($dynamicLinkDomain)
  {
    $this->dynamicLinkDomain = $dynamicLinkDomain;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDynamicLinkDomain()
  {
    return $this->dynamicLinkDomain;
  }
  /**
   * iOS related information. See iOS related parameters in the
   * [documentation](https://firebase.google.com/docs/dynamic-links/create-
   * manually).
   *
   * @param IosInfo $iosInfo
   */
  public function setIosInfo(IosInfo $iosInfo)
  {
    $this->iosInfo = $iosInfo;
  }
  /**
   * @return IosInfo
   */
  public function getIosInfo()
  {
    return $this->iosInfo;
  }
  /**
   * The link your app will open, You can specify any URL your app can handle.
   * This link must be a well-formatted URL, be properly URL-encoded, and use
   * the HTTP or HTTPS scheme. See 'link' parameters in the
   * [documentation](https://firebase.google.com/docs/dynamic-links/create-
   * manually). Required.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * Information of navigation behavior of a Firebase Dynamic Links.
   *
   * @param NavigationInfo $navigationInfo
   */
  public function setNavigationInfo(NavigationInfo $navigationInfo)
  {
    $this->navigationInfo = $navigationInfo;
  }
  /**
   * @return NavigationInfo
   */
  public function getNavigationInfo()
  {
    return $this->navigationInfo;
  }
  /**
   * Parameters for social meta tag params. Used to set meta tag data for link
   * previews on social sites.
   *
   * @param SocialMetaTagInfo $socialMetaTagInfo
   */
  public function setSocialMetaTagInfo(SocialMetaTagInfo $socialMetaTagInfo)
  {
    $this->socialMetaTagInfo = $socialMetaTagInfo;
  }
  /**
   * @return SocialMetaTagInfo
   */
  public function getSocialMetaTagInfo()
  {
    return $this->socialMetaTagInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicLinkInfo::class, 'Google_Service_FirebaseDynamicLinks_DynamicLinkInfo');
