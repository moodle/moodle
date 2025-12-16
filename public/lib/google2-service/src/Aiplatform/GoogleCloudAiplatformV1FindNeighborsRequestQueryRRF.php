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

class GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF extends \Google\Model
{
  /**
   * Required. Users can provide an alpha value to give more weight to dense vs
   * sparse results. For example, if the alpha is 0, we only return sparse and
   * if the alpha is 1, we only return dense.
   *
   * @var float
   */
  public $alpha;

  /**
   * Required. Users can provide an alpha value to give more weight to dense vs
   * sparse results. For example, if the alpha is 0, we only return sparse and
   * if the alpha is 1, we only return dense.
   *
   * @param float $alpha
   */
  public function setAlpha($alpha)
  {
    $this->alpha = $alpha;
  }
  /**
   * @return float
   */
  public function getAlpha()
  {
    return $this->alpha;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FindNeighborsRequestQueryRRF');
