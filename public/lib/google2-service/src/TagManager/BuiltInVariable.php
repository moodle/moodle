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

namespace Google\Service\TagManager;

class BuiltInVariable extends \Google\Model
{
  public const TYPE_builtInVariableTypeUnspecified = 'builtInVariableTypeUnspecified';
  public const TYPE_pageUrl = 'pageUrl';
  public const TYPE_pageHostname = 'pageHostname';
  public const TYPE_pagePath = 'pagePath';
  public const TYPE_referrer = 'referrer';
  /**
   * For web or mobile.
   */
  public const TYPE_event = 'event';
  public const TYPE_clickElement = 'clickElement';
  public const TYPE_clickClasses = 'clickClasses';
  public const TYPE_clickId = 'clickId';
  public const TYPE_clickTarget = 'clickTarget';
  public const TYPE_clickUrl = 'clickUrl';
  public const TYPE_clickText = 'clickText';
  public const TYPE_firstPartyServingUrl = 'firstPartyServingUrl';
  public const TYPE_formElement = 'formElement';
  public const TYPE_formClasses = 'formClasses';
  public const TYPE_formId = 'formId';
  public const TYPE_formTarget = 'formTarget';
  public const TYPE_formUrl = 'formUrl';
  public const TYPE_formText = 'formText';
  public const TYPE_errorMessage = 'errorMessage';
  public const TYPE_errorUrl = 'errorUrl';
  public const TYPE_errorLine = 'errorLine';
  public const TYPE_newHistoryUrl = 'newHistoryUrl';
  public const TYPE_oldHistoryUrl = 'oldHistoryUrl';
  public const TYPE_newHistoryFragment = 'newHistoryFragment';
  public const TYPE_oldHistoryFragment = 'oldHistoryFragment';
  public const TYPE_newHistoryState = 'newHistoryState';
  public const TYPE_oldHistoryState = 'oldHistoryState';
  public const TYPE_historySource = 'historySource';
  /**
   * For web or mobile.
   */
  public const TYPE_containerVersion = 'containerVersion';
  public const TYPE_debugMode = 'debugMode';
  /**
   * For web or mobile.
   */
  public const TYPE_randomNumber = 'randomNumber';
  /**
   * For web or mobile.
   */
  public const TYPE_containerId = 'containerId';
  public const TYPE_appId = 'appId';
  public const TYPE_appName = 'appName';
  public const TYPE_appVersionCode = 'appVersionCode';
  public const TYPE_appVersionName = 'appVersionName';
  public const TYPE_language = 'language';
  public const TYPE_osVersion = 'osVersion';
  public const TYPE_platform = 'platform';
  public const TYPE_sdkVersion = 'sdkVersion';
  public const TYPE_deviceName = 'deviceName';
  public const TYPE_resolution = 'resolution';
  public const TYPE_advertiserId = 'advertiserId';
  public const TYPE_advertisingTrackingEnabled = 'advertisingTrackingEnabled';
  public const TYPE_htmlId = 'htmlId';
  public const TYPE_environmentName = 'environmentName';
  public const TYPE_ampBrowserLanguage = 'ampBrowserLanguage';
  public const TYPE_ampCanonicalPath = 'ampCanonicalPath';
  public const TYPE_ampCanonicalUrl = 'ampCanonicalUrl';
  public const TYPE_ampCanonicalHost = 'ampCanonicalHost';
  public const TYPE_ampReferrer = 'ampReferrer';
  public const TYPE_ampTitle = 'ampTitle';
  public const TYPE_ampClientId = 'ampClientId';
  public const TYPE_ampClientTimezone = 'ampClientTimezone';
  public const TYPE_ampClientTimestamp = 'ampClientTimestamp';
  public const TYPE_ampClientScreenWidth = 'ampClientScreenWidth';
  public const TYPE_ampClientScreenHeight = 'ampClientScreenHeight';
  public const TYPE_ampClientScrollX = 'ampClientScrollX';
  public const TYPE_ampClientScrollY = 'ampClientScrollY';
  public const TYPE_ampClientMaxScrollX = 'ampClientMaxScrollX';
  public const TYPE_ampClientMaxScrollY = 'ampClientMaxScrollY';
  public const TYPE_ampTotalEngagedTime = 'ampTotalEngagedTime';
  public const TYPE_ampPageViewId = 'ampPageViewId';
  public const TYPE_ampPageLoadTime = 'ampPageLoadTime';
  public const TYPE_ampPageDownloadTime = 'ampPageDownloadTime';
  public const TYPE_ampGtmEvent = 'ampGtmEvent';
  public const TYPE_eventName = 'eventName';
  public const TYPE_firebaseEventParameterCampaign = 'firebaseEventParameterCampaign';
  public const TYPE_firebaseEventParameterCampaignAclid = 'firebaseEventParameterCampaignAclid';
  public const TYPE_firebaseEventParameterCampaignAnid = 'firebaseEventParameterCampaignAnid';
  public const TYPE_firebaseEventParameterCampaignClickTimestamp = 'firebaseEventParameterCampaignClickTimestamp';
  public const TYPE_firebaseEventParameterCampaignContent = 'firebaseEventParameterCampaignContent';
  public const TYPE_firebaseEventParameterCampaignCp1 = 'firebaseEventParameterCampaignCp1';
  public const TYPE_firebaseEventParameterCampaignGclid = 'firebaseEventParameterCampaignGclid';
  public const TYPE_firebaseEventParameterCampaignSource = 'firebaseEventParameterCampaignSource';
  public const TYPE_firebaseEventParameterCampaignTerm = 'firebaseEventParameterCampaignTerm';
  public const TYPE_firebaseEventParameterCurrency = 'firebaseEventParameterCurrency';
  public const TYPE_firebaseEventParameterDynamicLinkAcceptTime = 'firebaseEventParameterDynamicLinkAcceptTime';
  public const TYPE_firebaseEventParameterDynamicLinkLinkid = 'firebaseEventParameterDynamicLinkLinkid';
  public const TYPE_firebaseEventParameterNotificationMessageDeviceTime = 'firebaseEventParameterNotificationMessageDeviceTime';
  public const TYPE_firebaseEventParameterNotificationMessageId = 'firebaseEventParameterNotificationMessageId';
  public const TYPE_firebaseEventParameterNotificationMessageName = 'firebaseEventParameterNotificationMessageName';
  public const TYPE_firebaseEventParameterNotificationMessageTime = 'firebaseEventParameterNotificationMessageTime';
  public const TYPE_firebaseEventParameterNotificationTopic = 'firebaseEventParameterNotificationTopic';
  public const TYPE_firebaseEventParameterPreviousAppVersion = 'firebaseEventParameterPreviousAppVersion';
  public const TYPE_firebaseEventParameterPreviousOsVersion = 'firebaseEventParameterPreviousOsVersion';
  public const TYPE_firebaseEventParameterPrice = 'firebaseEventParameterPrice';
  public const TYPE_firebaseEventParameterProductId = 'firebaseEventParameterProductId';
  public const TYPE_firebaseEventParameterQuantity = 'firebaseEventParameterQuantity';
  public const TYPE_firebaseEventParameterValue = 'firebaseEventParameterValue';
  public const TYPE_videoProvider = 'videoProvider';
  public const TYPE_videoUrl = 'videoUrl';
  public const TYPE_videoTitle = 'videoTitle';
  public const TYPE_videoDuration = 'videoDuration';
  public const TYPE_videoPercent = 'videoPercent';
  public const TYPE_videoVisible = 'videoVisible';
  public const TYPE_videoStatus = 'videoStatus';
  public const TYPE_videoCurrentTime = 'videoCurrentTime';
  public const TYPE_scrollDepthThreshold = 'scrollDepthThreshold';
  public const TYPE_scrollDepthUnits = 'scrollDepthUnits';
  public const TYPE_scrollDepthDirection = 'scrollDepthDirection';
  public const TYPE_elementVisibilityRatio = 'elementVisibilityRatio';
  public const TYPE_elementVisibilityTime = 'elementVisibilityTime';
  public const TYPE_elementVisibilityFirstTime = 'elementVisibilityFirstTime';
  public const TYPE_elementVisibilityRecentTime = 'elementVisibilityRecentTime';
  public const TYPE_requestPath = 'requestPath';
  public const TYPE_requestMethod = 'requestMethod';
  public const TYPE_clientName = 'clientName';
  public const TYPE_queryString = 'queryString';
  public const TYPE_serverPageLocationUrl = 'serverPageLocationUrl';
  public const TYPE_serverPageLocationPath = 'serverPageLocationPath';
  public const TYPE_serverPageLocationHostname = 'serverPageLocationHostname';
  public const TYPE_visitorRegion = 'visitorRegion';
  public const TYPE_analyticsClientId = 'analyticsClientId';
  public const TYPE_analyticsSessionId = 'analyticsSessionId';
  public const TYPE_analyticsSessionNumber = 'analyticsSessionNumber';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * Name of the built-in variable to be used to refer to the built-in variable.
   *
   * @var string
   */
  public $name;
  /**
   * GTM BuiltInVariable's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Type of built-in variable.
   *
   * @var string
   */
  public $type;
  /**
   * GTM Workspace ID.
   *
   * @var string
   */
  public $workspaceId;

