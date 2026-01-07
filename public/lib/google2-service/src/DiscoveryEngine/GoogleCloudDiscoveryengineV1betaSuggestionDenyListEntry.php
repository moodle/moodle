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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaSuggestionDenyListEntry extends \Google\Model
{
  /**
   * @var string
   */
  public $blockPhrase;
  /**
   * @var string
   */
  public $matchOperator;

  /**
   * @param string
   */
  public function setBlockPhrase($blockPhrase)
  {
    $this->blockPhrase = $blockPhrase;
  }
  /**
   * @return string
   */
  public function getBlockPhrase()
  {
    return $this->blockPhrase;
  }
  /**
   * @param string
   */
  public function setMatchOperator($matchOperator)
  {
    $this->matchOperator = $matchOperator;
  }
  /**
   * @return string
   */
  public function getMatchOperator()
  {
    return $this->matchOperator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSuggestionDenyListEntry::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSuggestionDenyListEntry');
