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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1CustomCuration extends \Google\Model
{
  /**
   * Required. The unique name of the curation resource. This will be the name
   * of the curation resource in the format:
   * `projects/{project}/locations/{location}/curations/{curation}`
   *
   * @var string
   */
  public $curation;

  /**
   * Required. The unique name of the curation resource. This will be the name
   * of the curation resource in the format:
   * `projects/{project}/locations/{location}/curations/{curation}`
   *
   * @param string $curation
   */
  public function setCuration($curation)
  {
    $this->curation = $curation;
  }
  /**
   * @return string
   */
  public function getCuration()
  {
    return $this->curation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1CustomCuration::class, 'Google_Service_APIhub_GoogleCloudApihubV1CustomCuration');
