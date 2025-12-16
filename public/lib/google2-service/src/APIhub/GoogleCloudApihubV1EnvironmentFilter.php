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

class GoogleCloudApihubV1EnvironmentFilter extends \Google\Collection
{
  protected $collection_key = 'environments';
  /**
   * Optional. Indicates if this filter should match all environments or only a
   * subset of environments. If set to true, all environments are matched.
   *
   * @var bool
   */
  public $allEnvironments;
  /**
   * Optional. If provided, only environments in this list are matched. This
   * field is ignored if `all_environments` is true.
   *
   * @var string[]
   */
  public $environments;

  /**
   * Optional. Indicates if this filter should match all environments or only a
   * subset of environments. If set to true, all environments are matched.
   *
   * @param bool $allEnvironments
   */
  public function setAllEnvironments($allEnvironments)
  {
    $this->allEnvironments = $allEnvironments;
  }
  /**
   * @return bool
   */
  public function getAllEnvironments()
  {
    return $this->allEnvironments;
  }
  /**
   * Optional. If provided, only environments in this list are matched. This
   * field is ignored if `all_environments` is true.
   *
   * @param string[] $environments
   */
  public function setEnvironments($environments)
  {
    $this->environments = $environments;
  }
  /**
   * @return string[]
   */
  public function getEnvironments()
  {
    return $this->environments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1EnvironmentFilter::class, 'Google_Service_APIhub_GoogleCloudApihubV1EnvironmentFilter');