  /**
   * GTM Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * GTM Container ID.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * Name of the built-in variable to be used to refer to the built-in variable.
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
   * GTM BuiltInVariable's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Type of built-in variable.
   *
   * Accepted values: builtInVariableTypeUnspecified, pageUrl, pageHostname,
   * pagePath, referrer, event, clickElement, clickClasses, clickId,
   * clickTarget, clickUrl, clickText, firstPartyServingUrl, formElement,
   * formClasses, formId, formTarget, formUrl, formText, errorMessage, errorUrl,
   * errorLine, newHistoryUrl, oldHistoryUrl, newHistoryFragment,
   * oldHistoryFragment, newHistoryState, oldHistoryState, historySource,
   * containerVersion, debugMode, randomNumber, containerId, appId, appName,
   * appVersionCode, appVersionName, language, osVersion, platform, sdkVersion,
   * deviceName, resolution, advertiserId, advertisingTrackingEnabled, htmlId,
   * environmentName, ampBrowserLanguage, ampCanonicalPath, ampCanonicalUrl,
   * ampCanonicalHost, ampReferrer, ampTitle, ampClientId, ampClientTimezone,
   * ampClientTimestamp, ampClientScreenWidth, ampClientScreenHeight,
   * ampClientScrollX, ampClientScrollY, ampClientMaxScrollX,
   * ampClientMaxScrollY, ampTotalEngagedTime, ampPageViewId, ampPageLoadTime,
   * ampPageDownloadTime, ampGtmEvent, eventName,
   * firebaseEventParameterCampaign, firebaseEventParameterCampaignAclid,
   * firebaseEventParameterCampaignAnid,
   * firebaseEventParameterCampaignClickTimestamp,
   * firebaseEventParameterCampaignContent, firebaseEventParameterCampaignCp1,
   * firebaseEventParameterCampaignGclid, firebaseEventParameterCampaignSource,
   * firebaseEventParameterCampaignTerm, firebaseEventParameterCurrency,
   * firebaseEventParameterDynamicLinkAcceptTime,
   * firebaseEventParameterDynamicLinkLinkid,
   * firebaseEventParameterNotificationMessageDeviceTime,
   * firebaseEventParameterNotificationMessageId,
   * firebaseEventParameterNotificationMessageName,
   * firebaseEventParameterNotificationMessageTime,
   * firebaseEventParameterNotificationTopic,
   * firebaseEventParameterPreviousAppVersion,
   * firebaseEventParameterPreviousOsVersion, firebaseEventParameterPrice,
   * firebaseEventParameterProductId, firebaseEventParameterQuantity,
   * firebaseEventParameterValue, videoProvider, videoUrl, videoTitle,
   * videoDuration, videoPercent, videoVisible, videoStatus, videoCurrentTime,
   * scrollDepthThreshold, scrollDepthUnits, scrollDepthDirection,
   * elementVisibilityRatio, elementVisibilityTime, elementVisibilityFirstTime,
   * elementVisibilityRecentTime, requestPath, requestMethod, clientName,
   * queryString, serverPageLocationUrl, serverPageLocationPath,
   * serverPageLocationHostname, visitorRegion, analyticsClientId,
   * analyticsSessionId, analyticsSessionNumber
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * GTM Workspace ID.
   *
   * @param string $workspaceId
   */
  public function setWorkspaceId($workspaceId)
  {
    $this->workspaceId = $workspaceId;
  }
  /**
   * @return string
   */
  public function getWorkspaceId()
  {
    return $this->workspaceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuiltInVariable::class, 'Google_Service_TagManager_BuiltInVariable');
