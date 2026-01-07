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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Neighbor extends \Google\Model
{
  /**
   * Output only. The neighbor distance.
   *
   * @var 
   */
  public $neighborDistance;
  /**
   * Output only. The neighbor id.
   *
   * @var string
   */
  public $neighborId;

  public function setNeighborDistance($neighborDistance)
  {
    $this->neighborDistance = $neighborDistance;
  }
  public function getNeighborDistance()
  {
    return $this->neighborDistance;
  }
  /**
   * Output only. The neighbor id.
   *
   * @param string $neighborId
   */
  public function setNeighborId($neighborId)
  {
    $this->neighborId = $neighborId;
  }
  /**
   * @return string
   */
  public function getNeighborId()
  {
    return $this->neighborId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Neighbor::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Neighbor');
