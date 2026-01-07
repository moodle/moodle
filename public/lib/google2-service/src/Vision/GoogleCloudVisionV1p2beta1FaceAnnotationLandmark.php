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

class GoogleCloudVisionV1p2beta1FaceAnnotationLandmark extends \Google\Model
{
  /**
   * Unknown face landmark detected. Should not be filled.
   */
  public const TYPE_UNKNOWN_LANDMARK = 'UNKNOWN_LANDMARK';
  /**
   * Left eye.
   */
  public const TYPE_LEFT_EYE = 'LEFT_EYE';
  /**
   * Right eye.
   */
  public const TYPE_RIGHT_EYE = 'RIGHT_EYE';
  /**
   * Left of left eyebrow.
   */
  public const TYPE_LEFT_OF_LEFT_EYEBROW = 'LEFT_OF_LEFT_EYEBROW';
  /**
   * Right of left eyebrow.
   */
  public const TYPE_RIGHT_OF_LEFT_EYEBROW = 'RIGHT_OF_LEFT_EYEBROW';
  /**
   * Left of right eyebrow.
   */
  public const TYPE_LEFT_OF_RIGHT_EYEBROW = 'LEFT_OF_RIGHT_EYEBROW';
  /**
   * Right of right eyebrow.
   */
  public const TYPE_RIGHT_OF_RIGHT_EYEBROW = 'RIGHT_OF_RIGHT_EYEBROW';
  /**
   * Midpoint between eyes.
   */
  public const TYPE_MIDPOINT_BETWEEN_EYES = 'MIDPOINT_BETWEEN_EYES';
  /**
   * Nose tip.
   */
  public const TYPE_NOSE_TIP = 'NOSE_TIP';
  /**
   * Upper lip.
   */
  public const TYPE_UPPER_LIP = 'UPPER_LIP';
  /**
   * Lower lip.
   */
  public const TYPE_LOWER_LIP = 'LOWER_LIP';
  /**
   * Mouth left.
   */
  public const TYPE_MOUTH_LEFT = 'MOUTH_LEFT';
  /**
   * Mouth right.
   */
  public const TYPE_MOUTH_RIGHT = 'MOUTH_RIGHT';
  /**
   * Mouth center.
   */
  public const TYPE_MOUTH_CENTER = 'MOUTH_CENTER';
  /**
   * Nose, bottom right.
   */
  public const TYPE_NOSE_BOTTOM_RIGHT = 'NOSE_BOTTOM_RIGHT';
  /**
   * Nose, bottom left.
   */
  public const TYPE_NOSE_BOTTOM_LEFT = 'NOSE_BOTTOM_LEFT';
  /**
   * Nose, bottom center.
   */
  public const TYPE_NOSE_BOTTOM_CENTER = 'NOSE_BOTTOM_CENTER';
  /**
   * Left eye, top boundary.
   */
  public const TYPE_LEFT_EYE_TOP_BOUNDARY = 'LEFT_EYE_TOP_BOUNDARY';
  /**
   * Left eye, right corner.
   */
  public const TYPE_LEFT_EYE_RIGHT_CORNER = 'LEFT_EYE_RIGHT_CORNER';
  /**
   * Left eye, bottom boundary.
   */
  public const TYPE_LEFT_EYE_BOTTOM_BOUNDARY = 'LEFT_EYE_BOTTOM_BOUNDARY';
  /**
   * Left eye, left corner.
   */
  public const TYPE_LEFT_EYE_LEFT_CORNER = 'LEFT_EYE_LEFT_CORNER';
  /**
   * Right eye, top boundary.
   */
  public const TYPE_RIGHT_EYE_TOP_BOUNDARY = 'RIGHT_EYE_TOP_BOUNDARY';
  /**
   * Right eye, right corner.
   */
  public const TYPE_RIGHT_EYE_RIGHT_CORNER = 'RIGHT_EYE_RIGHT_CORNER';
  /**
   * Right eye, bottom boundary.
   */
  public const TYPE_RIGHT_EYE_BOTTOM_BOUNDARY = 'RIGHT_EYE_BOTTOM_BOUNDARY';
  /**
   * Right eye, left corner.
   */
  public const TYPE_RIGHT_EYE_LEFT_CORNER = 'RIGHT_EYE_LEFT_CORNER';
  /**
   * Left eyebrow, upper midpoint.
   */
  public const TYPE_LEFT_EYEBROW_UPPER_MIDPOINT = 'LEFT_EYEBROW_UPPER_MIDPOINT';
  /**
   * Right eyebrow, upper midpoint.
   */
  public const TYPE_RIGHT_EYEBROW_UPPER_MIDPOINT = 'RIGHT_EYEBROW_UPPER_MIDPOINT';
  /**
   * Left ear tragion.
   */
  public const TYPE_LEFT_EAR_TRAGION = 'LEFT_EAR_TRAGION';
  /**
   * Right ear tragion.
   */
  public const TYPE_RIGHT_EAR_TRAGION = 'RIGHT_EAR_TRAGION';
  /**
   * Left eye pupil.
   */
  public const TYPE_LEFT_EYE_PUPIL = 'LEFT_EYE_PUPIL';
  /**
   * Right eye pupil.
   */
  public const TYPE_RIGHT_EYE_PUPIL = 'RIGHT_EYE_PUPIL';
  /**
   * Forehead glabella.
   */
  public const TYPE_FOREHEAD_GLABELLA = 'FOREHEAD_GLABELLA';
  /**
   * Chin gnathion.
   */
  public const TYPE_CHIN_GNATHION = 'CHIN_GNATHION';
  /**
   * Chin left gonion.
   */
  public const TYPE_CHIN_LEFT_GONION = 'CHIN_LEFT_GONION';
  /**
   * Chin right gonion.
   */
  public const TYPE_CHIN_RIGHT_GONION = 'CHIN_RIGHT_GONION';
  /**
   * Left cheek center.
   */
  public const TYPE_LEFT_CHEEK_CENTER = 'LEFT_CHEEK_CENTER';
  /**
   * Right cheek center.
   */
  public const TYPE_RIGHT_CHEEK_CENTER = 'RIGHT_CHEEK_CENTER';
  protected $positionType = GoogleCloudVisionV1p2beta1Position::class;
  protected $positionDataType = '';
  /**
   * Face landmark type.
   *
   * @var string
   */
  public $type;

