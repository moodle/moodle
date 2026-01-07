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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1beta2VideoAnnotationProgress extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const FEATURE_FEATURE_UNSPECIFIED = 'FEATURE_UNSPECIFIED';
  /**
   * Label detection. Detect objects, such as dog or flower.
   */
  public const FEATURE_LABEL_DETECTION = 'LABEL_DETECTION';
  /**
   * Shot change detection.
   */
  public const FEATURE_SHOT_CHANGE_DETECTION = 'SHOT_CHANGE_DETECTION';
  /**
   * Explicit content detection.
   */
  public const FEATURE_EXPLICIT_CONTENT_DETECTION = 'EXPLICIT_CONTENT_DETECTION';
  /**
   * Human face detection.
   */
  public const FEATURE_FACE_DETECTION = 'FACE_DETECTION';
  /**
   * Speech transcription.
   */
  public const FEATURE_SPEECH_TRANSCRIPTION = 'SPEECH_TRANSCRIPTION';
  /**
   * OCR text detection and tracking.
   */
  public const FEATURE_TEXT_DETECTION = 'TEXT_DETECTION';
  /**
   * Object detection and tracking.
   */
  public const FEATURE_OBJECT_TRACKING = 'OBJECT_TRACKING';
  /**
   * Logo detection, tracking, and recognition.
   */
  public const FEATURE_LOGO_RECOGNITION = 'LOGO_RECOGNITION';
  /**
   * Person detection.
   */
  public const FEATURE_PERSON_DETECTION = 'PERSON_DETECTION';
  protected $exportStatusType = GoogleCloudVideointelligenceV1beta2ExportToOutputUriStatus::class;
  protected $exportStatusDataType = '';
  /**
   * Specifies which feature is being tracked if the request contains more than
   * one feature.
   *
   * @var string
   */
  public $feature;
  /**
   * Video file location in [Cloud Storage](https://cloud.google.com/storage/).
   *
   * @var string
   */
  public $inputUri;
  /**
   * Approximate percentage processed thus far. Guaranteed to be 100 when fully
   * processed.
   *
   * @var int
   */
  public $progressPercent;
  protected $segmentType = GoogleCloudVideointelligenceV1beta2VideoSegment::class;
  protected $segmentDataType = '';
  /**
   * Time when the request was received.
   *
   * @var string
   */
  public $startTime;
  /**
   * Time of the most recent update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Status of exporting annotation response to user specified `output_uri`.
   * Only set if `output_uri` is set in the request.
   *
   * @param GoogleCloudVideointelligenceV1beta2ExportToOutputUriStatus $exportStatus
   */
  public function setExportStatus(GoogleCloudVideointelligenceV1beta2ExportToOutputUriStatus $exportStatus)
  {
    $this->exportStatus = $exportStatus;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2ExportToOutputUriStatus
   */
  public function getExportStatus()
  {
    return $this->exportStatus;
  }
  /**
   * Specifies which feature is being tracked if the request contains more than
   * one feature.
   *
   * Accepted values: FEATURE_UNSPECIFIED, LABEL_DETECTION,
   * SHOT_CHANGE_DETECTION, EXPLICIT_CONTENT_DETECTION, FACE_DETECTION,
   * SPEECH_TRANSCRIPTION, TEXT_DETECTION, OBJECT_TRACKING, LOGO_RECOGNITION,
   * PERSON_DETECTION
   *
   * @param self::FEATURE_* $feature
   */
  public function setFeature($feature)
  {
    $this->feature = $feature;
  }
  /**
   * @return self::FEATURE_*
   */
  public function getFeature()
  {
    return $this->feature;
  }
  /**
   * Video file location in [Cloud Storage](https://cloud.google.com/storage/).
   *
   * @param string $inputUri
   */
  public function setInputUri($inputUri)
  {
    $this->inputUri = $inputUri;
  }
  /**
   * @return string
   */
  public function getInputUri()
  {
    return $this->inputUri;
  }
  /**
   * Approximate percentage processed thus far. Guaranteed to be 100 when fully
   * processed.
   *
   * @param int $progressPercent
   */
  public function setProgressPercent($progressPercent)
  {
    $this->progressPercent = $progressPercent;
  }
  /**
   * @return int
   */
  public function getProgressPercent()
  {
    return $this->progressPercent;
  }
  /**
   * Specifies which segment is being tracked if the request contains more than
   * one segment.
   *
   * @param GoogleCloudVideointelligenceV1beta2VideoSegment $segment
   */
  public function setSegment(GoogleCloudVideointelligenceV1beta2VideoSegment $segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2VideoSegment
   */
  public function getSegment()
  {
    return $this->segment;
  }
  /**
   * Time when the request was received.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Time of the most recent update.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1beta2VideoAnnotationProgress::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1beta2VideoAnnotationProgress');
