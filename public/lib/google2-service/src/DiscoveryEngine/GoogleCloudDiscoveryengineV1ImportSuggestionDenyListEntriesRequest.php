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

class GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequest extends \Google\Model
{
  protected $gcsSourceType = GoogleCloudDiscoveryengineV1GcsSource::class;
  protected $gcsSourceDataType = '';
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequestInlineSource::class;
  protected $inlineSourceDataType = '';

  /**
   * Cloud Storage location for the input content. Only 1 file can be specified
   * that contains all entries to import. Supported values `gcs_source.schema`
   * for autocomplete suggestion deny list entry imports: *
   * `suggestion_deny_list` (default): One JSON [SuggestionDenyListEntry] per
   * line.
   *
   * @param GoogleCloudDiscoveryengineV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudDiscoveryengineV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * The Inline source for the input content for suggestion deny list entries.
   *
   * @param GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequestInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequest');
