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

namespace Google\Service\Drive;

class FileList extends \Google\Collection
{
  protected $collection_key = 'files';
  protected $filesType = DriveFile::class;
  protected $filesDataType = 'array';
  /**
   * Whether the search process was incomplete. If true, then some search
   * results might be missing, since all documents were not searched. This can
   * occur when searching multiple drives with the `allDrives` corpora, but all
   * corpora couldn't be searched. When this happens, it's suggested that
   * clients narrow their query by choosing a different corpus such as `user` or
   * `drive`.
   *
   * @var bool
   */
  public $incompleteSearch;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#fileList"`.
   *
   * @var string
   */
  public $kind;
  /**
   * The page token for the next page of files. This will be absent if the end
   * of the files list has been reached. If the token is rejected for any
   * reason, it should be discarded, and pagination should be restarted from the
   * first page of results. The page token is typically valid for several hours.
   * However, if new items are added or removed, your expected results might
   * differ.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of files. If `nextPageToken` is populated, then this list may be
   * incomplete and an additional page of results should be fetched.
   *
   * @param DriveFile[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return DriveFile[]
   */
  public function getFiles()
  {
    return $this->files;
  }
  /**
   * Whether the search process was incomplete. If true, then some search
   * results might be missing, since all documents were not searched. This can
   * occur when searching multiple drives with the `allDrives` corpora, but all
   * corpora couldn't be searched. When this happens, it's suggested that
   * clients narrow their query by choosing a different corpus such as `user` or
   * `drive`.
   *
   * @param bool $incompleteSearch
   */
  public function setIncompleteSearch($incompleteSearch)
  {
    $this->incompleteSearch = $incompleteSearch;
  }
  /**
   * @return bool
   */
  public function getIncompleteSearch()
  {
    return $this->incompleteSearch;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#fileList"`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The page token for the next page of files. This will be absent if the end
   * of the files list has been reached. If the token is rejected for any
   * reason, it should be discarded, and pagination should be restarted from the
   * first page of results. The page token is typically valid for several hours.
   * However, if new items are added or removed, your expected results might
   * differ.
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
class_alias(FileList::class, 'Google_Service_Drive_FileList');
