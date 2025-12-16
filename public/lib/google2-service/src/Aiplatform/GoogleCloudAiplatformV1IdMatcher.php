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

class GoogleCloudAiplatformV1IdMatcher extends \Google\Collection
{
  protected $collection_key = 'ids';
  /**
   * Required. The following are accepted as `ids`: * A single-element list
   * containing only `*`, which selects all Features in the target EntityType,
   * or * A list containing only Feature IDs, which selects only Features with
   * those IDs in the target EntityType.
   *
   * @var string[]
   */
  public $ids;

  /**
   * Required. The following are accepted as `ids`: * A single-element list
   * containing only `*`, which selects all Features in the target EntityType,
   * or * A list containing only Feature IDs, which selects only Features with
   * those IDs in the target EntityType.
   *
   * @param string[] $ids
   */
  public function setIds($ids)
  {
    $this->ids = $ids;
  }
  /**
   * @return string[]
   */
  public function getIds()
  {
    return $this->ids;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1IdMatcher::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IdMatcher');
