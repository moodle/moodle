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

namespace Google\Service\YouTube;

class LiveBroadcastContentDetails extends \Google\Model
{
  public const CLOSED_CAPTIONS_TYPE_closedCaptionsTypeUnspecified = 'closedCaptionsTypeUnspecified';
  public const CLOSED_CAPTIONS_TYPE_closedCaptionsDisabled = 'closedCaptionsDisabled';
  public const CLOSED_CAPTIONS_TYPE_closedCaptionsHttpPost = 'closedCaptionsHttpPost';
  public const CLOSED_CAPTIONS_TYPE_closedCaptionsEmbedded = 'closedCaptionsEmbedded';
  public const LATENCY_PREFERENCE_latencyPreferenceUnspecified = 'latencyPreferenceUnspecified';
  /**
   * Best for: highest quality viewer playbacks and higher resolutions.
   */
  public const LATENCY_PREFERENCE_normal = 'normal';
  /**
   * Best for: near real-time interaction, with minimal playback buffering.
   */
  public const LATENCY_PREFERENCE_low = 'low';
  /**
   * Best for: real-time interaction Does not support: Closed captions, 1440p,
   * and 4k resolutions
   */
  public const LATENCY_PREFERENCE_ultraLow = 'ultraLow';
  public const PROJECTION_projectionUnspecified = 'projectionUnspecified';
  public const PROJECTION_rectangular = 'rectangular';
  public const PROJECTION_value_360 = '360';
  public const PROJECTION_mesh = 'mesh';
  public const STEREO_LAYOUT_stereoLayoutUnspecified = 'stereoLayoutUnspecified';
  public const STEREO_LAYOUT_mono = 'mono';
  public const STEREO_LAYOUT_leftRight = 'leftRight';
  public const STEREO_LAYOUT_topBottom = 'topBottom';
  /**
   * This value uniquely identifies the live stream bound to the broadcast.
   *
   * @var string
   */
  public $boundStreamId;
  /**
   * The date and time that the live stream referenced by boundStreamId was last
   * updated.
   *
   * @var string
   */
  public $boundStreamLastUpdateTimeMs;
  /**
   * @var string
   */
  public $closedCaptionsType;
  /**
   * This setting indicates whether auto start is enabled for this broadcast.
   * The default value for this property is false. This setting can only be used
   * by Events.
   *
   * @var bool
   */
  public $enableAutoStart;
  /**
   * This setting indicates whether auto stop is enabled for this broadcast. The
   * default value for this property is false. This setting can only be used by
   * Events.
   *
   * @var bool
   */
  public $enableAutoStop;
  /**
   * This setting indicates whether HTTP POST closed captioning is enabled for
   * this broadcast. The ingestion URL of the closed captions is returned
   * through the liveStreams API. This is mutually exclusive with using the
   * closed_captions_type property, and is equivalent to setting
   * closed_captions_type to CLOSED_CAPTIONS_HTTP_POST.
   *
   * @deprecated
   * @var bool
   */
  public $enableClosedCaptions;
  /**
   * This setting indicates whether YouTube should enable content encryption for
   * the broadcast.
   *
   * @var bool
   */
  public $enableContentEncryption;
  /**
   * This setting determines whether viewers can access DVR controls while
   * watching the video. DVR controls enable the viewer to control the video
   * playback experience by pausing, rewinding, or fast forwarding content. The
   * default value for this property is true. *Important:* You must set the
   * value to true and also set the enableArchive property's value to true if
   * you want to make playback available immediately after the broadcast ends.
   *
   * @var bool
   */
  public $enableDvr;
  /**
   * This setting indicates whether the broadcast video can be played in an
   * embedded player. If you choose to archive the video (using the
   * enableArchive property), this setting will also apply to the archived
   * video.
   *
   * @var bool
   */
  public $enableEmbed;
  /**
   * Indicates whether this broadcast has low latency enabled.
   *
   * @deprecated
   * @var bool
   */
  public $enableLowLatency;
  /**
   * If both this and enable_low_latency are set, they must match.
   * LATENCY_NORMAL should match enable_low_latency=false LATENCY_LOW should
   * match enable_low_latency=true LATENCY_ULTRA_LOW should have
   * enable_low_latency omitted.
   *
   * @var string
   */
  public $latencyPreference;
  /**
   * The mesh for projecting the video if projection is mesh. The mesh value
   * must be a UTF-8 string containing the base-64 encoding of 3D mesh data that
   * follows the Spherical Video V2 RFC specification for an mshp box, excluding
   * the box size and type but including the following four reserved zero bytes
   * for the version and flags.
   *
   * @var string
   */
  public $mesh;
  protected $monitorStreamType = MonitorStreamInfo::class;
  protected $monitorStreamDataType = '';
  /**
   * The projection format of this broadcast. This defaults to rectangular.
   *
   * @var string
   */
  public $projection;
  /**
   * Automatically start recording after the event goes live. The default value
   * for this property is true. *Important:* You must also set the enableDvr
   * property's value to true if you want the playback to be available
   * immediately after the broadcast ends. If you set this property's value to
   * true but do not also set the enableDvr property to true, there may be a
   * delay of around one day before the archived video will be available for
   * playback.
   *
   * @var bool
   */
  public $recordFromStart;
  /**
   * This setting indicates whether the broadcast should automatically begin
   * with an in-stream slate when you update the broadcast's status to live.
   * After updating the status, you then need to send a liveCuepoints.insert
   * request that sets the cuepoint's eventState to end to remove the in-stream
   * slate and make your broadcast stream visible to viewers.
   *
   * @deprecated
   * @var bool
   */
  public $startWithSlate;
  /**
   * The 3D stereo layout of this broadcast. This defaults to mono.
   *
   * @var string
   */
  public $stereoLayout;

