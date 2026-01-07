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

class GetIosPostInstallAttributionResponse extends \Google\Model
{
  /**
   * Unset.
   */
  public const ATTRIBUTION_CONFIDENCE_UNKNOWN_ATTRIBUTION_CONFIDENCE = 'UNKNOWN_ATTRIBUTION_CONFIDENCE';
  /**
   * Weak confidence, more than one matching link found or link suspected to be
   * false positive.
   */
  public const ATTRIBUTION_CONFIDENCE_WEAK = 'WEAK';
  /**
   * Default confidence, match based on device heuristics.
   */
  public const ATTRIBUTION_CONFIDENCE_DEFAULT = 'DEFAULT';
  /**
   * Unique confidence, match based on "unique match link to check" or other
   * means.
   */
  public const ATTRIBUTION_CONFIDENCE_UNIQUE = 'UNIQUE';
  /**
   * Unset.
   */
  public const REQUEST_IP_VERSION_UNKNOWN_IP_VERSION = 'UNKNOWN_IP_VERSION';
  /**
   * Request made from an IPv4 IP address.
   */
  public const REQUEST_IP_VERSION_IP_V4 = 'IP_V4';
  /**
   * Request made from an IPv6 IP address.
   */
  public const REQUEST_IP_VERSION_IP_V6 = 'IP_V6';
  /**
   * The minimum version for app, specified by dev through ?imv= parameter.
   * Return to iSDK to allow app to evaluate if current version meets this.
   *
   * @var string
   */
  public $appMinimumVersion;
  /**
   * The confidence of the returned attribution.
   *
   * @var string
   */
  public $attributionConfidence;
  /**
   * The deep-link attributed post-install via one of several techniques (device
   * heuristics, copy unique).
   *
   * @var string
   */
  public $deepLink;
  /**
   * User-agent specific custom-scheme URIs for iSDK to open. This will be set
   * according to the user-agent tha the click was originally made in. There is
   * no Safari-equivalent custom-scheme open URLs. ie:
   * googlechrome://www.example.com ie: firefox://open-
   * url?url=http://www.example.com ie: opera-http://example.com
   *
   * @var string
   */
  public $externalBrowserDestinationLink;
  /**
   * The link to navigate to update the app if min version is not met. This is
   * either (in order): 1) fallback link (from ?ifl= parameter, if specified by
   * developer) or 2) AppStore URL (from ?isi= parameter, if specified), or 3)
   * the payload link (from required link= parameter).
   *
   * @var string
   */
  public $fallbackLink;
  /**
   * Invitation ID attributed post-install via one of several techniques (device
   * heuristics, copy unique).
   *
   * @var string
   */
  public $invitationId;
  /**
   * Instruction for iSDK to attemmpt to perform strong match. For instance, if
   * browser does not support/allow cookie or outside of support browsers, this
   * will be false.
   *
   * @var bool
   */
  public $isStrongMatchExecutable;
  /**
   * Describes why match failed, ie: "discarded due to low confidence". This
   * message will be publicly visible.
   *
   * @var string
   */
  public $matchMessage;
  /**
   * Which IP version the request was made from.
   *
   * @var string
   */
  public $requestIpVersion;
  /**
   * Entire FDL (short or long) attributed post-install via one of several
   * techniques (device heuristics, copy unique).
   *
   * @var string
   */
  public $requestedLink;
  /**
   * The entire FDL, expanded from a short link. It is the same as the
   * requested_link, if it is long. Parameters from this should not be used
   * directly (ie: server can default utm_[campaign|medium|source] to a value
   * when requested_link lack them, server determine the best fallback_link when
   * requested_link specifies >1 fallback links).
   *
   * @var string
   */
  public $resolvedLink;
  /**
   * Scion campaign value to be propagated by iSDK to Scion at post-install.
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
   * Scion medium value to be propagated by iSDK to Scion at post-install.
   *
   * @var string
   */
  public $utmMedium;
  /**
   * Scion source value to be propagated by iSDK to Scion at post-install.
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

  /**
   * The minimum version for app, specified by dev through ?imv= parameter.
   * Return to iSDK to allow app to evaluate if current version meets this.
   *
   * @param string $appMinimumVersion
   */
  public function setAppMinimumVersion($appMinimumVersion)
  {
    $this->appMinimumVersion = $appMinimumVersion;
  }
  /**
   * @return string
   */
  public function getAppMinimumVersion()
  {
    return $this->appMinimumVersion;
  }
  /**
   * The confidence of the returned attribution.
   *
   * Accepted values: UNKNOWN_ATTRIBUTION_CONFIDENCE, WEAK, DEFAULT, UNIQUE
   *
   * @param self::ATTRIBUTION_CONFIDENCE_* $attributionConfidence
   */
  public function setAttributionConfidence($attributionConfidence)
  {
    $this->attributionConfidence = $attributionConfidence;
  }
  /**
   * @return self::ATTRIBUTION_CONFIDENCE_*
   */
  public function getAttributionConfidence()
  {
    return $this->attributionConfidence;
  }
  /**
   * The deep-link attributed post-install via one of several techniques (device
   * heuristics, copy unique).
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
   * User-agent specific custom-scheme URIs for iSDK to open. This will be set
   * according to the user-agent tha the click was originally made in. There is
   * no Safari-equivalent custom-scheme open URLs. ie:
   * googlechrome://www.example.com ie: firefox://open-
   * url?url=http://www.example.com ie: opera-http://example.com
   *
   * @param string $externalBrowserDestinationLink
   */
  public function setExternalBrowserDestinationLink($externalBrowserDestinationLink)
  {
    $this->externalBrowserDestinationLink = $externalBrowserDestinationLink;
  }
  /**
   * @return string
   */
  public function getExternalBrowserDestinationLink()
  {
    return $this->externalBrowserDestinationLink;
  }
  /**
   * The link to navigate to update the app if min version is not met. This is
   * either (in order): 1) fallback link (from ?ifl= parameter, if specified by
   * developer) or 2) AppStore URL (from ?isi= parameter, if specified), or 3)
   * the payload link (from required link= parameter).
   *
   * @param string $fallbackLink
   */
  public function setFallbackLink($fallbackLink)
  {
    $this->fallbackLink = $fallbackLink;
  }
  /**
   * @return string
   */
  public function getFallbackLink()
  {
    return $this->fallbackLink;
  }
  /**
   * Invitation ID attributed post-install via one of several techniques (device
   * heuristics, copy unique).
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
   * Instruction for iSDK to attemmpt to perform strong match. For instance, if
   * browser does not support/allow cookie or outside of support browsers, this
   * will be false.
   *
   * @param bool $isStrongMatchExecutable
   */
  public function setIsStrongMatchExecutable($isStrongMatchExecutable)
  {
    $this->isStrongMatchExecutable = $isStrongMatchExecutable;
  }
  /**
   * @return bool
   */
  public function getIsStrongMatchExecutable()
  {
    return $this->isStrongMatchExecutable;
  }
  /**
   * Describes why match failed, ie: "discarded due to low confidence". This
   * message will be publicly visible.
   *
   * @param string $matchMessage
   */
  public function setMatchMessage($matchMessage)
  {
    $this->matchMessage = $matchMessage;
  }
  /**
   * @return string
   */
  public function getMatchMessage()
  {
    return $this->matchMessage;
  }
  /**
   * Which IP version the request was made from.
   *
   * Accepted values: UNKNOWN_IP_VERSION, IP_V4, IP_V6
   *
   * @param self::REQUEST_IP_VERSION_* $requestIpVersion
   */
  public function setRequestIpVersion($requestIpVersion)
  {
    $this->requestIpVersion = $requestIpVersion;
  }
  /**
   * @return self::REQUEST_IP_VERSION_*
   */
  public function getRequestIpVersion()
  {
    return $this->requestIpVersion;
  }
  /**
   * Entire FDL (short or long) attributed post-install via one of several
   * techniques (device heuristics, copy unique).
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
   * The entire FDL, expanded from a short link. It is the same as the
   * requested_link, if it is long. Parameters from this should not be used
   * directly (ie: server can default utm_[campaign|medium|source] to a value
   * when requested_link lack them, server determine the best fallback_link when
   * requested_link specifies >1 fallback links).
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
   * Scion campaign value to be propagated by iSDK to Scion at post-install.
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
   * Scion medium value to be propagated by iSDK to Scion at post-install.
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
   * Scion source value to be propagated by iSDK to Scion at post-install.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetIosPostInstallAttributionResponse::class, 'Google_Service_FirebaseDynamicLinks_GetIosPostInstallAttributionResponse');
