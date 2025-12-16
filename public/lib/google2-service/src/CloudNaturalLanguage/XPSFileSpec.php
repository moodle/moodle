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

namespace Google\Service\CloudNaturalLanguage;

class XPSFileSpec extends \Google\Model
{
  public const FILE_FORMAT_FILE_FORMAT_UNKNOWN = 'FILE_FORMAT_UNKNOWN';
  /**
   * @deprecated
   */
  public const FILE_FORMAT_FILE_FORMAT_SSTABLE = 'FILE_FORMAT_SSTABLE';
  /**
   * Internal format for parallel text data used by Google Translate.
   */
  public const FILE_FORMAT_FILE_FORMAT_TRANSLATION_RKV = 'FILE_FORMAT_TRANSLATION_RKV';
  public const FILE_FORMAT_FILE_FORMAT_RECORDIO = 'FILE_FORMAT_RECORDIO';
  /**
   * Only the lexicographically first file described by the file_spec contains
   * the header line.
   */
  public const FILE_FORMAT_FILE_FORMAT_RAW_CSV = 'FILE_FORMAT_RAW_CSV';
  public const FILE_FORMAT_FILE_FORMAT_RAW_CAPACITOR = 'FILE_FORMAT_RAW_CAPACITOR';
  /**
   * Deprecated. Use file_spec.
   *
   * @deprecated
   * @var string
   */
  public $directoryPath;
  /**
   * @var string
   */
  public $fileFormat;
  /**
   * Single file path, or file pattern of format "/path/to/file@shard_count".
   * E.g. /cns/cell-d/somewhere/file@2 is expanded to two files:
   * /cns/cell-d/somewhere/file-00000-of-00002 and
   * /cns/cell-d/somewhere/file-00001-of-00002.
   *
   * @var string
   */
  public $fileSpec;
  /**
   * Deprecated. Use file_spec.
   *
   * @deprecated
   * @var string
   */
  public $singleFilePath;

  /**
   * Deprecated. Use file_spec.
   *
   * @deprecated
   * @param string $directoryPath
   */
  public function setDirectoryPath($directoryPath)
  {
    $this->directoryPath = $directoryPath;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDirectoryPath()
  {
    return $this->directoryPath;
  }
  /**
   * @param self::FILE_FORMAT_* $fileFormat
   */
  public function setFileFormat($fileFormat)
  {
    $this->fileFormat = $fileFormat;
  }
  /**
   * @return self::FILE_FORMAT_*
   */
  public function getFileFormat()
  {
    return $this->fileFormat;
  }
  /**
   * Single file path, or file pattern of format "/path/to/file@shard_count".
   * E.g. /cns/cell-d/somewhere/file@2 is expanded to two files:
   * /cns/cell-d/somewhere/file-00000-of-00002 and
   * /cns/cell-d/somewhere/file-00001-of-00002.
   *
   * @param string $fileSpec
   */
  public function setFileSpec($fileSpec)
  {
    $this->fileSpec = $fileSpec;
  }
  /**
   * @return string
   */
  public function getFileSpec()
  {
    return $this->fileSpec;
  }
  /**
   * Deprecated. Use file_spec.
   *
   * @deprecated
   * @param string $singleFilePath
   */
  public function setSingleFilePath($singleFilePath)
  {
    $this->singleFilePath = $singleFilePath;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getSingleFilePath()
  {
    return $this->singleFilePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSFileSpec::class, 'Google_Service_CloudNaturalLanguage_XPSFileSpec');
