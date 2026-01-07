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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig extends \Google\Model
{
  /**
   * Optional. The chunk sizes to use when splitting documents, in order of
   * level.
   *
   * @var int
   */
  public $chunkSize;
  /**
   * Optional. Whether or not to include ancestor headings when splitting.
   *
   * @var bool
   */
  public $includeAncestorHeadings;

  /**
   * Optional. The chunk sizes to use when splitting documents, in order of
   * level.
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
   * Optional. Whether or not to include ancestor headings when splitting.
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
class_alias(GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessOptionsLayoutConfigChunkingConfig');
