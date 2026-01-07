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

class GoogleCloudAiplatformV1BlurBaselineConfig extends \Google\Model
{
  /**
   * The standard deviation of the blur kernel for the blurred baseline. The
   * same blurring parameter is used for both the height and the width
   * dimension. If not set, the method defaults to the zero (i.e. black for
   * images) baseline.
   *
   * @var float
   */
  public $maxBlurSigma;

  /**
   * The standard deviation of the blur kernel for the blurred baseline. The
   * same blurring parameter is used for both the height and the width
   * dimension. If not set, the method defaults to the zero (i.e. black for
   * images) baseline.
   *
   * @param float $maxBlurSigma
   */
  public function setMaxBlurSigma($maxBlurSigma)
  {
    $this->maxBlurSigma = $maxBlurSigma;
  }
  /**
   * @return float
   */
  public function getMaxBlurSigma()
  {
    return $this->maxBlurSigma;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BlurBaselineConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BlurBaselineConfig');