  /**
   * Face landmark position.
   *
   * @param GoogleCloudVisionV1p2beta1Position $position
   */
  public function setPosition(GoogleCloudVisionV1p2beta1Position $position)
  {
    $this->position = $position;
  }
  /**
   * @return GoogleCloudVisionV1p2beta1Position
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Face landmark type.
   *
   * Accepted values: UNKNOWN_LANDMARK, LEFT_EYE, RIGHT_EYE,
   * LEFT_OF_LEFT_EYEBROW, RIGHT_OF_LEFT_EYEBROW, LEFT_OF_RIGHT_EYEBROW,
   * RIGHT_OF_RIGHT_EYEBROW, MIDPOINT_BETWEEN_EYES, NOSE_TIP, UPPER_LIP,
   * LOWER_LIP, MOUTH_LEFT, MOUTH_RIGHT, MOUTH_CENTER, NOSE_BOTTOM_RIGHT,
   * NOSE_BOTTOM_LEFT, NOSE_BOTTOM_CENTER, LEFT_EYE_TOP_BOUNDARY,
   * LEFT_EYE_RIGHT_CORNER, LEFT_EYE_BOTTOM_BOUNDARY, LEFT_EYE_LEFT_CORNER,
   * RIGHT_EYE_TOP_BOUNDARY, RIGHT_EYE_RIGHT_CORNER, RIGHT_EYE_BOTTOM_BOUNDARY,
   * RIGHT_EYE_LEFT_CORNER, LEFT_EYEBROW_UPPER_MIDPOINT,
   * RIGHT_EYEBROW_UPPER_MIDPOINT, LEFT_EAR_TRAGION, RIGHT_EAR_TRAGION,
   * LEFT_EYE_PUPIL, RIGHT_EYE_PUPIL, FOREHEAD_GLABELLA, CHIN_GNATHION,
   * CHIN_LEFT_GONION, CHIN_RIGHT_GONION, LEFT_CHEEK_CENTER, RIGHT_CHEEK_CENTER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p2beta1FaceAnnotationLandmark::class, 'Google_Service_Vision_GoogleCloudVisionV1p2beta1FaceAnnotationLandmark');
