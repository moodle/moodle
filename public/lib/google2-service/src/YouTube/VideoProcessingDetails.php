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

class VideoProcessingDetails extends \Google\Model
{
  public const PROCESSING_FAILURE_REASON_uploadFailed = 'uploadFailed';
  public const PROCESSING_FAILURE_REASON_transcodeFailed = 'transcodeFailed';
  public const PROCESSING_FAILURE_REASON_streamingFailed = 'streamingFailed';
  public const PROCESSING_FAILURE_REASON_other = 'other';
  public const PROCESSING_STATUS_processing = 'processing';
  public const PROCESSING_STATUS_succeeded = 'succeeded';
  public const PROCESSING_STATUS_failed = 'failed';
  public const PROCESSING_STATUS_terminated = 'terminated';
  /**
   * This value indicates whether video editing suggestions, which might improve
   * video quality or the playback experience, are available for the video. You
   * can retrieve these suggestions by requesting the suggestions part in your
   * videos.list() request.
   *
   * @var string
   */
  public $editorSuggestionsAvailability;
  /**
   * This value indicates whether file details are available for the uploaded
   * video. You can retrieve a video's file details by requesting the
   * fileDetails part in your videos.list() request.
   *
   * @var string
   */
  public $fileDetailsAvailability;
  /**
   * The reason that YouTube failed to process the video. This property will
   * only have a value if the processingStatus property's value is failed.
   *
   * @var string
   */
  public $processingFailureReason;
  /**
   * This value indicates whether the video processing engine has generated
   * suggestions that might improve YouTube's ability to process the the video,
   * warnings that explain video processing problems, or errors that cause video
   * processing problems. You can retrieve these suggestions by requesting the
   * suggestions part in your videos.list() request.
   *
   * @var string
   */
  public $processingIssuesAvailability;
  protected $processingProgressType = VideoProcessingDetailsProcessingProgress::class;
  protected $processingProgressDataType = '';
  /**
   * The video's processing status. This value indicates whether YouTube was
   * able to process the video or if the video is still being processed.
   *
   * @var string
   */
  public $processingStatus;
  /**
   * This value indicates whether keyword (tag) suggestions are available for
   * the video. Tags can be added to a video's metadata to make it easier for
   * other users to find the video. You can retrieve these suggestions by
   * requesting the suggestions part in your videos.list() request.
   *
   * @var string
   */
  public $tagSuggestionsAvailability;
  /**
   * This value indicates whether thumbnail images have been generated for the
   * video.
   *
   * @var string
   */
  public $thumbnailsAvailability;

  /**
   * This value indicates whether video editing suggestions, which might improve
   * video quality or the playback experience, are available for the video. You
   * can retrieve these suggestions by requesting the suggestions part in your
   * videos.list() request.
   *
   * @param string $editorSuggestionsAvailability
   */
  public function setEditorSuggestionsAvailability($editorSuggestionsAvailability)
  {
    $this->editorSuggestionsAvailability = $editorSuggestionsAvailability;
  }
  /**
   * @return string
   */
  public function getEditorSuggestionsAvailability()
  {
    return $this->editorSuggestionsAvailability;
  }
  /**
   * This value indicates whether file details are available for the uploaded
   * video. You can retrieve a video's file details by requesting the
   * fileDetails part in your videos.list() request.
   *
   * @param string $fileDetailsAvailability
   */
  public function setFileDetailsAvailability($fileDetailsAvailability)
  {
    $this->fileDetailsAvailability = $fileDetailsAvailability;
  }
  /**
   * @return string
   */
  public function getFileDetailsAvailability()
  {
    return $this->fileDetailsAvailability;
  }
  /**
   * The reason that YouTube failed to process the video. This property will
   * only have a value if the processingStatus property's value is failed.
   *
   * Accepted values: uploadFailed, transcodeFailed, streamingFailed, other
   *
   * @param self::PROCESSING_FAILURE_REASON_* $processingFailureReason
   */
  public function setProcessingFailureReason($processingFailureReason)
  {
    $this->processingFailureReason = $processingFailureReason;
  }
  /**
   * @return self::PROCESSING_FAILURE_REASON_*
   */
  public function getProcessingFailureReason()
  {
    return $this->processingFailureReason;
  }
  /**
   * This value indicates whether the video processing engine has generated
   * suggestions that might improve YouTube's ability to process the the video,
   * warnings that explain video processing problems, or errors that cause video
   * processing problems. You can retrieve these suggestions by requesting the
   * suggestions part in your videos.list() request.
   *
   * @param string $processingIssuesAvailability
   */
  public function setProcessingIssuesAvailability($processingIssuesAvailability)
  {
    $this->processingIssuesAvailability = $processingIssuesAvailability;
  }
  /**
   * @return string
   */
  public function getProcessingIssuesAvailability()
  {
    return $this->processingIssuesAvailability;
  }
  /**
   * The processingProgress object contains information about the progress
   * YouTube has made in processing the video. The values are really only
   * relevant if the video's processing status is processing.
   *
   * @param VideoProcessingDetailsProcessingProgress $processingProgress
   */
  public function setProcessingProgress(VideoProcessingDetailsProcessingProgress $processingProgress)
  {
    $this->processingProgress = $processingProgress;
  }
  /**
   * @return VideoProcessingDetailsProcessingProgress
   */
  public function getProcessingProgress()
  {
    return $this->processingProgress;
  }
  /**
   * The video's processing status. This value indicates whether YouTube was
   * able to process the video or if the video is still being processed.
   *
   * Accepted values: processing, succeeded, failed, terminated
   *
   * @param self::PROCESSING_STATUS_* $processingStatus
   */
  public function setProcessingStatus($processingStatus)
  {
    $this->processingStatus = $processingStatus;
  }
  /**
   * @return self::PROCESSING_STATUS_*
   */
  public function getProcessingStatus()
  {
    return $this->processingStatus;
  }
  /**
   * This value indicates whether keyword (tag) suggestions are available for
   * the video. Tags can be added to a video's metadata to make it easier for
   * other users to find the video. You can retrieve these suggestions by
   * requesting the suggestions part in your videos.list() request.
   *
   * @param string $tagSuggestionsAvailability
   */
  public function setTagSuggestionsAvailability($tagSuggestionsAvailability)
  {
    $this->tagSuggestionsAvailability = $tagSuggestionsAvailability;
  }
  /**
   * @return string
   */
  public function getTagSuggestionsAvailability()
  {
    return $this->tagSuggestionsAvailability;
  }
  /**
   * This value indicates whether thumbnail images have been generated for the
   * video.
   *
   * @param string $thumbnailsAvailability
   */
  public function setThumbnailsAvailability($thumbnailsAvailability)
  {
    $this->thumbnailsAvailability = $thumbnailsAvailability;
  }
  /**
   * @return string
   */
  public function getThumbnailsAvailability()
  {
    return $this->thumbnailsAvailability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoProcessingDetails::class, 'Google_Service_YouTube_VideoProcessingDetails');
