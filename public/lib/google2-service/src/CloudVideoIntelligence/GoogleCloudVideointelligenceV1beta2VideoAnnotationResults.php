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

class GoogleCloudVideointelligenceV1beta2VideoAnnotationResults extends \Google\Collection
{
  protected $collection_key = 'textAnnotations';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $explicitAnnotationType = GoogleCloudVideointelligenceV1beta2ExplicitContentAnnotation::class;
  protected $explicitAnnotationDataType = '';
  protected $faceAnnotationsType = GoogleCloudVideointelligenceV1beta2FaceAnnotation::class;
  protected $faceAnnotationsDataType = 'array';
  protected $faceDetectionAnnotationsType = GoogleCloudVideointelligenceV1beta2FaceDetectionAnnotation::class;
  protected $faceDetectionAnnotationsDataType = 'array';
  protected $frameLabelAnnotationsType = GoogleCloudVideointelligenceV1beta2LabelAnnotation::class;
  protected $frameLabelAnnotationsDataType = 'array';
  /**
   * Video file location in [Cloud Storage](https://cloud.google.com/storage/).
   *
   * @var string
   */
  public $inputUri;
  protected $logoRecognitionAnnotationsType = GoogleCloudVideointelligenceV1beta2LogoRecognitionAnnotation::class;
  protected $logoRecognitionAnnotationsDataType = 'array';
  protected $objectAnnotationsType = GoogleCloudVideointelligenceV1beta2ObjectTrackingAnnotation::class;
  protected $objectAnnotationsDataType = 'array';
  protected $personDetectionAnnotationsType = GoogleCloudVideointelligenceV1beta2PersonDetectionAnnotation::class;
  protected $personDetectionAnnotationsDataType = 'array';
  protected $segmentType = GoogleCloudVideointelligenceV1beta2VideoSegment::class;
  protected $segmentDataType = '';
  protected $segmentLabelAnnotationsType = GoogleCloudVideointelligenceV1beta2LabelAnnotation::class;
  protected $segmentLabelAnnotationsDataType = 'array';
  protected $segmentPresenceLabelAnnotationsType = GoogleCloudVideointelligenceV1beta2LabelAnnotation::class;
  protected $segmentPresenceLabelAnnotationsDataType = 'array';
  protected $shotAnnotationsType = GoogleCloudVideointelligenceV1beta2VideoSegment::class;
  protected $shotAnnotationsDataType = 'array';
  protected $shotLabelAnnotationsType = GoogleCloudVideointelligenceV1beta2LabelAnnotation::class;
  protected $shotLabelAnnotationsDataType = 'array';
  protected $shotPresenceLabelAnnotationsType = GoogleCloudVideointelligenceV1beta2LabelAnnotation::class;
  protected $shotPresenceLabelAnnotationsDataType = 'array';
  protected $speechTranscriptionsType = GoogleCloudVideointelligenceV1beta2SpeechTranscription::class;
  protected $speechTranscriptionsDataType = 'array';
  protected $textAnnotationsType = GoogleCloudVideointelligenceV1beta2TextAnnotation::class;
  protected $textAnnotationsDataType = 'array';

