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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation extends \Google\Collection
{
  protected $collection_key = 'objectTrackingFrames';
  protected $annotationSpecType = GoogleCloudDatalabelingV1beta1AnnotationSpec::class;
  protected $annotationSpecDataType = '';
  protected $objectTrackingFramesType = GoogleCloudDatalabelingV1beta1ObjectTrackingFrame::class;
  protected $objectTrackingFramesDataType = 'array';
  protected $timeSegmentType = GoogleCloudDatalabelingV1beta1TimeSegment::class;
  protected $timeSegmentDataType = '';

  /**
   * Label of the object tracked in this annotation.
   *
   * @param GoogleCloudDatalabelingV1beta1AnnotationSpec $annotationSpec
   */
  public function setAnnotationSpec(GoogleCloudDatalabelingV1beta1AnnotationSpec $annotationSpec)
  {
    $this->annotationSpec = $annotationSpec;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1AnnotationSpec
   */
  public function getAnnotationSpec()
  {
    return $this->annotationSpec;
  }
  /**
   * The list of frames where this object track appears.
   *
   * @param GoogleCloudDatalabelingV1beta1ObjectTrackingFrame[] $objectTrackingFrames
   */
  public function setObjectTrackingFrames($objectTrackingFrames)
  {
    $this->objectTrackingFrames = $objectTrackingFrames;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1ObjectTrackingFrame[]
   */
  public function getObjectTrackingFrames()
  {
    return $this->objectTrackingFrames;
  }
  /**
   * The time segment of the video to which object tracking applies.
   *
   * @param GoogleCloudDatalabelingV1beta1TimeSegment $timeSegment
   */
  public function setTimeSegment(GoogleCloudDatalabelingV1beta1TimeSegment $timeSegment)
  {
    $this->timeSegment = $timeSegment;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TimeSegment
   */
  public function getTimeSegment()
  {
    return $this->timeSegment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1VideoObjectTrackingAnnotation');
