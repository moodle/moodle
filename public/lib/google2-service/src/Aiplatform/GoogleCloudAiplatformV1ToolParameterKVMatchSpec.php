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

class GoogleCloudAiplatformV1ToolParameterKVMatchSpec extends \Google\Model
{
  /**
   * Optional. Whether to use STRICT string match on parameter values.
   *
   * @var bool
   */
  public $useStrictStringMatch;

  /**
   * Optional. Whether to use STRICT string match on parameter values.
   *
   * @param bool $useStrictStringMatch
   */
  public function setUseStrictStringMatch($useStrictStringMatch)
  {
    $this->useStrictStringMatch = $useStrictStringMatch;
  }
  /**
   * @return bool
   */
  public function getUseStrictStringMatch()
  {
    return $this->useStrictStringMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolParameterKVMatchSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolParameterKVMatchSpec');
