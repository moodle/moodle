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

class AllowlistResult extends \Google\Model
{
  /**
   * The allowlist pattern that the image matched.
   *
   * @var string
   */
  public $matchedPattern;

  /**
   * The allowlist pattern that the image matched.
   *
   * @param string $matchedPattern
   */
  public function setMatchedPattern($matchedPattern)
  {
    $this->matchedPattern = $matchedPattern;
  }
  /**
   * @return string
   */
  public function getMatchedPattern()
  {
    return $this->matchedPattern;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllowlistResult::class, 'Google_Service_BinaryAuthorization_AllowlistResult');
