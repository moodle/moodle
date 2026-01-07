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

class GoogleCloudAiplatformV1RagChunkPageSpan extends \Google\Model
{
  /**
   * Page where chunk starts in the document. Inclusive. 1-indexed.
   *
   * @var int
   */
  public $firstPage;
  /**
   * Page where chunk ends in the document. Inclusive. 1-indexed.
   *
   * @var int
   */
  public $lastPage;

  /**
   * Page where chunk starts in the document. Inclusive. 1-indexed.
   *
   * @param int $firstPage
   */
  public function setFirstPage($firstPage)
  {
    $this->firstPage = $firstPage;
  }
  /**
   * @return int
   */
  public function getFirstPage()
  {
    return $this->firstPage;
  }
  /**
   * Page where chunk ends in the document. Inclusive. 1-indexed.
   *
   * @param int $lastPage
   */
  public function setLastPage($lastPage)
  {
    $this->lastPage = $lastPage;
  }
  /**
   * @return int
   */
  public function getLastPage()
  {
    return $this->lastPage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagChunkPageSpan::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagChunkPageSpan');
