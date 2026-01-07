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

class GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec extends \Google\Model
{
  /**
   * Unspecified spell correction mode. In this case, server behavior defaults
   * to Mode.AUTO.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Search API tries to find a spelling suggestion. If a suggestion is found,
   * it is put in the SearchResponse.corrected_query. The spelling suggestion
   * won't be used as the search query.
   */
  public const MODE_SUGGESTION_ONLY = 'SUGGESTION_ONLY';
  /**
   * Automatic spell correction built by the Search API. Search will be based on
   * the corrected query if found.
   */
  public const MODE_AUTO = 'AUTO';
  /**
   * The mode under which spell correction replaces the original search query.
   * Defaults to Mode.AUTO.
   *
   * @var string
   */
  public $mode;

  /**
   * The mode under which spell correction replaces the original search query.
   * Defaults to Mode.AUTO.
   *
   * Accepted values: MODE_UNSPECIFIED, SUGGESTION_ONLY, AUTO
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec');
