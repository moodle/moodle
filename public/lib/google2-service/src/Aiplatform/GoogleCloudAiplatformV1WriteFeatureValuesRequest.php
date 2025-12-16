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

class GoogleCloudAiplatformV1WriteFeatureValuesRequest extends \Google\Collection
{
  protected $collection_key = 'payloads';
  protected $payloadsType = GoogleCloudAiplatformV1WriteFeatureValuesPayload::class;
  protected $payloadsDataType = 'array';

  /**
   * Required. The entities to be written. Up to 100,000 feature values can be
   * written across all `payloads`.
   *
   * @param GoogleCloudAiplatformV1WriteFeatureValuesPayload[] $payloads
   */
  public function setPayloads($payloads)
  {
    $this->payloads = $payloads;
  }
  /**
   * @return GoogleCloudAiplatformV1WriteFeatureValuesPayload[]
   */
  public function getPayloads()
  {
    return $this->payloads;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1WriteFeatureValuesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1WriteFeatureValuesRequest');
