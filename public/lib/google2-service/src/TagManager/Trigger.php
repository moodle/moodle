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

class Trigger extends \Google\Collection
{
  public const TYPE_eventTypeUnspecified = 'eventTypeUnspecified';
  public const TYPE_pageview = 'pageview';
  public const TYPE_domReady = 'domReady';
  public const TYPE_windowLoaded = 'windowLoaded';
  public const TYPE_customEvent = 'customEvent';
  public const TYPE_triggerGroup = 'triggerGroup';
  public const TYPE_init = 'init';
  public const TYPE_consentInit = 'consentInit';
  public const TYPE_serverPageview = 'serverPageview';
  public const TYPE_always = 'always';
  public const TYPE_firebaseAppException = 'firebaseAppException';
  public const TYPE_firebaseAppUpdate = 'firebaseAppUpdate';
  public const TYPE_firebaseCampaign = 'firebaseCampaign';
  public const TYPE_firebaseFirstOpen = 'firebaseFirstOpen';
  public const TYPE_firebaseInAppPurchase = 'firebaseInAppPurchase';
  public const TYPE_firebaseNotificationDismiss = 'firebaseNotificationDismiss';
  public const TYPE_firebaseNotificationForeground = 'firebaseNotificationForeground';
  public const TYPE_firebaseNotificationOpen = 'firebaseNotificationOpen';
  public const TYPE_firebaseNotificationReceive = 'firebaseNotificationReceive';
  public const TYPE_firebaseOsUpdate = 'firebaseOsUpdate';
  public const TYPE_firebaseSessionStart = 'firebaseSessionStart';
  public const TYPE_firebaseUserEngagement = 'firebaseUserEngagement';
  public const TYPE_formSubmission = 'formSubmission';
  public const TYPE_click = 'click';
  public const TYPE_linkClick = 'linkClick';
  public const TYPE_jsError = 'jsError';
  public const TYPE_historyChange = 'historyChange';
  public const TYPE_timer = 'timer';
  public const TYPE_ampClick = 'ampClick';
  public const TYPE_ampTimer = 'ampTimer';
  public const TYPE_ampScroll = 'ampScroll';
  public const TYPE_ampVisibility = 'ampVisibility';
  public const TYPE_youTubeVideo = 'youTubeVideo';
  public const TYPE_scrollDepth = 'scrollDepth';
  public const TYPE_elementVisibility = 'elementVisibility';
  protected $collection_key = 'parameter';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  protected $autoEventFilterType = Condition::class;
  protected $autoEventFilterDataType = 'array';
  protected $checkValidationType = Parameter::class;
  protected $checkValidationDataType = '';
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  protected $continuousTimeMinMillisecondsType = Parameter::class;
  protected $continuousTimeMinMillisecondsDataType = '';
  protected $customEventFilterType = Condition::class;
  protected $customEventFilterDataType = 'array';
  protected $eventNameType = Parameter::class;
  protected $eventNameDataType = '';
  protected $filterType = Condition::class;
  protected $filterDataType = 'array';
  /**
   * The fingerprint of the GTM Trigger as computed at storage time. This value
   * is recomputed whenever the trigger is modified.
   *
   * @var string
   */
  public $fingerprint;
  protected $horizontalScrollPercentageListType = Parameter::class;
  protected $horizontalScrollPercentageListDataType = '';
  protected $intervalType = Parameter::class;
  protected $intervalDataType = '';
  protected $intervalSecondsType = Parameter::class;
  protected $intervalSecondsDataType = '';
  protected $limitType = Parameter::class;
  protected $limitDataType = '';
  protected $maxTimerLengthSecondsType = Parameter::class;
  protected $maxTimerLengthSecondsDataType = '';
  /**
   * Trigger display name.
   *
   * @var string
   */
  public $name;
  /**
   * User notes on how to apply this trigger in the container.
   *
   * @var string
   */
  public $notes;
  protected $parameterType = Parameter::class;
  protected $parameterDataType = 'array';
  /**
   * Parent folder id.
   *
   * @var string
   */
  public $parentFolderId;
  /**
   * GTM Trigger's API relative path.
   *
   * @var string
   */
  public $path;
  protected $selectorType = Parameter::class;
  protected $selectorDataType = '';
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  protected $totalTimeMinMillisecondsType = Parameter::class;
  protected $totalTimeMinMillisecondsDataType = '';
  /**
   * The Trigger ID uniquely identifies the GTM Trigger.
   *
   * @var string
   */
  public $triggerId;
  /**
   * Defines the data layer event that causes this trigger.
   *
   * @var string
   */
  public $type;
  protected $uniqueTriggerIdType = Parameter::class;
  protected $uniqueTriggerIdDataType = '';
  protected $verticalScrollPercentageListType = Parameter::class;
  protected $verticalScrollPercentageListDataType = '';
  protected $visibilitySelectorType = Parameter::class;
  protected $visibilitySelectorDataType = '';
  protected $visiblePercentageMaxType = Parameter::class;
  protected $visiblePercentageMaxDataType = '';
  protected $visiblePercentageMinType = Parameter::class;
  protected $visiblePercentageMinDataType = '';
  protected $waitForTagsType = Parameter::class;
  protected $waitForTagsDataType = '';
  protected $waitForTagsTimeoutType = Parameter::class;
  protected $waitForTagsTimeoutDataType = '';
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
   * Used in the case of auto event tracking.
   *
   * @param Condition[] $autoEventFilter
   */
  public function setAutoEventFilter($autoEventFilter)
  {
    $this->autoEventFilter = $autoEventFilter;
  }
  /**
   * @return Condition[]
   */
  public function getAutoEventFilter()
  {
    return $this->autoEventFilter;
  }
  /**
   * Whether or not we should only fire tags if the form submit or link click
   * event is not cancelled by some other event handler (e.g. because of
   * validation). Only valid for Form Submission and Link Click triggers.
   *
   * @param Parameter $checkValidation
   */
  public function setCheckValidation(Parameter $checkValidation)
  {
    $this->checkValidation = $checkValidation;
  }
  /**
   * @return Parameter
   */
  public function getCheckValidation()
  {
    return $this->checkValidation;
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
   * A visibility trigger minimum continuous visible time (in milliseconds).
   * Only valid for AMP Visibility trigger.
   *
   * @param Parameter $continuousTimeMinMilliseconds
   */
  public function setContinuousTimeMinMilliseconds(Parameter $continuousTimeMinMilliseconds)
  {
    $this->continuousTimeMinMilliseconds = $continuousTimeMinMilliseconds;
  }
  /**
   * @return Parameter
   */
  public function getContinuousTimeMinMilliseconds()
  {
    return $this->continuousTimeMinMilliseconds;
  }
  /**
   * Used in the case of custom event, which is fired iff all Conditions are
   * true.
   *
   * @param Condition[] $customEventFilter
   */
  public function setCustomEventFilter($customEventFilter)
  {
    $this->customEventFilter = $customEventFilter;
  }
  /**
   * @return Condition[]
   */
  public function getCustomEventFilter()
  {
    return $this->customEventFilter;
  }
  /**
   * Name of the GTM event that is fired. Only valid for Timer triggers.
   *
   * @param Parameter $eventName
   */
  public function setEventName(Parameter $eventName)
  {
    $this->eventName = $eventName;
  }
  /**
   * @return Parameter
   */
  public function getEventName()
  {
    return $this->eventName;
  }
  /**
   * The trigger will only fire iff all Conditions are true.
   *
   * @param Condition[] $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Condition[]
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The fingerprint of the GTM Trigger as computed at storage time. This value
   * is recomputed whenever the trigger is modified.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * List of integer percentage values for scroll triggers. The trigger will
   * fire when each percentage is reached when the view is scrolled
   * horizontally. Only valid for AMP scroll triggers.
   *
   * @param Parameter $horizontalScrollPercentageList
   */
  public function setHorizontalScrollPercentageList(Parameter $horizontalScrollPercentageList)
  {
    $this->horizontalScrollPercentageList = $horizontalScrollPercentageList;
  }
  /**
   * @return Parameter
   */
  public function getHorizontalScrollPercentageList()
  {
    return $this->horizontalScrollPercentageList;
  }
  /**
   * Time between triggering recurring Timer Events (in milliseconds). Only
   * valid for Timer triggers.
   *
   * @param Parameter $interval
   */
  public function setInterval(Parameter $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return Parameter
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * Time between Timer Events to fire (in seconds). Only valid for AMP Timer
   * trigger.
   *
   * @param Parameter $intervalSeconds
   */
  public function setIntervalSeconds(Parameter $intervalSeconds)
  {
    $this->intervalSeconds = $intervalSeconds;
  }
  /**
   * @return Parameter
   */
  public function getIntervalSeconds()
  {
    return $this->intervalSeconds;
  }
  /**
   * Limit of the number of GTM events this Timer Trigger will fire. If no limit
   * is set, we will continue to fire GTM events until the user leaves the page.
   * Only valid for Timer triggers.
   *
   * @param Parameter $limit
   */
  public function setLimit(Parameter $limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return Parameter
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Max time to fire Timer Events (in seconds). Only valid for AMP Timer
   * trigger.
   *
   * @param Parameter $maxTimerLengthSeconds
   */
  public function setMaxTimerLengthSeconds(Parameter $maxTimerLengthSeconds)
  {
    $this->maxTimerLengthSeconds = $maxTimerLengthSeconds;
  }
  /**
   * @return Parameter
   */
  public function getMaxTimerLengthSeconds()
  {
    return $this->maxTimerLengthSeconds;
  }
  /**
   * Trigger display name.
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
   * User notes on how to apply this trigger in the container.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Additional parameters.
   *
   * @param Parameter[] $parameter
   */
  public function setParameter($parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return Parameter[]
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * Parent folder id.
   *
   * @param string $parentFolderId
   */
  public function setParentFolderId($parentFolderId)
  {
    $this->parentFolderId = $parentFolderId;
  }
  /**
   * @return string
   */
  public function getParentFolderId()
  {
    return $this->parentFolderId;
  }
  /**
   * GTM Trigger's API relative path.
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
   * A click trigger CSS selector (i.e. "a", "button" etc.). Only valid for AMP
   * Click trigger.
   *
   * @param Parameter $selector
   */
  public function setSelector(Parameter $selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return Parameter
   */
  public function getSelector()
  {
    return $this->selector;
  }
  /**
   * Auto generated link to the tag manager UI
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
  /**
   * A visibility trigger minimum total visible time (in milliseconds). Only
   * valid for AMP Visibility trigger.
   *
   * @param Parameter $totalTimeMinMilliseconds
   */
  public function setTotalTimeMinMilliseconds(Parameter $totalTimeMinMilliseconds)
  {
    $this->totalTimeMinMilliseconds = $totalTimeMinMilliseconds;
  }
  /**
   * @return Parameter
   */
  public function getTotalTimeMinMilliseconds()
  {
    return $this->totalTimeMinMilliseconds;
  }
  /**
   * The Trigger ID uniquely identifies the GTM Trigger.
   *
   * @param string $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
  /**
   * Defines the data layer event that causes this trigger.
   *
   * Accepted values: eventTypeUnspecified, pageview, domReady, windowLoaded,
   * customEvent, triggerGroup, init, consentInit, serverPageview, always,
   * firebaseAppException, firebaseAppUpdate, firebaseCampaign,
   * firebaseFirstOpen, firebaseInAppPurchase, firebaseNotificationDismiss,
   * firebaseNotificationForeground, firebaseNotificationOpen,
   * firebaseNotificationReceive, firebaseOsUpdate, firebaseSessionStart,
   * firebaseUserEngagement, formSubmission, click, linkClick, jsError,
   * historyChange, timer, ampClick, ampTimer, ampScroll, ampVisibility,
   * youTubeVideo, scrollDepth, elementVisibility
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
   * Globally unique id of the trigger that auto-generates this (a Form Submit,
   * Link Click or Timer listener) if any. Used to make incompatible auto-events
   * work together with trigger filtering based on trigger ids. This value is
   * populated during output generation since the tags implied by triggers don't
   * exist until then. Only valid for Form Submit, Link Click and Timer
   * triggers.
   *
   * @param Parameter $uniqueTriggerId
   */
  public function setUniqueTriggerId(Parameter $uniqueTriggerId)
  {
    $this->uniqueTriggerId = $uniqueTriggerId;
  }
  /**
   * @return Parameter
   */
  public function getUniqueTriggerId()
  {
    return $this->uniqueTriggerId;
  }
  /**
   * List of integer percentage values for scroll triggers. The trigger will
   * fire when each percentage is reached when the view is scrolled vertically.
   * Only valid for AMP scroll triggers.
   *
   * @param Parameter $verticalScrollPercentageList
   */
  public function setVerticalScrollPercentageList(Parameter $verticalScrollPercentageList)
  {
    $this->verticalScrollPercentageList = $verticalScrollPercentageList;
  }
  /**
   * @return Parameter
   */
  public function getVerticalScrollPercentageList()
  {
    return $this->verticalScrollPercentageList;
  }
  /**
   * A visibility trigger CSS selector (i.e. "#id"). Only valid for AMP
   * Visibility trigger.
   *
   * @param Parameter $visibilitySelector
   */
  public function setVisibilitySelector(Parameter $visibilitySelector)
  {
    $this->visibilitySelector = $visibilitySelector;
  }
  /**
   * @return Parameter
   */
  public function getVisibilitySelector()
  {
    return $this->visibilitySelector;
  }
  /**
   * A visibility trigger maximum percent visibility. Only valid for AMP
   * Visibility trigger.
   *
   * @param Parameter $visiblePercentageMax
   */
  public function setVisiblePercentageMax(Parameter $visiblePercentageMax)
  {
    $this->visiblePercentageMax = $visiblePercentageMax;
  }
  /**
   * @return Parameter
   */
  public function getVisiblePercentageMax()
  {
    return $this->visiblePercentageMax;
  }
  /**
   * A visibility trigger minimum percent visibility. Only valid for AMP
   * Visibility trigger.
   *
   * @param Parameter $visiblePercentageMin
   */
  public function setVisiblePercentageMin(Parameter $visiblePercentageMin)
  {
    $this->visiblePercentageMin = $visiblePercentageMin;
  }
  /**
   * @return Parameter
   */
  public function getVisiblePercentageMin()
  {
    return $this->visiblePercentageMin;
  }
  /**
   * Whether or not we should delay the form submissions or link opening until
   * all of the tags have fired (by preventing the default action and later
   * simulating the default action). Only valid for Form Submission and Link
   * Click triggers.
   *
   * @param Parameter $waitForTags
   */
  public function setWaitForTags(Parameter $waitForTags)
  {
    $this->waitForTags = $waitForTags;
  }
  /**
   * @return Parameter
   */
  public function getWaitForTags()
  {
    return $this->waitForTags;
  }
  /**
   * How long to wait (in milliseconds) for tags to fire when 'waits_for_tags'
   * above evaluates to true. Only valid for Form Submission and Link Click
   * triggers.
   *
   * @param Parameter $waitForTagsTimeout
   */
  public function setWaitForTagsTimeout(Parameter $waitForTagsTimeout)
  {
    $this->waitForTagsTimeout = $waitForTagsTimeout;
  }
  /**
   * @return Parameter
   */
  public function getWaitForTagsTimeout()
  {
    return $this->waitForTagsTimeout;
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
class_alias(Trigger::class, 'Google_Service_TagManager_Trigger');
