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

namespace Google\Service\AndroidManagement;

class AppTrackInfo extends \Google\Model
{
  /**
   * The track name associated with the trackId, set in the Play Console. The
   * name is modifiable from Play Console.
   *
   * @var string
   */
  public $trackAlias;
  /**
   * The unmodifiable unique track identifier, taken from the releaseTrackId in
   * the URL of the Play Console page that displays the app’s track information.
   *
   * @var string
   */
  public $trackId;

  /**
   * The track name associated with the trackId, set in the Play Console. The
   * name is modifiable from Play Console.
   *
   * @param string $trackAlias
   */
  public function setTrackAlias($trackAlias)
  {
    $this->trackAlias = $trackAlias;
  }
  /**
   * @return string
   */
  public function getTrackAlias()
  {
    return $this->trackAlias;
  }
  /**
   * The unmodifiable unique track identifier, taken from the releaseTrackId in
   * the URL of the Play Console page that displays the app’s track information.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppTrackInfo::class, 'Google_Service_AndroidManagement_AppTrackInfo');
