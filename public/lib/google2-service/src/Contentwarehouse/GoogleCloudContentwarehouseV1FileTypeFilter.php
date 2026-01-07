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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1FileTypeFilter extends \Google\Model
{
  /**
   * Default document type. If set, disables the filter.
   */
  public const FILE_TYPE_FILE_TYPE_UNSPECIFIED = 'FILE_TYPE_UNSPECIFIED';
  /**
   * Returns all document types, including folders.
   */
  public const FILE_TYPE_ALL = 'ALL';
  /**
   * Returns only folders.
   */
  public const FILE_TYPE_FOLDER = 'FOLDER';
  /**
   * Returns only non-folder documents.
   */
  public const FILE_TYPE_DOCUMENT = 'DOCUMENT';
  /**
   * Returns only root folders
   */
  public const FILE_TYPE_ROOT_FOLDER = 'ROOT_FOLDER';
  /**
   * The type of files to return.
   *
   * @var string
   */
  public $fileType;

  /**
   * The type of files to return.
   *
   * Accepted values: FILE_TYPE_UNSPECIFIED, ALL, FOLDER, DOCUMENT, ROOT_FOLDER
   *
   * @param self::FILE_TYPE_* $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return self::FILE_TYPE_*
   */
  public function getFileType()
  {
    return $this->fileType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1FileTypeFilter::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1FileTypeFilter');
