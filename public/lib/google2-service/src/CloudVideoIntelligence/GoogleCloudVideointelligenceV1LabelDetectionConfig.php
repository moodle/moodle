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

class GoogleCloudVideointelligenceV1LabelDetectionConfig extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const LABEL_DETECTION_MODE_LABEL_DETECTION_MODE_UNSPECIFIED = 'LABEL_DETECTION_MODE_UNSPECIFIED';
  /**
   * Detect shot-level labels.
   */
  public const LABEL_DETECTION_MODE_SHOT_MODE = 'SHOT_MODE';
  /**
   * Detect frame-level labels.
   */
  public const LABEL_DETECTION_MODE_FRAME_MODE = 'FRAME_MODE';
  /**
   * Detect both shot-level and frame-level labels.
   */
  public const LABEL_DETECTION_MODE_SHOT_AND_FRAME_MODE = 'SHOT_AND_FRAME_MODE';
  /**
   * The confidence threshold we perform filtering on the labels from frame-
   * level detection. If not set, it is set to 0.4 by default. The valid range
   * for this threshold is [0.1, 0.9]. Any value set outside of this range will
   * be clipped. Note: For best results, follow the default threshold. We will
   * update the default threshold everytime when we release a new model.
   *
   * @var float
   */
  public $frameConfidenceThreshold;
  /**
   * What labels should be detected with LABEL_DETECTION, in addition to video-
   * level labels or segment-level labels. If unspecified, defaults to
   * `SHOT_MODE`.
   *
   * @var string
   */
  public $labelDetectionMode;
  /**
   * Model to use for label detection. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest".
   *
   * @var string
   */
  public $model;
  /**
   * Whether the video has been shot from a stationary (i.e., non-moving)
   * camera. When set to true, might improve detection accuracy for moving
   * objects. Should be used with `SHOT_AND_FRAME_MODE` enabled.
   *
   * @var bool
   */
  public $stationaryCamera;
  /**
   * The confidence threshold we perform filtering on the labels from video-
   * level and shot-level detections. If not set, it's set to 0.3 by default.
   * The valid range for this threshold is [0.1, 0.9]. Any value set outside of
   * this range will be clipped. Note: For best results, follow the default
   * threshold. We will update the default threshold everytime when we release a
   * new model.
   *
   * @var float
   */
  public $videoConfidenceThreshold;

  /**
   * The confidence threshold we perform filtering on the labels from frame-
   * level detection. If not set, it is set to 0.4 by default. The valid range
   * for this threshold is [0.1, 0.9]. Any value set outside of this range will
   * be clipped. Note: For best results, follow the default threshold. We will
   * update the default threshold everytime when we release a new model.
   *
   * @param float $frameConfidenceThreshold
   */
  public function setFrameConfidenceThreshold($frameConfidenceThreshold)
  {
    $this->frameConfidenceThreshold = $frameConfidenceThreshold;
  }
  /**
   * @return float
   */
  public function getFrameConfidenceThreshold()
  {
    return $this->frameConfidenceThreshold;
  }
  /**
   * What labels should be detected with LABEL_DETECTION, in addition to video-
   * level labels or segment-level labels. If unspecified, defaults to
   * `SHOT_MODE`.
   *
   * Accepted values: LABEL_DETECTION_MODE_UNSPECIFIED, SHOT_MODE, FRAME_MODE,
   * SHOT_AND_FRAME_MODE
   *
   * @param self::LABEL_DETECTION_MODE_* $labelDetectionMode
   */
  public function setLabelDetectionMode($labelDetectionMode)
  {
    $this->labelDetectionMode = $labelDetectionMode;
  }
  /**
   * @return self::LABEL_DETECTION_MODE_*
   */
  public function getLabelDetectionMode()
  {
    return $this->labelDetectionMode;
  }
  /**
   * Model to use for label detection. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest".
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Whether the video has been shot from a stationary (i.e., non-moving)
   * camera. When set to true, might improve detection accuracy for moving
   * objects. Should be used with `SHOT_AND_FRAME_MODE` enabled.
   *
   * @param bool $stationaryCamera
   */
  public function setStationaryCamera($stationaryCamera)
  {
    $this->stationaryCamera = $stationaryCamera;
  }
  /**
   * @return bool
   */
  public function getStationaryCamera()
  {
    return $this->stationaryCamera;
  }
  /**
   * The confidence threshold we perform filtering on the labels from video-
   * level and shot-level detections. If not set, it's set to 0.3 by default.
   * The valid range for this threshold is [0.1, 0.9]. Any value set outside of
   * this range will be clipped. Note: For best results, follow the default
   * threshold. We will update the default threshold everytime when we release a
   * new model.
   *
   * @param float $videoConfidenceThreshold
   */
  public function setVideoConfidenceThreshold($videoConfidenceThreshold)
  {
    $this->videoConfidenceThreshold = $videoConfidenceThreshold;
  }
  /**
   * @return float
   */
  public function getVideoConfidenceThreshold()
  {
    return $this->videoConfidenceThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1LabelDetectionConfig::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1LabelDetectionConfig');
