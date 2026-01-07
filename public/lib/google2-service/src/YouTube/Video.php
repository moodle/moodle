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

class Video extends \Google\Model
{
  protected $ageGatingType = VideoAgeGating::class;
  protected $ageGatingDataType = '';
  protected $contentDetailsType = VideoContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  protected $fileDetailsType = VideoFileDetails::class;
  protected $fileDetailsDataType = '';
  /**
   * The ID that YouTube uses to uniquely identify the video.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#video".
   *
   * @var string
   */
  public $kind;
  protected $liveStreamingDetailsType = VideoLiveStreamingDetails::class;
  protected $liveStreamingDetailsDataType = '';
  protected $localizationsType = VideoLocalization::class;
  protected $localizationsDataType = 'map';
  protected $monetizationDetailsType = VideoMonetizationDetails::class;
  protected $monetizationDetailsDataType = '';
  protected $paidProductPlacementDetailsType = VideoPaidProductPlacementDetails::class;
  protected $paidProductPlacementDetailsDataType = '';
  protected $playerType = VideoPlayer::class;
  protected $playerDataType = '';
  protected $processingDetailsType = VideoProcessingDetails::class;
  protected $processingDetailsDataType = '';
  protected $projectDetailsType = VideoProjectDetails::class;
  protected $projectDetailsDataType = '';
  protected $recordingDetailsType = VideoRecordingDetails::class;
  protected $recordingDetailsDataType = '';
  protected $snippetType = VideoSnippet::class;
  protected $snippetDataType = '';
  protected $statisticsType = VideoStatistics::class;
  protected $statisticsDataType = '';
  protected $statusType = VideoStatus::class;
  protected $statusDataType = '';
  protected $suggestionsType = VideoSuggestions::class;
  protected $suggestionsDataType = '';
  protected $topicDetailsType = VideoTopicDetails::class;
  protected $topicDetailsDataType = '';

