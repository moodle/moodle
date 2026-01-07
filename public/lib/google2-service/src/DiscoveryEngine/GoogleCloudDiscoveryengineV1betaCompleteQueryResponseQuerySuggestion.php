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

class GoogleCloudDiscoveryengineV1betaCompleteQueryResponseQuerySuggestion extends \Google\Collection
{
  protected $collection_key = 'completableFieldPaths';
  /**
   * @var string[]
   */
  public $completableFieldPaths;
  /**
   * @var string
   */
  public $suggestion;

  /**
   * @param string[]
   */
  public function setCompletableFieldPaths($completableFieldPaths)
  {
    $this->completableFieldPaths = $completableFieldPaths;
  }
  /**
   * @return string[]
   */
  public function getCompletableFieldPaths()
  {
    return $this->completableFieldPaths;
  }
  /**
   * @param string
   */
  public function setSuggestion($suggestion)
  {
    $this->suggestion = $suggestion;
  }
  /**
   * @return string
   */
  public function getSuggestion()
  {
    return $this->suggestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaCompleteQueryResponseQuerySuggestion::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaCompleteQueryResponseQuerySuggestion');
