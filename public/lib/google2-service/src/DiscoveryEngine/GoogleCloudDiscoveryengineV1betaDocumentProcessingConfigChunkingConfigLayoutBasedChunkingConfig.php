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

class GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigChunkingConfigLayoutBasedChunkingConfig extends \Google\Model
{
  /**
   * The token size limit for each chunk. Supported values: 100-500 (inclusive).
   * Default value: 500.
   *
   * @var int
   */
  public $chunkSize;
  /**
   * Whether to include appending different levels of headings to chunks from
   * the middle of the document to prevent context loss. Default value: False.
   *
   * @var bool
   */
  public $includeAncestorHeadings;

  /**
   * The token size limit for each chunk. Supported values: 100-500 (inclusive).
   * Default value: 500.
   *
   * @param int $chunkSize
   */
  public function setChunkSize($chunkSize)
  {
    $this->chunkSize = $chunkSize;
  }
  /**
   * @return int
   */
  public function getChunkSize()
  {
    return $this->chunkSize;
  }
  /**
   * Whether to include appending different levels of headings to chunks from
   * the middle of the document to prevent context loss. Default value: False.
   *
   * @param bool $includeAncestorHeadings
   */
  public function setIncludeAncestorHeadings($includeAncestorHeadings)
  {
    $this->includeAncestorHeadings = $includeAncestorHeadings;
  }
  /**
   * @return bool
   */
  public function getIncludeAncestorHeadings()
  {
    return $this->includeAncestorHeadings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigChunkingConfigLayoutBasedChunkingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigChunkingConfigLayoutBasedChunkingConfig');