  /**
   * Age restriction details related to a video. This data can only be retrieved
   * by the video owner.
   *
   * @param VideoAgeGating $ageGating
   */
  public function setAgeGating(VideoAgeGating $ageGating)
  {
    $this->ageGating = $ageGating;
  }
  /**
   * @return VideoAgeGating
   */
  public function getAgeGating()
  {
    return $this->ageGating;
  }
  /**
   * The contentDetails object contains information about the video content,
   * including the length of the video and its aspect ratio.
   *
   * @param VideoContentDetails $contentDetails
   */
  public function setContentDetails(VideoContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return VideoContentDetails
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
   * The fileDetails object encapsulates information about the video file that
   * was uploaded to YouTube, including the file's resolution, duration, audio
   * and video codecs, stream bitrates, and more. This data can only be
   * retrieved by the video owner.
   *
   * @param VideoFileDetails $fileDetails
   */
  public function setFileDetails(VideoFileDetails $fileDetails)
  {
    $this->fileDetails = $fileDetails;
  }
  /**
   * @return VideoFileDetails
   */
  public function getFileDetails()
  {
    return $this->fileDetails;
  }
  /**
   * The ID that YouTube uses to uniquely identify the video.
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
   * "youtube#video".
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
   * The liveStreamingDetails object contains metadata about a live video
   * broadcast. The object will only be present in a video resource if the video
   * is an upcoming, live, or completed live broadcast.
   *
   * @param VideoLiveStreamingDetails $liveStreamingDetails
   */
  public function setLiveStreamingDetails(VideoLiveStreamingDetails $liveStreamingDetails)
  {
    $this->liveStreamingDetails = $liveStreamingDetails;
  }
  /**
   * @return VideoLiveStreamingDetails
   */
  public function getLiveStreamingDetails()
  {
    return $this->liveStreamingDetails;
  }
  /**
   * The localizations object contains localized versions of the basic details
   * about the video, such as its title and description.
   *
   * @param VideoLocalization[] $localizations
   */
  public function setLocalizations($localizations)
  {
    $this->localizations = $localizations;
  }
  /**
   * @return VideoLocalization[]
   */
  public function getLocalizations()
  {
    return $this->localizations;
  }
  /**
   * The monetizationDetails object encapsulates information about the
   * monetization status of the video.
   *
   * @param VideoMonetizationDetails $monetizationDetails
   */
  public function setMonetizationDetails(VideoMonetizationDetails $monetizationDetails)
  {
    $this->monetizationDetails = $monetizationDetails;
  }
  /**
   * @return VideoMonetizationDetails
   */
  public function getMonetizationDetails()
  {
    return $this->monetizationDetails;
  }
  /**
   * @param VideoPaidProductPlacementDetails $paidProductPlacementDetails
   */
  public function setPaidProductPlacementDetails(VideoPaidProductPlacementDetails $paidProductPlacementDetails)
  {
    $this->paidProductPlacementDetails = $paidProductPlacementDetails;
  }
  /**
   * @return VideoPaidProductPlacementDetails
   */
  public function getPaidProductPlacementDetails()
  {
    return $this->paidProductPlacementDetails;
  }
  /**
   * The player object contains information that you would use to play the video
   * in an embedded player.
   *
   * @param VideoPlayer $player
   */
  public function setPlayer(VideoPlayer $player)
  {
    $this->player = $player;
  }
  /**
   * @return VideoPlayer
   */
  public function getPlayer()
  {
    return $this->player;
  }
  /**
   * The processingDetails object encapsulates information about YouTube's
   * progress in processing the uploaded video file. The properties in the
   * object identify the current processing status and an estimate of the time
   * remaining until YouTube finishes processing the video. This part also
   * indicates whether different types of data or content, such as file details
   * or thumbnail images, are available for the video. The processingProgress
   * object is designed to be polled so that the video uploaded can track the
   * progress that YouTube has made in processing the uploaded video file. This
   * data can only be retrieved by the video owner.
   *
   * @param VideoProcessingDetails $processingDetails
   */
  public function setProcessingDetails(VideoProcessingDetails $processingDetails)
  {
    $this->processingDetails = $processingDetails;
  }
  /**
   * @return VideoProcessingDetails
   */
  public function getProcessingDetails()
  {
    return $this->processingDetails;
  }
  /**
   * @deprecated
   * @param VideoProjectDetails $projectDetails
   */
  public function setProjectDetails(VideoProjectDetails $projectDetails)
  {
    $this->projectDetails = $projectDetails;
  }
  /**
   * @deprecated
   * @return VideoProjectDetails
   */
  public function getProjectDetails()
  {
    return $this->projectDetails;
  }
  /**
   * The recordingDetails object encapsulates information about the location,
   * date and address where the video was recorded.
   *
   * @param VideoRecordingDetails $recordingDetails
   */
  public function setRecordingDetails(VideoRecordingDetails $recordingDetails)
  {
    $this->recordingDetails = $recordingDetails;
  }
  /**
   * @return VideoRecordingDetails
   */
  public function getRecordingDetails()
  {
    return $this->recordingDetails;
  }
  /**
   * The snippet object contains basic details about the video, such as its
   * title, description, and category.
   *
   * @param VideoSnippet $snippet
   */
  public function setSnippet(VideoSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return VideoSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * The statistics object contains statistics about the video.
   *
   * @param VideoStatistics $statistics
   */
  public function setStatistics(VideoStatistics $statistics)
  {
    $this->statistics = $statistics;
  }
  /**
   * @return VideoStatistics
   */
  public function getStatistics()
  {
    return $this->statistics;
  }
  /**
   * The status object contains information about the video's uploading,
   * processing, and privacy statuses.
   *
   * @param VideoStatus $status
   */
  public function setStatus(VideoStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return VideoStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The suggestions object encapsulates suggestions that identify opportunities
   * to improve the video quality or the metadata for the uploaded video. This
   * data can only be retrieved by the video owner.
   *
   * @param VideoSuggestions $suggestions
   */
  public function setSuggestions(VideoSuggestions $suggestions)
  {
    $this->suggestions = $suggestions;
  }
  /**
   * @return VideoSuggestions
   */
  public function getSuggestions()
  {
    return $this->suggestions;
  }
  /**
   * The topicDetails object encapsulates information about Freebase topics
   * associated with the video.
   *
   * @param VideoTopicDetails $topicDetails
   */
  public function setTopicDetails(VideoTopicDetails $topicDetails)
  {
    $this->topicDetails = $topicDetails;
  }
  /**
   * @return VideoTopicDetails
   */
  public function getTopicDetails()
  {
    return $this->topicDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Video::class, 'Google_Service_YouTube_Video');
