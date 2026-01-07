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

class GoogleCloudAiplatformV1PurgeContextsResponse extends \Google\Collection
{
  protected $collection_key = 'purgeSample';
  /**
   * The number of Contexts that this request deleted (or, if `force` is false,
   * the number of Contexts that will be deleted). This can be an estimate.
   *
   * @var string
   */
  public $purgeCount;
  /**
   * A sample of the Context names that will be deleted. Only populated if
   * `force` is set to false. The maximum number of samples is 100 (it is
   * possible to return fewer).
   *
   * @var string[]
   */
  public $purgeSample;

  /**
   * The number of Contexts that this request deleted (or, if `force` is false,
   * the number of Contexts that will be deleted). This can be an estimate.
   *
   * @param string $purgeCount
   */
  public function setPurgeCount($purgeCount)
  {
    $this->purgeCount = $purgeCount;
  }
  /**
   * @return string
   */
  public function getPurgeCount()
  {
    return $this->purgeCount;
  }
  /**
   * A sample of the Context names that will be deleted. Only populated if
   * `force` is set to false. The maximum number of samples is 100 (it is
   * possible to return fewer).
   *
   * @param string[] $purgeSample
   */
  public function setPurgeSample($purgeSample)
  {
    $this->purgeSample = $purgeSample;
  }
  /**
   * @return string[]
   */
  public function getPurgeSample()
  {
    return $this->purgeSample;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PurgeContextsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PurgeContextsResponse');