  /**
   * This value uniquely identifies the live stream bound to the broadcast.
   *
   * @param string $boundStreamId
   */
  public function setBoundStreamId($boundStreamId)
  {
    $this->boundStreamId = $boundStreamId;
  }
  /**
   * @return string
   */
  public function getBoundStreamId()
  {
    return $this->boundStreamId;
  }
  /**
   * The date and time that the live stream referenced by boundStreamId was last
   * updated.
   *
   * @param string $boundStreamLastUpdateTimeMs
   */
  public function setBoundStreamLastUpdateTimeMs($boundStreamLastUpdateTimeMs)
  {
    $this->boundStreamLastUpdateTimeMs = $boundStreamLastUpdateTimeMs;
  }
  /**
   * @return string
   */
  public function getBoundStreamLastUpdateTimeMs()
  {
    return $this->boundStreamLastUpdateTimeMs;
  }
  /**
   * @param self::CLOSED_CAPTIONS_TYPE_* $closedCaptionsType
   */
  public function setClosedCaptionsType($closedCaptionsType)
  {
    $this->closedCaptionsType = $closedCaptionsType;
  }
  /**
   * @return self::CLOSED_CAPTIONS_TYPE_*
   */
  public function getClosedCaptionsType()
  {
    return $this->closedCaptionsType;
  }
  /**
   * This setting indicates whether auto start is enabled for this broadcast.
   * The default value for this property is false. This setting can only be used
   * by Events.
   *
   * @param bool $enableAutoStart
   */
  public function setEnableAutoStart($enableAutoStart)
  {
    $this->enableAutoStart = $enableAutoStart;
  }
  /**
   * @return bool
   */
  public function getEnableAutoStart()
  {
    return $this->enableAutoStart;
  }
  /**
   * This setting indicates whether auto stop is enabled for this broadcast. The
   * default value for this property is false. This setting can only be used by
   * Events.
   *
   * @param bool $enableAutoStop
   */
  public function setEnableAutoStop($enableAutoStop)
  {
    $this->enableAutoStop = $enableAutoStop;
  }
  /**
   * @return bool
   */
  public function getEnableAutoStop()
  {
    return $this->enableAutoStop;
  }
  /**
   * This setting indicates whether HTTP POST closed captioning is enabled for
   * this broadcast. The ingestion URL of the closed captions is returned
   * through the liveStreams API. This is mutually exclusive with using the
   * closed_captions_type property, and is equivalent to setting
   * closed_captions_type to CLOSED_CAPTIONS_HTTP_POST.
   *
   * @deprecated
   * @param bool $enableClosedCaptions
   */
  public function setEnableClosedCaptions($enableClosedCaptions)
  {
    $this->enableClosedCaptions = $enableClosedCaptions;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableClosedCaptions()
  {
    return $this->enableClosedCaptions;
  }
  /**
   * This setting indicates whether YouTube should enable content encryption for
   * the broadcast.
   *
   * @param bool $enableContentEncryption
   */
  public function setEnableContentEncryption($enableContentEncryption)
  {
    $this->enableContentEncryption = $enableContentEncryption;
  }
  /**
   * @return bool
   */
  public function getEnableContentEncryption()
  {
    return $this->enableContentEncryption;
  }
  /**
   * This setting determines whether viewers can access DVR controls while
   * watching the video. DVR controls enable the viewer to control the video
   * playback experience by pausing, rewinding, or fast forwarding content. The
   * default value for this property is true. *Important:* You must set the
   * value to true and also set the enableArchive property's value to true if
   * you want to make playback available immediately after the broadcast ends.
   *
   * @param bool $enableDvr
   */
  public function setEnableDvr($enableDvr)
  {
    $this->enableDvr = $enableDvr;
  }
  /**
   * @return bool
   */
  public function getEnableDvr()
  {
    return $this->enableDvr;
  }
  /**
   * This setting indicates whether the broadcast video can be played in an
   * embedded player. If you choose to archive the video (using the
   * enableArchive property), this setting will also apply to the archived
   * video.
   *
   * @param bool $enableEmbed
   */
  public function setEnableEmbed($enableEmbed)
  {
    $this->enableEmbed = $enableEmbed;
  }
  /**
   * @return bool
   */
  public function getEnableEmbed()
  {
    return $this->enableEmbed;
  }
  /**
   * Indicates whether this broadcast has low latency enabled.
   *
   * @deprecated
   * @param bool $enableLowLatency
   */
  public function setEnableLowLatency($enableLowLatency)
  {
    $this->enableLowLatency = $enableLowLatency;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableLowLatency()
  {
    return $this->enableLowLatency;
  }
  /**
   * If both this and enable_low_latency are set, they must match.
   * LATENCY_NORMAL should match enable_low_latency=false LATENCY_LOW should
   * match enable_low_latency=true LATENCY_ULTRA_LOW should have
   * enable_low_latency omitted.
   *
   * Accepted values: latencyPreferenceUnspecified, normal, low, ultraLow
   *
   * @param self::LATENCY_PREFERENCE_* $latencyPreference
   */
  public function setLatencyPreference($latencyPreference)
  {
    $this->latencyPreference = $latencyPreference;
  }
  /**
   * @return self::LATENCY_PREFERENCE_*
   */
  public function getLatencyPreference()
  {
    return $this->latencyPreference;
  }
  /**
   * The mesh for projecting the video if projection is mesh. The mesh value
   * must be a UTF-8 string containing the base-64 encoding of 3D mesh data that
   * follows the Spherical Video V2 RFC specification for an mshp box, excluding
   * the box size and type but including the following four reserved zero bytes
   * for the version and flags.
   *
   * @param string $mesh
   */
  public function setMesh($mesh)
  {
    $this->mesh = $mesh;
  }
  /**
   * @return string
   */
  public function getMesh()
  {
    return $this->mesh;
  }
  /**
   * The monitorStream object contains information about the monitor stream,
   * which the broadcaster can use to review the event content before the
   * broadcast stream is shown publicly.
   *
   * @param MonitorStreamInfo $monitorStream
   */
  public function setMonitorStream(MonitorStreamInfo $monitorStream)
  {
    $this->monitorStream = $monitorStream;
  }
  /**
   * @return MonitorStreamInfo
   */
  public function getMonitorStream()
  {
    return $this->monitorStream;
  }
  /**
   * The projection format of this broadcast. This defaults to rectangular.
   *
   * Accepted values: projectionUnspecified, rectangular, 360, mesh
   *
   * @param self::PROJECTION_* $projection
   */
  public function setProjection($projection)
  {
    $this->projection = $projection;
  }
  /**
   * @return self::PROJECTION_*
   */
  public function getProjection()
  {
    return $this->projection;
  }
  /**
   * Automatically start recording after the event goes live. The default value
   * for this property is true. *Important:* You must also set the enableDvr
   * property's value to true if you want the playback to be available
   * immediately after the broadcast ends. If you set this property's value to
   * true but do not also set the enableDvr property to true, there may be a
   * delay of around one day before the archived video will be available for
   * playback.
   *
   * @param bool $recordFromStart
   */
  public function setRecordFromStart($recordFromStart)
  {
    $this->recordFromStart = $recordFromStart;
  }
  /**
   * @return bool
   */
  public function getRecordFromStart()
  {
    return $this->recordFromStart;
  }
  /**
   * This setting indicates whether the broadcast should automatically begin
   * with an in-stream slate when you update the broadcast's status to live.
   * After updating the status, you then need to send a liveCuepoints.insert
   * request that sets the cuepoint's eventState to end to remove the in-stream
   * slate and make your broadcast stream visible to viewers.
   *
   * @deprecated
   * @param bool $startWithSlate
   */
  public function setStartWithSlate($startWithSlate)
  {
    $this->startWithSlate = $startWithSlate;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getStartWithSlate()
  {
    return $this->startWithSlate;
  }
  /**
   * The 3D stereo layout of this broadcast. This defaults to mono.
   *
   * Accepted values: stereoLayoutUnspecified, mono, leftRight, topBottom
   *
   * @param self::STEREO_LAYOUT_* $stereoLayout
   */
  public function setStereoLayout($stereoLayout)
  {
    $this->stereoLayout = $stereoLayout;
  }
  /**
   * @return self::STEREO_LAYOUT_*
   */
  public function getStereoLayout()
  {
    return $this->stereoLayout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveBroadcastContentDetails::class, 'Google_Service_YouTube_LiveBroadcastContentDetails');
