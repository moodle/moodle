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

class GoogleCloudVideointelligenceV1p2beta1FaceAnnotation extends \Google\Collection
{
  protected $collection_key = 'segments';
  protected $framesType = GoogleCloudVideointelligenceV1p2beta1FaceFrame::class;
  protected $framesDataType = 'array';
  protected $segmentsType = GoogleCloudVideointelligenceV1p2beta1FaceSegment::class;
  protected $segmentsDataType = 'array';
  /**
   * Thumbnail of a representative face view (in JPEG format).
   *
   * @var string
   */
  public $thumbnail;

  /**
   * All video frames where a face was detected.
   *
   * @param GoogleCloudVideointelligenceV1p2beta1FaceFrame[] $frames
   */
  public function setFrames($frames)
  {
    $this->frames = $frames;
  }
  /**
   * @return GoogleCloudVideointelligenceV1p2beta1FaceFrame[]
   */
  public function getFrames()
  {
    return $this->frames;
  }
  /**
   * All video segments where a face was detected.
   *
   * @param GoogleCloudVideointelligenceV1p2beta1FaceSegment[] $segments
   */
  public function setSegments($segments)
  {
    $this->segments = $segments;
  }
  /**
   * @return GoogleCloudVideointelligenceV1p2beta1FaceSegment[]
   */
  public function getSegments()
  {
    return $this->segments;
  }
  /**
   * Thumbnail of a representative face view (in JPEG format).
   *
   * @param string $thumbnail
   */
  public function setThumbnail($thumbnail)
  {
    $this->thumbnail = $thumbnail;
  }
  /**
   * @return string
   */
  public function getThumbnail()
  {
    return $this->thumbnail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1p2beta1FaceAnnotation::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1p2beta1FaceAnnotation');
