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

namespace Google\Service\Solar;

class RoofSegmentSizeAndSunshineStats extends \Google\Model
{
  /**
   * Compass direction the roof segment is pointing in. 0 = North, 90 = East,
   * 180 = South. For a "flat" roof segment (`pitch_degrees` very near 0),
   * azimuth is not well defined, so for consistency, we define it arbitrarily
   * to be 0 (North).
   *
   * @var float
   */
  public $azimuthDegrees;
  protected $boundingBoxType = LatLngBox::class;
  protected $boundingBoxDataType = '';
  protected $centerType = LatLng::class;
  protected $centerDataType = '';
  /**
   * Angle of the roof segment relative to the theoretical ground plane. 0 =
   * parallel to the ground, 90 = perpendicular to the ground.
   *
   * @var float
   */
  public $pitchDegrees;
  /**
   * The height of the roof segment plane, in meters above sea level, at the
   * point designated by `center`. Together with the pitch, azimuth, and center
   * location, this fully defines the roof segment plane.
   *
   * @var float
   */
  public $planeHeightAtCenterMeters;
  protected $statsType = SizeAndSunshineStats::class;
  protected $statsDataType = '';

  /**
   * Compass direction the roof segment is pointing in. 0 = North, 90 = East,
   * 180 = South. For a "flat" roof segment (`pitch_degrees` very near 0),
   * azimuth is not well defined, so for consistency, we define it arbitrarily
   * to be 0 (North).
   *
   * @param float $azimuthDegrees
   */
  public function setAzimuthDegrees($azimuthDegrees)
  {
    $this->azimuthDegrees = $azimuthDegrees;
  }
  /**
   * @return float
   */
  public function getAzimuthDegrees()
  {
    return $this->azimuthDegrees;
  }
  /**
   * The bounding box of the roof segment.
   *
   * @param LatLngBox $boundingBox
   */
  public function setBoundingBox(LatLngBox $boundingBox)
  {
    $this->boundingBox = $boundingBox;
  }
  /**
   * @return LatLngBox
   */
  public function getBoundingBox()
  {
    return $this->boundingBox;
  }
  /**
   * A point near the center of the roof segment.
   *
   * @param LatLng $center
   */
  public function setCenter(LatLng $center)
  {
    $this->center = $center;
  }
  /**
   * @return LatLng
   */
  public function getCenter()
  {
    return $this->center;
  }
  /**
   * Angle of the roof segment relative to the theoretical ground plane. 0 =
   * parallel to the ground, 90 = perpendicular to the ground.
   *
   * @param float $pitchDegrees
   */
  public function setPitchDegrees($pitchDegrees)
  {
    $this->pitchDegrees = $pitchDegrees;
  }
  /**
   * @return float
   */
  public function getPitchDegrees()
  {
    return $this->pitchDegrees;
  }
  /**
   * The height of the roof segment plane, in meters above sea level, at the
   * point designated by `center`. Together with the pitch, azimuth, and center
   * location, this fully defines the roof segment plane.
   *
   * @param float $planeHeightAtCenterMeters
   */
  public function setPlaneHeightAtCenterMeters($planeHeightAtCenterMeters)
  {
    $this->planeHeightAtCenterMeters = $planeHeightAtCenterMeters;
  }
  /**
   * @return float
   */
  public function getPlaneHeightAtCenterMeters()
  {
    return $this->planeHeightAtCenterMeters;
  }
  /**
   * Total size and sunlight quantiles for the roof segment.
   *
   * @param SizeAndSunshineStats $stats
   */
  public function setStats(SizeAndSunshineStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return SizeAndSunshineStats
   */
  public function getStats()
  {
    return $this->stats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoofSegmentSizeAndSunshineStats::class, 'Google_Service_Solar_RoofSegmentSizeAndSunshineStats');
