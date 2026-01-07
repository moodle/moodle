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

class GetIosReopenAttributionResponse extends \Google\Collection
{
  protected $collection_key = 'warning';
  /**
   * The deep-link attributed the app universal link open. For both regular FDL
   * links and invite FDL links.
   *
   * @var string
   */
  public $deepLink;
  /**
   * Optional invitation ID, for only invite typed requested FDL links.
   *
   * @var string
   */
  public $invitationId;
  /**
   * FDL input value of the "&imv=" parameter, minimum app version to be
   * returned to Google Firebase SDK running on iOS-9.
   *
   * @var string
   */
  public $iosMinAppVersion;
  /**
   * The entire FDL, expanded from a short link. It is the same as the
   * requested_link, if it is long.
   *
   * @var string
   */
  public $resolvedLink;
  /**
   * Scion campaign value to be propagated by iSDK to Scion at app-reopen.
   *
   * @var string
   */
  public $utmCampaign;
  /**
   * Scion content value to be propagated by iSDK to Scion at app-reopen.
   *
   * @var string
   */
  public $utmContent;
  /**
   * Scion medium value to be propagated by iSDK to Scion at app-reopen.
   *
   * @var string
   */
  public $utmMedium;
  /**
   * Scion source value to be propagated by iSDK to Scion at app-reopen.
   *
   * @var string
   */
  public $utmSource;
  /**
   * Scion term value to be propagated by iSDK to Scion at app-reopen.
   *
   * @var string
   */
  public $utmTerm;
  protected $warningType = DynamicLinkWarning::class;
  protected $warningDataType = 'array';

  /**
   * The deep-link attributed the app universal link open. For both regular FDL
   * links and invite FDL links.
   *
   * @param string $deepLink
   */
  public function setDeepLink($deepLink)
  {
    $this->deepLink = $deepLink;
  }
  /**
   * @return string
   */
  public function getDeepLink()
  {
    return $this->deepLink;
  }
  /**
   * Optional invitation ID, for only invite typed requested FDL links.
   *
   * @param string $invitationId
   */
  public function setInvitationId($invitationId)
  {
    $this->invitationId = $invitationId;
  }
  /**
   * @return string
   */
  public function getInvitationId()
  {
    return $this->invitationId;
  }
  /**
   * FDL input value of the "&imv=" parameter, minimum app version to be
   * returned to Google Firebase SDK running on iOS-9.
   *
   * @param string $iosMinAppVersion
   */
  public function setIosMinAppVersion($iosMinAppVersion)
  {
    $this->iosMinAppVersion = $iosMinAppVersion;
  }
  /**
   * @return string
   */
  public function getIosMinAppVersion()
  {
    return $this->iosMinAppVersion;
  }
  /**
   * The entire FDL, expanded from a short link. It is the same as the
   * requested_link, if it is long.
   *
   * @param string $resolvedLink
   */
  public function setResolvedLink($resolvedLink)
  {
    $this->resolvedLink = $resolvedLink;
  }
  /**
   * @return string
   */
  public function getResolvedLink()
  {
    return $this->resolvedLink;
  }
  /**
   * Scion campaign value to be propagated by iSDK to Scion at app-reopen.
   *
   * @param string $utmCampaign
   */
  public function setUtmCampaign($utmCampaign)
  {
    $this->utmCampaign = $utmCampaign;
  }
  /**
   * @return string
   */
  public function getUtmCampaign()
  {
    return $this->utmCampaign;
  }
  /**
   * Scion content value to be propagated by iSDK to Scion at app-reopen.
   *
   * @param string $utmContent
   */
  public function setUtmContent($utmContent)
  {
    $this->utmContent = $utmContent;
  }
  /**
   * @return string
   */
  public function getUtmContent()
  {
    return $this->utmContent;
  }
  /**
   * Scion medium value to be propagated by iSDK to Scion at app-reopen.
   *
   * @param string $utmMedium
   */
  public function setUtmMedium($utmMedium)
  {
    $this->utmMedium = $utmMedium;
  }
  /**
   * @return string
   */
  public function getUtmMedium()
  {
    return $this->utmMedium;
  }
  /**
   * Scion source value to be propagated by iSDK to Scion at app-reopen.
   *
   * @param string $utmSource
   */
  public function setUtmSource($utmSource)
  {
    $this->utmSource = $utmSource;
  }
  /**
   * @return string
   */
  public function getUtmSource()
  {
    return $this->utmSource;
  }
  /**
   * Scion term value to be propagated by iSDK to Scion at app-reopen.
   *
   * @param string $utmTerm
   */
  public function setUtmTerm($utmTerm)
  {
    $this->utmTerm = $utmTerm;
  }
  /**
   * @return string
   */
  public function getUtmTerm()
  {
    return $this->utmTerm;
  }
  /**
   * Optional warnings associated this API request.
   *
   * @param DynamicLinkWarning[] $warning
   */
  public function setWarning($warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return DynamicLinkWarning[]
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetIosReopenAttributionResponse::class, 'Google_Service_FirebaseDynamicLinks_GetIosReopenAttributionResponse');
