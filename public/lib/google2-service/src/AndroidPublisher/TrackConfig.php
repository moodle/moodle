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

namespace Google\Service\AndroidPublisher;

class TrackConfig extends \Google\Model
{
  /**
   * Fallback value, do not use.
   */
  public const FORM_FACTOR_FORM_FACTOR_UNSPECIFIED = 'FORM_FACTOR_UNSPECIFIED';
  /**
   * Default track.
   */
  public const FORM_FACTOR_DEFAULT = 'DEFAULT';
  /**
   * Wear form factor track.
   */
  public const FORM_FACTOR_WEAR = 'WEAR';
  /**
   * Automotive form factor track.
   */
  public const FORM_FACTOR_AUTOMOTIVE = 'AUTOMOTIVE';
  /**
   * Fallback value, do not use.
   */
  public const TYPE_TRACK_TYPE_UNSPECIFIED = 'TRACK_TYPE_UNSPECIFIED';
  /**
   * Closed testing track.
   */
  public const TYPE_CLOSED_TESTING = 'CLOSED_TESTING';
  /**
   * Required. Form factor of the new track. Defaults to the default track.
   *
   * @var string
   */
  public $formFactor;
  /**
   * Required. Identifier of the new track. For default tracks, this field
   * consists of the track alias only. Form factor tracks have a special prefix
   * as an identifier, for example `wear:production`, `automotive:production`.
   * This prefix must match the value of the `form_factor` field, if it is not a
   * default track. [More on track name](https://developers.google.com/android-
   * publisher/tracks#ff-track-name)
   *
   * @var string
   */
  public $track;
  /**
   * Required. Type of the new track. Currently, the only supported value is
   * closedTesting.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Form factor of the new track. Defaults to the default track.
   *
   * Accepted values: FORM_FACTOR_UNSPECIFIED, DEFAULT, WEAR, AUTOMOTIVE
   *
   * @param self::FORM_FACTOR_* $formFactor
   */
  public function setFormFactor($formFactor)
  {
    $this->formFactor = $formFactor;
  }
  /**
   * @return self::FORM_FACTOR_*
   */
  public function getFormFactor()
  {
    return $this->formFactor;
  }
  /**
   * Required. Identifier of the new track. For default tracks, this field
   * consists of the track alias only. Form factor tracks have a special prefix
   * as an identifier, for example `wear:production`, `automotive:production`.
   * This prefix must match the value of the `form_factor` field, if it is not a
   * default track. [More on track name](https://developers.google.com/android-
   * publisher/tracks#ff-track-name)
   *
   * @param string $track
   */
  public function setTrack($track)
  {
    $this->track = $track;
  }
  /**
   * @return string
   */
  public function getTrack()
  {
    return $this->track;
  }
  /**
   * Required. Type of the new track. Currently, the only supported value is
   * closedTesting.
   *
   * Accepted values: TRACK_TYPE_UNSPECIFIED, CLOSED_TESTING
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
class_alias(TrackConfig::class, 'Google_Service_AndroidPublisher_TrackConfig');
