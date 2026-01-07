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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p1beta1FaceAnnotation extends \Google\Collection
{
  /**
   * Unknown likelihood.
   */
  public const ANGER_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const ANGER_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const ANGER_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const ANGER_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const ANGER_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const ANGER_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const BLURRED_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const BLURRED_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const BLURRED_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const BLURRED_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const BLURRED_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const BLURRED_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const HEADWEAR_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const HEADWEAR_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const HEADWEAR_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const HEADWEAR_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const HEADWEAR_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const HEADWEAR_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const JOY_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const JOY_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const JOY_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const JOY_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const JOY_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const JOY_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const SORROW_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const SORROW_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const SORROW_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const SORROW_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const SORROW_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const SORROW_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const SURPRISE_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const SURPRISE_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const SURPRISE_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const SURPRISE_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const SURPRISE_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const SURPRISE_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Unknown likelihood.
   */
  public const UNDER_EXPOSED_LIKELIHOOD_UNKNOWN = 'UNKNOWN';
  /**
   * It is very unlikely.
   */
  public const UNDER_EXPOSED_LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * It is unlikely.
   */
  public const UNDER_EXPOSED_LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * It is possible.
   */
  public const UNDER_EXPOSED_LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * It is likely.
   */
  public const UNDER_EXPOSED_LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * It is very likely.
   */
  public const UNDER_EXPOSED_LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  protected $collection_key = 'landmarks';
  /**
   * Anger likelihood.
   *
   * @var string
   */
  public $angerLikelihood;
  /**
   * Blurred likelihood.
   *
   * @var string
   */
  public $blurredLikelihood;
  protected $boundingPolyType = GoogleCloudVisionV1p1beta1BoundingPoly::class;
  protected $boundingPolyDataType = '';
  /**
   * Detection confidence. Range [0, 1].
   *
   * @var float
   */
  public $detectionConfidence;
  protected $fdBoundingPolyType = GoogleCloudVisionV1p1beta1BoundingPoly::class;
  protected $fdBoundingPolyDataType = '';
  /**
   * Headwear likelihood.
   *
   * @var string
   */
  public $headwearLikelihood;
  /**
   * Joy likelihood.
   *
   * @var string
   */
  public $joyLikelihood;
  /**
   * Face landmarking confidence. Range [0, 1].
   *
   * @var float
   */
  public $landmarkingConfidence;
  protected $landmarksType = GoogleCloudVisionV1p1beta1FaceAnnotationLandmark::class;
  protected $landmarksDataType = 'array';
  /**
   * Yaw angle, which indicates the leftward/rightward angle that the face is
   * pointing relative to the vertical plane perpendicular to the image. Range
   * [-180,180].
   *
   * @var float
   */
  public $panAngle;
  /**
   * Roll angle, which indicates the amount of clockwise/anti-clockwise rotation
   * of the face relative to the image vertical about the axis perpendicular to
   * the face. Range [-180,180].
   *
   * @var float
   */
  public $rollAngle;
  /**
   * Sorrow likelihood.
   *
   * @var string
   */
  public $sorrowLikelihood;
  /**
   * Surprise likelihood.
   *
   * @var string
   */
  public $surpriseLikelihood;
  /**
   * Pitch angle, which indicates the upwards/downwards angle that the face is
   * pointing relative to the image's horizontal plane. Range [-180,180].
   *
   * @var float
   */
  public $tiltAngle;
  /**
   * Under-exposed likelihood.
   *
   * @var string
   */
  public $underExposedLikelihood;

  /**
   * Anger likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::ANGER_LIKELIHOOD_* $angerLikelihood
   */
  public function setAngerLikelihood($angerLikelihood)
  {
    $this->angerLikelihood = $angerLikelihood;
  }
  /**
   * @return self::ANGER_LIKELIHOOD_*
   */
  public function getAngerLikelihood()
  {
    return $this->angerLikelihood;
  }
  /**
   * Blurred likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::BLURRED_LIKELIHOOD_* $blurredLikelihood
   */
  public function setBlurredLikelihood($blurredLikelihood)
  {
    $this->blurredLikelihood = $blurredLikelihood;
  }
  /**
   * @return self::BLURRED_LIKELIHOOD_*
   */
  public function getBlurredLikelihood()
  {
    return $this->blurredLikelihood;
  }
  /**
   * The bounding polygon around the face. The coordinates of the bounding box
   * are in the original image's scale. The bounding box is computed to "frame"
   * the face in accordance with human expectations. It is based on the
   * landmarker results. Note that one or more x and/or y coordinates may not be
   * generated in the `BoundingPoly` (the polygon will be unbounded) if only a
   * partial face appears in the image to be annotated.
   *
   * @param GoogleCloudVisionV1p1beta1BoundingPoly $boundingPoly
   */
  public function setBoundingPoly(GoogleCloudVisionV1p1beta1BoundingPoly $boundingPoly)
  {
    $this->boundingPoly = $boundingPoly;
  }
  /**
   * @return GoogleCloudVisionV1p1beta1BoundingPoly
   */
  public function getBoundingPoly()
  {
    return $this->boundingPoly;
  }
  /**
   * Detection confidence. Range [0, 1].
   *
   * @param float $detectionConfidence
   */
  public function setDetectionConfidence($detectionConfidence)
  {
    $this->detectionConfidence = $detectionConfidence;
  }
  /**
   * @return float
   */
  public function getDetectionConfidence()
  {
    return $this->detectionConfidence;
  }
  /**
   * The `fd_bounding_poly` bounding polygon is tighter than the `boundingPoly`,
   * and encloses only the skin part of the face. Typically, it is used to
   * eliminate the face from any image analysis that detects the "amount of
   * skin" visible in an image. It is not based on the landmarker results, only
   * on the initial face detection, hence the fd (face detection) prefix.
   *
   * @param GoogleCloudVisionV1p1beta1BoundingPoly $fdBoundingPoly
   */
  public function setFdBoundingPoly(GoogleCloudVisionV1p1beta1BoundingPoly $fdBoundingPoly)
  {
    $this->fdBoundingPoly = $fdBoundingPoly;
  }
  /**
   * @return GoogleCloudVisionV1p1beta1BoundingPoly
   */
  public function getFdBoundingPoly()
  {
    return $this->fdBoundingPoly;
  }
  /**
   * Headwear likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::HEADWEAR_LIKELIHOOD_* $headwearLikelihood
   */
  public function setHeadwearLikelihood($headwearLikelihood)
  {
    $this->headwearLikelihood = $headwearLikelihood;
  }
  /**
   * @return self::HEADWEAR_LIKELIHOOD_*
   */
  public function getHeadwearLikelihood()
  {
    return $this->headwearLikelihood;
  }
  /**
   * Joy likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::JOY_LIKELIHOOD_* $joyLikelihood
   */
  public function setJoyLikelihood($joyLikelihood)
  {
    $this->joyLikelihood = $joyLikelihood;
  }
  /**
   * @return self::JOY_LIKELIHOOD_*
   */
  public function getJoyLikelihood()
  {
    return $this->joyLikelihood;
  }
  /**
   * Face landmarking confidence. Range [0, 1].
   *
   * @param float $landmarkingConfidence
   */
  public function setLandmarkingConfidence($landmarkingConfidence)
  {
    $this->landmarkingConfidence = $landmarkingConfidence;
  }
  /**
   * @return float
   */
  public function getLandmarkingConfidence()
  {
    return $this->landmarkingConfidence;
  }
  /**
   * Detected face landmarks.
   *
   * @param GoogleCloudVisionV1p1beta1FaceAnnotationLandmark[] $landmarks
   */
  public function setLandmarks($landmarks)
  {
    $this->landmarks = $landmarks;
  }
  /**
   * @return GoogleCloudVisionV1p1beta1FaceAnnotationLandmark[]
   */
  public function getLandmarks()
  {
    return $this->landmarks;
  }
  /**
   * Yaw angle, which indicates the leftward/rightward angle that the face is
   * pointing relative to the vertical plane perpendicular to the image. Range
   * [-180,180].
   *
   * @param float $panAngle
   */
  public function setPanAngle($panAngle)
  {
    $this->panAngle = $panAngle;
  }
  /**
   * @return float
   */
  public function getPanAngle()
  {
    return $this->panAngle;
  }
  /**
   * Roll angle, which indicates the amount of clockwise/anti-clockwise rotation
   * of the face relative to the image vertical about the axis perpendicular to
   * the face. Range [-180,180].
   *
   * @param float $rollAngle
   */
  public function setRollAngle($rollAngle)
  {
    $this->rollAngle = $rollAngle;
  }
  /**
   * @return float
   */
  public function getRollAngle()
  {
    return $this->rollAngle;
  }
  /**
   * Sorrow likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::SORROW_LIKELIHOOD_* $sorrowLikelihood
   */
  public function setSorrowLikelihood($sorrowLikelihood)
  {
    $this->sorrowLikelihood = $sorrowLikelihood;
  }
  /**
   * @return self::SORROW_LIKELIHOOD_*
   */
  public function getSorrowLikelihood()
  {
    return $this->sorrowLikelihood;
  }
  /**
   * Surprise likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::SURPRISE_LIKELIHOOD_* $surpriseLikelihood
   */
  public function setSurpriseLikelihood($surpriseLikelihood)
  {
    $this->surpriseLikelihood = $surpriseLikelihood;
  }
  /**
   * @return self::SURPRISE_LIKELIHOOD_*
   */
  public function getSurpriseLikelihood()
  {
    return $this->surpriseLikelihood;
  }
  /**
   * Pitch angle, which indicates the upwards/downwards angle that the face is
   * pointing relative to the image's horizontal plane. Range [-180,180].
   *
   * @param float $tiltAngle
   */
  public function setTiltAngle($tiltAngle)
  {
    $this->tiltAngle = $tiltAngle;
  }
  /**
   * @return float
   */
  public function getTiltAngle()
  {
    return $this->tiltAngle;
  }
  /**
   * Under-exposed likelihood.
   *
   * Accepted values: UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY,
   * VERY_LIKELY
   *
   * @param self::UNDER_EXPOSED_LIKELIHOOD_* $underExposedLikelihood
   */
  public function setUnderExposedLikelihood($underExposedLikelihood)
  {
    $this->underExposedLikelihood = $underExposedLikelihood;
  }
  /**
   * @return self::UNDER_EXPOSED_LIKELIHOOD_*
   */
  public function getUnderExposedLikelihood()
  {
    return $this->underExposedLikelihood;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p1beta1FaceAnnotation::class, 'Google_Service_Vision_GoogleCloudVisionV1p1beta1FaceAnnotation');
