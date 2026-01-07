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

namespace Google\Service\Dataform;

class SearchResult extends \Google\Model
{
  protected $directoryType = DirectorySearchResult::class;
  protected $directoryDataType = '';
  protected $fileType = FileSearchResult::class;
  protected $fileDataType = '';

  /**
   * Details when search result is a directory.
   *
   * @param DirectorySearchResult $directory
   */
  public function setDirectory(DirectorySearchResult $directory)
  {
    $this->directory = $directory;
  }
  /**
   * @return DirectorySearchResult
   */
  public function getDirectory()
  {
    return $this->directory;
  }
  /**
   * Details when search result is a file.
   *
   * @param FileSearchResult $file
   */
  public function setFile(FileSearchResult $file)
  {
    $this->file = $file;
  }
  /**
   * @return FileSearchResult
   */
  public function getFile()
  {
    return $this->file;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchResult::class, 'Google_Service_Dataform_SearchResult');
