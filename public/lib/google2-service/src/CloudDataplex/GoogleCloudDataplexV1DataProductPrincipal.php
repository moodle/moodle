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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProductPrincipal extends \Google\Model
{
  /**
   * Email of the Google Group, as per
   * https://cloud.google.com/iam/docs/principals-overview#google-group.
   *
   * @var string
   */
  public $googleGroup;

  /**
   * Email of the Google Group, as per
   * https://cloud.google.com/iam/docs/principals-overview#google-group.
   *
   * @param string $googleGroup
   */
  public function setGoogleGroup($googleGroup)
  {
    $this->googleGroup = $googleGroup;
  }
  /**
   * @return string
   */
  public function getGoogleGroup()
  {
    return $this->googleGroup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProductPrincipal::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProductPrincipal');
