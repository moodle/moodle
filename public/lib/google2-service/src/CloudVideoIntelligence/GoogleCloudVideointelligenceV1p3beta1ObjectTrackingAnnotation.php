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

class GoogleCloudVideointelligenceV1p3beta1ObjectTrackingAnnotation extends \Google\Collection
{
  protected $collection_key = 'frames';
  /**
   * Object category's labeling confidence of this track.
   *
   * @var float
   */
  public $confidence;
  protected $entityType = GoogleCloudVideointelligenceV1p3beta1Entity::class;
  protected $entityDataType = '';
  protected $framesType = GoogleCloudVideointelligenceV1p3beta1ObjectTrackingFrame::class;
  protected $framesDataType = 'array';
  protected $segmentType = GoogleCloudVideointelligenceV1p3beta1VideoSegment::class;
  protected $segmentDataType = '';
  /**
   * Streaming mode ONLY. In streaming mode, we do not know the end time of a
   * tracked object before it is completed. Hence, there is no VideoSegment info
   * returned. Instead, we provide a unique identifiable integer track_id so
   * that the customers can correlate the results of the ongoing
   * ObjectTrackAnnotation of the same track_id over time.
   *
   * @var string
   */
  public $trackId;
  /**
   * Feature version.
   *
   * @var string
   */
  public $version;

  /**
   * Object category's labeling confidence of this track.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Entity to specify the object category that this track is labeled as.
   *
   * @param GoogleCloudVideointelligenceV1p3beta1Entity $entity
   */
  public function setEntity(GoogleCloudVideointelligenceV1p3beta1Entity $entity)
  {
    $this->entity = $entity;
  }
  /**
   * @return GoogleCloudVideointelligenceV1p3beta1Entity
   */
  public function getEntity()
  {
    return $this->entity;
  }
  /**
   * Information corresponding to all frames where this object track appears.
   * Non-streaming batch mode: it may be one or multiple ObjectTrackingFrame
   * messages in frames. Streaming mode: it can only be one ObjectTrackingFrame
   * message in frames.
   *
   * @param GoogleCloudVideointelligenceV1p3beta1ObjectTrackingFrame[] $frames
   */
  public function setFrames($frames)
  {
    $this->frames = $frames;
  }
  /**
   * @return GoogleCloudVideointelligenceV1p3beta1ObjectTrackingFrame[]
   */
  public function getFrames()
  {
    return $this->frames;
  }
  /**
   * Non-streaming batch mode ONLY. Each object track corresponds to one video
   * segment where it appears.
   *
   * @param GoogleCloudVideointelligenceV1p3beta1VideoSegment $segment
   */
  public function setSegment(GoogleCloudVideointelligenceV1p3beta1VideoSegment $segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return GoogleCloudVideointelligenceV1p3beta1VideoSegment
   */
  public function getSegment()
  {
    return $this->segment;
  }
  /**
   * Streaming mode ONLY. In streaming mode, we do not know the end time of a
   * tracked object before it is completed. Hence, there is no VideoSegment info
   * returned. Instead, we provide a unique identifiable integer track_id so
   * that the customers can correlate the results of the ongoing
   * ObjectTrackAnnotation of the same track_id over time.
   *
   * @param string $trackId
   */
  public function setTrackId($trackId)
  {
    $this->trackId = $trackId;
  }
  /**
   * @return string
   */
  public function getTrackId()
  {
    return $this->trackId;
  }
  /**
   * Feature version.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1p3beta1ObjectTrackingAnnotation::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1p3beta1ObjectTrackingAnnotation');