  /**
   * If set, indicates an error. Note that for a single `AnnotateVideoRequest`
   * some videos may succeed and some may fail.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Explicit content annotation.
   *
   * @param GoogleCloudVideointelligenceV1beta2ExplicitContentAnnotation $explicitAnnotation
   */
  public function setExplicitAnnotation(GoogleCloudVideointelligenceV1beta2ExplicitContentAnnotation $explicitAnnotation)
  {
    $this->explicitAnnotation = $explicitAnnotation;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2ExplicitContentAnnotation
   */
  public function getExplicitAnnotation()
  {
    return $this->explicitAnnotation;
  }
  /**
   * Deprecated. Please use `face_detection_annotations` instead.
   *
   * @deprecated
   * @param GoogleCloudVideointelligenceV1beta2FaceAnnotation[] $faceAnnotations
   */
  public function setFaceAnnotations($faceAnnotations)
  {
    $this->faceAnnotations = $faceAnnotations;
  }
  /**
   * @deprecated
   * @return GoogleCloudVideointelligenceV1beta2FaceAnnotation[]
   */
  public function getFaceAnnotations()
  {
    return $this->faceAnnotations;
  }
  /**
   * Face detection annotations.
   *
   * @param GoogleCloudVideointelligenceV1beta2FaceDetectionAnnotation[] $faceDetectionAnnotations
   */
  public function setFaceDetectionAnnotations($faceDetectionAnnotations)
  {
    $this->faceDetectionAnnotations = $faceDetectionAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2FaceDetectionAnnotation[]
   */
  public function getFaceDetectionAnnotations()
  {
    return $this->faceDetectionAnnotations;
  }
  /**
   * Label annotations on frame level. There is exactly one element for each
   * unique label.
   *
   * @param GoogleCloudVideointelligenceV1beta2LabelAnnotation[] $frameLabelAnnotations
   */
  public function setFrameLabelAnnotations($frameLabelAnnotations)
  {
    $this->frameLabelAnnotations = $frameLabelAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2LabelAnnotation[]
   */
  public function getFrameLabelAnnotations()
  {
    return $this->frameLabelAnnotations;
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
   * Annotations for list of logos detected, tracked and recognized in video.
   *
   * @param GoogleCloudVideointelligenceV1beta2LogoRecognitionAnnotation[] $logoRecognitionAnnotations
   */
  public function setLogoRecognitionAnnotations($logoRecognitionAnnotations)
  {
    $this->logoRecognitionAnnotations = $logoRecognitionAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2LogoRecognitionAnnotation[]
   */
  public function getLogoRecognitionAnnotations()
  {
    return $this->logoRecognitionAnnotations;
  }
  /**
   * Annotations for list of objects detected and tracked in video.
   *
   * @param GoogleCloudVideointelligenceV1beta2ObjectTrackingAnnotation[] $objectAnnotations
   */
  public function setObjectAnnotations($objectAnnotations)
  {
    $this->objectAnnotations = $objectAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2ObjectTrackingAnnotation[]
   */
  public function getObjectAnnotations()
  {
    return $this->objectAnnotations;
  }
  /**
   * Person detection annotations.
   *
   * @param GoogleCloudVideointelligenceV1beta2PersonDetectionAnnotation[] $personDetectionAnnotations
   */
  public function setPersonDetectionAnnotations($personDetectionAnnotations)
  {
    $this->personDetectionAnnotations = $personDetectionAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2PersonDetectionAnnotation[]
   */
  public function getPersonDetectionAnnotations()
  {
    return $this->personDetectionAnnotations;
  }
  /**
   * Video segment on which the annotation is run.
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
   * Topical label annotations on video level or user-specified segment level.
   * There is exactly one element for each unique label.
   *
   * @param GoogleCloudVideointelligenceV1beta2LabelAnnotation[] $segmentLabelAnnotations
   */
  public function setSegmentLabelAnnotations($segmentLabelAnnotations)
  {
    $this->segmentLabelAnnotations = $segmentLabelAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2LabelAnnotation[]
   */
  public function getSegmentLabelAnnotations()
  {
    return $this->segmentLabelAnnotations;
  }
  /**
   * Presence label annotations on video level or user-specified segment level.
   * There is exactly one element for each unique label. Compared to the
   * existing topical `segment_label_annotations`, this field presents more
   * fine-grained, segment-level labels detected in video content and is made
   * available only when the client sets `LabelDetectionConfig.model` to
   * "builtin/latest" in the request.
   *
   * @param GoogleCloudVideointelligenceV1beta2LabelAnnotation[] $segmentPresenceLabelAnnotations
   */
  public function setSegmentPresenceLabelAnnotations($segmentPresenceLabelAnnotations)
  {
    $this->segmentPresenceLabelAnnotations = $segmentPresenceLabelAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2LabelAnnotation[]
   */
  public function getSegmentPresenceLabelAnnotations()
  {
    return $this->segmentPresenceLabelAnnotations;
  }
  /**
   * Shot annotations. Each shot is represented as a video segment.
   *
   * @param GoogleCloudVideointelligenceV1beta2VideoSegment[] $shotAnnotations
   */
  public function setShotAnnotations($shotAnnotations)
  {
    $this->shotAnnotations = $shotAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2VideoSegment[]
   */
  public function getShotAnnotations()
  {
    return $this->shotAnnotations;
  }
  /**
   * Topical label annotations on shot level. There is exactly one element for
   * each unique label.
   *
   * @param GoogleCloudVideointelligenceV1beta2LabelAnnotation[] $shotLabelAnnotations
   */
  public function setShotLabelAnnotations($shotLabelAnnotations)
  {
    $this->shotLabelAnnotations = $shotLabelAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2LabelAnnotation[]
   */
  public function getShotLabelAnnotations()
  {
    return $this->shotLabelAnnotations;
  }
  /**
   * Presence label annotations on shot level. There is exactly one element for
   * each unique label. Compared to the existing topical
   * `shot_label_annotations`, this field presents more fine-grained, shot-level
   * labels detected in video content and is made available only when the client
   * sets `LabelDetectionConfig.model` to "builtin/latest" in the request.
   *
   * @param GoogleCloudVideointelligenceV1beta2LabelAnnotation[] $shotPresenceLabelAnnotations
   */
  public function setShotPresenceLabelAnnotations($shotPresenceLabelAnnotations)
  {
    $this->shotPresenceLabelAnnotations = $shotPresenceLabelAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2LabelAnnotation[]
   */
  public function getShotPresenceLabelAnnotations()
  {
    return $this->shotPresenceLabelAnnotations;
  }
  /**
   * Speech transcription.
   *
   * @param GoogleCloudVideointelligenceV1beta2SpeechTranscription[] $speechTranscriptions
   */
  public function setSpeechTranscriptions($speechTranscriptions)
  {
    $this->speechTranscriptions = $speechTranscriptions;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2SpeechTranscription[]
   */
  public function getSpeechTranscriptions()
  {
    return $this->speechTranscriptions;
  }
  /**
   * OCR text detection and tracking. Annotations for list of detected text
   * snippets. Each will have list of frame information associated with it.
   *
   * @param GoogleCloudVideointelligenceV1beta2TextAnnotation[] $textAnnotations
   */
  public function setTextAnnotations($textAnnotations)
  {
    $this->textAnnotations = $textAnnotations;
  }
  /**
   * @return GoogleCloudVideointelligenceV1beta2TextAnnotation[]
   */
  public function getTextAnnotations()
  {
    return $this->textAnnotations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1beta2VideoAnnotationResults::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1beta2VideoAnnotationResults');
