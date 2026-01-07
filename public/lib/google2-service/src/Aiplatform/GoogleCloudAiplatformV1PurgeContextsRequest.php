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

class GoogleCloudAiplatformV1PurgeContextsRequest extends \Google\Model
{
  /**
   * Required. A required filter matching the Contexts to be purged. E.g.,
   * `update_time <= 2020-11-19T11:30:00-04:00`.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. Flag to indicate to actually perform the purge. If `force` is set
   * to false, the method will return a sample of Context names that would be
   * deleted.
   *
   * @var bool
   */
  public $force;

  /**
   * Required. A required filter matching the Contexts to be purged. E.g.,
   * `update_time <= 2020-11-19T11:30:00-04:00`.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. Flag to indicate to actually perform the purge. If `force` is set
   * to false, the method will return a sample of Context names that would be
   * deleted.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PurgeContextsRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PurgeContextsRequest');
