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

namespace Google\Service\BinaryAuthorization;

class ImageFreshnessCheck extends \Google\Model
{
  /**
   * Required. The max number of days that is allowed since the image was
   * uploaded. Must be greater than zero.
   *
   * @var int
   */
  public $maxUploadAgeDays;

  /**
   * Required. The max number of days that is allowed since the image was
   * uploaded. Must be greater than zero.
   *
   * @param int $maxUploadAgeDays
   */
  public function setMaxUploadAgeDays($maxUploadAgeDays)
  {
    $this->maxUploadAgeDays = $maxUploadAgeDays;
  }
  /**
   * @return int
   */
  public function getMaxUploadAgeDays()
  {
    return $this->maxUploadAgeDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageFreshnessCheck::class, 'Google_Service_BinaryAuthorization_ImageFreshnessCheck');
