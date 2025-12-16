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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CloudStorageOptions extends \Google\Collection
{
  /**
   * No sampling.
   */
  public const SAMPLE_METHOD_SAMPLE_METHOD_UNSPECIFIED = 'SAMPLE_METHOD_UNSPECIFIED';
  /**
   * Scan from the top (default).
   */
  public const SAMPLE_METHOD_TOP = 'TOP';
  /**
   * For each file larger than bytes_limit_per_file, randomly pick the offset to
   * start scanning. The scanned bytes are contiguous.
   */
  public const SAMPLE_METHOD_RANDOM_START = 'RANDOM_START';
  protected $collection_key = 'fileTypes';
  /**
   * Max number of bytes to scan from a file. If a scanned file's size is bigger
   * than this value then the rest of the bytes are omitted. Only one of
   * `bytes_limit_per_file` and `bytes_limit_per_file_percent` can be specified.
   * This field can't be set if de-identification is requested. For certain file
   * types, setting this field has no effect. For more information, see [Limits
   * on bytes scanned per file](https://cloud.google.com/sensitive-data-
   * protection/docs/supported-file-types#max-byte-size-per-file).
   *
   * @var string
   */
  public $bytesLimitPerFile;
  /**
   * Max percentage of bytes to scan from a file. The rest are omitted. The
   * number of bytes scanned is rounded down. Must be between 0 and 100,
   * inclusively. Both 0 and 100 means no limit. Defaults to 0. Only one of
   * bytes_limit_per_file and bytes_limit_per_file_percent can be specified.
   * This field can't be set if de-identification is requested. For certain file
   * types, setting this field has no effect. For more information, see [Limits
   * on bytes scanned per file](https://cloud.google.com/sensitive-data-
   * protection/docs/supported-file-types#max-byte-size-per-file).
   *
   * @var int
   */
  public $bytesLimitPerFilePercent;
  protected $fileSetType = GooglePrivacyDlpV2FileSet::class;
  protected $fileSetDataType = '';
  /**
   * List of file type groups to include in the scan. If empty, all files are
   * scanned and available data format processors are applied. In addition, the
   * binary content of the selected files is always scanned as well. Images are
   * scanned only as binary if the specified region does not support image
   * inspection and no file_types were specified. Image inspection is restricted
   * to 'global', 'us', 'asia', and 'europe'.
   *
   * @var string[]
   */
  public $fileTypes;
  /**
   * Limits the number of files to scan to this percentage of the input FileSet.
   * Number of files scanned is rounded down. Must be between 0 and 100,
   * inclusively. Both 0 and 100 means no limit. Defaults to 0.
   *
   * @var int
   */
  public $filesLimitPercent;
  /**
   * How to sample the data.
   *
   * @var string
   */
  public $sampleMethod;

  /**
   * Max number of bytes to scan from a file. If a scanned file's size is bigger
   * than this value then the rest of the bytes are omitted. Only one of
   * `bytes_limit_per_file` and `bytes_limit_per_file_percent` can be specified.
   * This field can't be set if de-identification is requested. For certain file
   * types, setting this field has no effect. For more information, see [Limits
   * on bytes scanned per file](https://cloud.google.com/sensitive-data-
   * protection/docs/supported-file-types#max-byte-size-per-file).
   *
   * @param string $bytesLimitPerFile
   */
  public function setBytesLimitPerFile($bytesLimitPerFile)
  {
    $this->bytesLimitPerFile = $bytesLimitPerFile;
  }
  /**
   * @return string
   */
  public function getBytesLimitPerFile()
  {
    return $this->bytesLimitPerFile;
  }
  /**
   * Max percentage of bytes to scan from a file. The rest are omitted. The
   * number of bytes scanned is rounded down. Must be between 0 and 100,
   * inclusively. Both 0 and 100 means no limit. Defaults to 0. Only one of
   * bytes_limit_per_file and bytes_limit_per_file_percent can be specified.
   * This field can't be set if de-identification is requested. For certain file
   * types, setting this field has no effect. For more information, see [Limits
   * on bytes scanned per file](https://cloud.google.com/sensitive-data-
   * protection/docs/supported-file-types#max-byte-size-per-file).
   *
   * @param int $bytesLimitPerFilePercent
   */
  public function setBytesLimitPerFilePercent($bytesLimitPerFilePercent)
  {
    $this->bytesLimitPerFilePercent = $bytesLimitPerFilePercent;
  }
  /**
   * @return int
   */
  public function getBytesLimitPerFilePercent()
  {
    return $this->bytesLimitPerFilePercent;
  }
  /**
   * The set of one or more files to scan.
   *
   * @param GooglePrivacyDlpV2FileSet $fileSet
   */
  public function setFileSet(GooglePrivacyDlpV2FileSet $fileSet)
  {
    $this->fileSet = $fileSet;
  }
  /**
   * @return GooglePrivacyDlpV2FileSet
   */
  public function getFileSet()
  {
    return $this->fileSet;
  }
  /**
   * List of file type groups to include in the scan. If empty, all files are
   * scanned and available data format processors are applied. In addition, the
   * binary content of the selected files is always scanned as well. Images are
   * scanned only as binary if the specified region does not support image
   * inspection and no file_types were specified. Image inspection is restricted
   * to 'global', 'us', 'asia', and 'europe'.
   *
   * @param string[] $fileTypes
   */
  public function setFileTypes($fileTypes)
  {
    $this->fileTypes = $fileTypes;
  }
  /**
   * @return string[]
   */
  public function getFileTypes()
  {
    return $this->fileTypes;
  }
  /**
   * Limits the number of files to scan to this percentage of the input FileSet.
   * Number of files scanned is rounded down. Must be between 0 and 100,
   * inclusively. Both 0 and 100 means no limit. Defaults to 0.
   *
   * @param int $filesLimitPercent
   */
  public function setFilesLimitPercent($filesLimitPercent)
  {
    $this->filesLimitPercent = $filesLimitPercent;
  }
  /**
   * @return int
   */
  public function getFilesLimitPercent()
  {
    return $this->filesLimitPercent;
  }
  /**
   * How to sample the data.
   *
   * Accepted values: SAMPLE_METHOD_UNSPECIFIED, TOP, RANDOM_START
   *
   * @param self::SAMPLE_METHOD_* $sampleMethod
   */
  public function setSampleMethod($sampleMethod)
  {
    $this->sampleMethod = $sampleMethod;
  }
  /**
   * @return self::SAMPLE_METHOD_*
   */
  public function getSampleMethod()
  {
    return $this->sampleMethod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CloudStorageOptions::class, 'Google_Service_DLP_GooglePrivacyDlpV2CloudStorageOptions');
