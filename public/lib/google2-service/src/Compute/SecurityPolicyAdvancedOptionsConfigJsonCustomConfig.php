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

namespace Google\Service\Compute;

class SecurityPolicyAdvancedOptionsConfigJsonCustomConfig extends \Google\Collection
{
  protected $collection_key = 'contentTypes';
  /**
   * A list of custom Content-Type header values to apply the JSON parsing.
   *
   * As per RFC 1341, a Content-Type header value has the following format:
   *
   * Content-Type := type "/" subtype *[";" parameter]
   *
   * When configuring a custom Content-Type header value, only the type/subtype
   * needs to be specified, and the parameters should be excluded.
   *
   * @var string[]
   */
  public $contentTypes;

  /**
   * A list of custom Content-Type header values to apply the JSON parsing.
   *
   * As per RFC 1341, a Content-Type header value has the following format:
   *
   * Content-Type := type "/" subtype *[";" parameter]
   *
   * When configuring a custom Content-Type header value, only the type/subtype
   * needs to be specified, and the parameters should be excluded.
   *
   * @param string[] $contentTypes
   */
  public function setContentTypes($contentTypes)
  {
    $this->contentTypes = $contentTypes;
  }
  /**
   * @return string[]
   */
  public function getContentTypes()
  {
    return $this->contentTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyAdvancedOptionsConfigJsonCustomConfig::class, 'Google_Service_Compute_SecurityPolicyAdvancedOptionsConfigJsonCustomConfig');
