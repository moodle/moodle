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

class LiveBroadcast extends \Google\Model
{
  protected $contentDetailsType = LiveBroadcastContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube assigns to uniquely identify the broadcast.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#liveBroadcast".
   *
   * @var string
   */
  public $kind;
  protected $monetizationDetailsType = LiveBroadcastMonetizationDetails::class;
  protected $monetizationDetailsDataType = '';
  protected $snippetType = LiveBroadcastSnippet::class;
  protected $snippetDataType = '';
  protected $statisticsType = LiveBroadcastStatistics::class;
  protected $statisticsDataType = '';
  protected $statusType = LiveBroadcastStatus::class;
  protected $statusDataType = '';

  /**
   * The contentDetails object contains information about the event's video
   * content, such as whether the content can be shown in an embedded video
   * player or if it will be archived and therefore available for viewing after
   * the event has concluded.
   *
   * @param LiveBroadcastContentDetails $contentDetails
   */
  public function setContentDetails(LiveBroadcastContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return LiveBroadcastContentDetails
   */
  public function getContentDetails()
  {
    return $this->contentDetails;
  }
  /**
   * Etag of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The ID that YouTube assigns to uniquely identify the broadcast.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#liveBroadcast".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The monetizationDetails object contains information about the event's
   * monetization details.
   *
   * @param LiveBroadcastMonetizationDetails $monetizationDetails
   */
  public function setMonetizationDetails(LiveBroadcastMonetizationDetails $monetizationDetails)
  {
    $this->monetizationDetails = $monetizationDetails;
  }
  /**
   * @return LiveBroadcastMonetizationDetails
   */
  public function getMonetizationDetails()
  {
    return $this->monetizationDetails;
  }
  /**
   * The snippet object contains basic details about the event, including its
   * title, description, start time, and end time.
   *
   * @param LiveBroadcastSnippet $snippet
   */
  public function setSnippet(LiveBroadcastSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return LiveBroadcastSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The statistics object contains info about the event's current stats. These
   * include concurrent viewers and total chat count. Statistics can change (in
   * either direction) during the lifetime of an event. Statistics are only
   * returned while the event is live.
   *
   * @param LiveBroadcastStatistics $statistics
   */
  public function setStatistics(LiveBroadcastStatistics $statistics)
  {
    $this->statistics = $statistics;
  }
  /**
   * @return LiveBroadcastStatistics
   */
  public function getStatistics()
  {
    return $this->statistics;
  }
  /**
   * The status object contains information about the event's status.
   *
   * @param LiveBroadcastStatus $status
   */
  public function setStatus(LiveBroadcastStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return LiveBroadcastStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveBroadcast::class, 'Google_Service_YouTube_LiveBroadcast');
