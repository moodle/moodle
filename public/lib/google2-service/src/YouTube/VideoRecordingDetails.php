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

class VideoRecordingDetails extends \Google\Model
{
  protected $locationType = GeoPoint::class;
  protected $locationDataType = '';
  /**
   * The text description of the location where the video was recorded.
   *
   * @var string
   */
  public $locationDescription;
  /**
   * The date and time when the video was recorded.
   *
   * @var string
   */
  public $recordingDate;

  /**
   * The geolocation information associated with the video.
   *
   * @param GeoPoint $location
   */
  public function setLocation(GeoPoint $location)
  {
    $this->location = $location;
  }
  /**
   * @return GeoPoint
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The text description of the location where the video was recorded.
   *
   * @param string $locationDescription
   */
  public function setLocationDescription($locationDescription)
  {
    $this->locationDescription = $locationDescription;
  }
  /**
   * @return string
   */
  public function getLocationDescription()
  {
    return $this->locationDescription;
  }
  /**
   * The date and time when the video was recorded.
   *
   * @param string $recordingDate
   */
  public function setRecordingDate($recordingDate)
  {
    $this->recordingDate = $recordingDate;
  }
  /**
   * @return string
   */
  public function getRecordingDate()
  {
    return $this->recordingDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoRecordingDetails::class, 'Google_Service_YouTube_VideoRecordingDetails');
