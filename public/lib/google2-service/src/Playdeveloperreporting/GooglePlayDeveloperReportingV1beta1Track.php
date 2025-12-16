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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1Track extends \Google\Collection
{
  protected $collection_key = 'servingReleases';
  /**
   * Readable identifier of the track.
   *
   * @var string
   */
  public $displayName;
  protected $servingReleasesType = GooglePlayDeveloperReportingV1beta1Release::class;
  protected $servingReleasesDataType = 'array';
  /**
   * The type of the track.
   *
   * @var string
   */
  public $type;

  /**
   * Readable identifier of the track.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Represents all active releases in the track.
   *
   * @param GooglePlayDeveloperReportingV1beta1Release[] $servingReleases
   */
  public function setServingReleases($servingReleases)
  {
    $this->servingReleases = $servingReleases;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1Release[]
   */
  public function getServingReleases()
  {
    return $this->servingReleases;
  }
  /**
   * The type of the track.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1Track::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1Track');
