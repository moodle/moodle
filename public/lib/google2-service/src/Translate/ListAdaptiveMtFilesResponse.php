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

namespace Google\Service\Translate;

class ListAdaptiveMtFilesResponse extends \Google\Collection
{
  protected $collection_key = 'adaptiveMtFiles';
  protected $adaptiveMtFilesType = AdaptiveMtFile::class;
  protected $adaptiveMtFilesDataType = 'array';
  /**
   * Optional. A token to retrieve a page of results. Pass this value in the
   * ListAdaptiveMtFilesRequest.page_token field in the subsequent call to
   * `ListAdaptiveMtFiles` method to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Output only. The Adaptive MT files.
   *
   * @param AdaptiveMtFile[] $adaptiveMtFiles
   */
  public function setAdaptiveMtFiles($adaptiveMtFiles)
  {
    $this->adaptiveMtFiles = $adaptiveMtFiles;
  }
  /**
   * @return AdaptiveMtFile[]
   */
  public function getAdaptiveMtFiles()
  {
    return $this->adaptiveMtFiles;
  }
  /**
   * Optional. A token to retrieve a page of results. Pass this value in the
   * ListAdaptiveMtFilesRequest.page_token field in the subsequent call to
   * `ListAdaptiveMtFiles` method to retrieve the next page of results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAdaptiveMtFilesResponse::class, 'Google_Service_Translate_ListAdaptiveMtFilesResponse');
