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

class Tag extends \Google\Collection
{
  public const TAG_FIRING_OPTION_tagFiringOptionUnspecified = 'tagFiringOptionUnspecified';
  /**
   * Tag can be fired multiple times per event.
   */
  public const TAG_FIRING_OPTION_unlimited = 'unlimited';
  /**
   * Tag can only be fired per event but can be fired multiple times per load
   * (e.g., app load or page load).
   */
  public const TAG_FIRING_OPTION_oncePerEvent = 'oncePerEvent';
  /**
   * Tag can only be fired per load (e.g., app load or page load).
   */
  public const TAG_FIRING_OPTION_oncePerLoad = 'oncePerLoad';
  protected $collection_key = 'teardownTag';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * Blocking trigger IDs. If any of the listed triggers evaluate to true, the
   * tag will not fire.
   *
   * @var string[]
   */
  public $blockingTriggerId;
  protected $consentSettingsType = TagConsentSetting::class;
  protected $consentSettingsDataType = '';
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * The fingerprint of the GTM Tag as computed at storage time. This value is
   * recomputed whenever the tag is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Firing trigger IDs. A tag will fire when any of the listed triggers are
   * true and all of its blockingTriggerIds (if any specified) are false.
   *
   * @var string[]
   */
  public $firingTriggerId;
  /**
   * If set to true, this tag will only fire in the live environment (e.g. not
   * in preview or debug mode).
   *
   * @var bool
   */
  public $liveOnly;
  protected $monitoringMetadataType = Parameter::class;
  protected $monitoringMetadataDataType = '';
  /**
   * If non-empty, then the tag display name will be included in the monitoring
   * metadata map using the key specified.
   *
   * @var string
   */
  public $monitoringMetadataTagNameKey;
  /**
   * Tag display name.
   *
   * @var string
   */
  public $name;
  /**
   * User notes on how to apply this tag in the container.
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
   * GTM Tag's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Indicates whether the tag is paused, which prevents the tag from firing.
   *
   * @var bool
   */
  public $paused;
  protected $priorityType = Parameter::class;
  protected $priorityDataType = '';
  /**
   * The end timestamp in milliseconds to schedule a tag.
   *
   * @var string
   */
  public $scheduleEndMs;
  /**
   * The start timestamp in milliseconds to schedule a tag.
   *
   * @var string
   */
  public $scheduleStartMs;
  protected $setupTagType = SetupTag::class;
  protected $setupTagDataType = 'array';
  /**
   * Option to fire this tag.
   *
   * @var string
   */
  public $tagFiringOption;
  /**
   * The Tag ID uniquely identifies the GTM Tag.
   *
   * @var string
   */
  public $tagId;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  protected $teardownTagType = TeardownTag::class;
  protected $teardownTagDataType = 'array';
  /**
   * GTM Tag Type.
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
   * Blocking trigger IDs. If any of the listed triggers evaluate to true, the
   * tag will not fire.
   *
   * @param string[] $blockingTriggerId
   */
  public function setBlockingTriggerId($blockingTriggerId)
  {
    $this->blockingTriggerId = $blockingTriggerId;
  }
  /**
   * @return string[]
   */
  public function getBlockingTriggerId()
  {
    return $this->blockingTriggerId;
  }
  /**
   * Consent settings of a tag.
   *
   * @param TagConsentSetting $consentSettings
   */
  public function setConsentSettings(TagConsentSetting $consentSettings)
  {
    $this->consentSettings = $consentSettings;
  }
  /**
   * @return TagConsentSetting
   */
  public function getConsentSettings()
  {
    return $this->consentSettings;
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
   * The fingerprint of the GTM Tag as computed at storage time. This value is
   * recomputed whenever the tag is modified.
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
   * Firing trigger IDs. A tag will fire when any of the listed triggers are
   * true and all of its blockingTriggerIds (if any specified) are false.
   *
   * @param string[] $firingTriggerId
   */
  public function setFiringTriggerId($firingTriggerId)
  {
    $this->firingTriggerId = $firingTriggerId;
  }
  /**
   * @return string[]
   */
  public function getFiringTriggerId()
  {
    return $this->firingTriggerId;
  }
  /**
   * If set to true, this tag will only fire in the live environment (e.g. not
   * in preview or debug mode).
   *
   * @param bool $liveOnly
   */
  public function setLiveOnly($liveOnly)
  {
    $this->liveOnly = $liveOnly;
  }
  /**
   * @return bool
   */
  public function getLiveOnly()
  {
    return $this->liveOnly;
  }
  /**
   * A map of key-value pairs of tag metadata to be included in the event data
   * for tag monitoring. Notes: - This parameter must be type MAP. - Each
   * parameter in the map are type TEMPLATE, however cannot contain variable
   * references.
   *
   * @param Parameter $monitoringMetadata
   */
  public function setMonitoringMetadata(Parameter $monitoringMetadata)
  {
    $this->monitoringMetadata = $monitoringMetadata;
  }
  /**
   * @return Parameter
   */
  public function getMonitoringMetadata()
  {
    return $this->monitoringMetadata;
  }
  /**
   * If non-empty, then the tag display name will be included in the monitoring
   * metadata map using the key specified.
   *
   * @param string $monitoringMetadataTagNameKey
   */
  public function setMonitoringMetadataTagNameKey($monitoringMetadataTagNameKey)
  {
    $this->monitoringMetadataTagNameKey = $monitoringMetadataTagNameKey;
  }
  /**
   * @return string
   */
  public function getMonitoringMetadataTagNameKey()
  {
    return $this->monitoringMetadataTagNameKey;
  }
  /**
   * Tag display name.
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
   * User notes on how to apply this tag in the container.
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
   * The tag's parameters.
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
   * GTM Tag's API relative path.
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
   * Indicates whether the tag is paused, which prevents the tag from firing.
   *
   * @param bool $paused
   */
  public function setPaused($paused)
  {
    $this->paused = $paused;
  }
  /**
   * @return bool
   */
  public function getPaused()
  {
    return $this->paused;
  }
  /**
   * User defined numeric priority of the tag. Tags are fired asynchronously in
   * order of priority. Tags with higher numeric value fire first. A tag's
   * priority can be a positive or negative value. The default value is 0.
   *
   * @param Parameter $priority
   */
  public function setPriority(Parameter $priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return Parameter
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * The end timestamp in milliseconds to schedule a tag.
   *
   * @param string $scheduleEndMs
   */
  public function setScheduleEndMs($scheduleEndMs)
  {
    $this->scheduleEndMs = $scheduleEndMs;
  }
  /**
   * @return string
   */
  public function getScheduleEndMs()
  {
    return $this->scheduleEndMs;
  }
  /**
   * The start timestamp in milliseconds to schedule a tag.
   *
   * @param string $scheduleStartMs
   */
  public function setScheduleStartMs($scheduleStartMs)
  {
    $this->scheduleStartMs = $scheduleStartMs;
  }
  /**
   * @return string
   */
  public function getScheduleStartMs()
  {
    return $this->scheduleStartMs;
  }
  /**
   * The list of setup tags. Currently we only allow one.
   *
   * @param SetupTag[] $setupTag
   */
  public function setSetupTag($setupTag)
  {
    $this->setupTag = $setupTag;
  }
  /**
   * @return SetupTag[]
   */
  public function getSetupTag()
  {
    return $this->setupTag;
  }
  /**
   * Option to fire this tag.
   *
   * Accepted values: tagFiringOptionUnspecified, unlimited, oncePerEvent,
   * oncePerLoad
   *
   * @param self::TAG_FIRING_OPTION_* $tagFiringOption
   */
  public function setTagFiringOption($tagFiringOption)
  {
    $this->tagFiringOption = $tagFiringOption;
  }
  /**
   * @return self::TAG_FIRING_OPTION_*
   */
  public function getTagFiringOption()
  {
    return $this->tagFiringOption;
  }
  /**
   * The Tag ID uniquely identifies the GTM Tag.
   *
   * @param string $tagId
   */
  public function setTagId($tagId)
  {
    $this->tagId = $tagId;
  }
  /**
   * @return string
   */
  public function getTagId()
  {
    return $this->tagId;
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
   * The list of teardown tags. Currently we only allow one.
   *
   * @param TeardownTag[] $teardownTag
   */
  public function setTeardownTag($teardownTag)
  {
    $this->teardownTag = $teardownTag;
  }
  /**
   * @return TeardownTag[]
   */
  public function getTeardownTag()
  {
    return $this->teardownTag;
  }
  /**
   * GTM Tag Type.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
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
class_alias(Tag::class, 'Google_Service_TagManager_Tag');
