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

class GooglePrivacyDlpV2CloudStorageRegexFileSet extends \Google\Collection
{
  protected $collection_key = 'includeRegex';
  /**
   * The name of a Cloud Storage bucket. Required.
   *
   * @var string
   */
  public $bucketName;
  /**
   * A list of regular expressions matching file paths to exclude. All files in
   * the bucket that match at least one of these regular expressions will be
   * excluded from the scan. Regular expressions use RE2
   * [syntax](https://github.com/google/re2/wiki/Syntax); a guide can be found
   * under the google/re2 repository on GitHub.
   *
   * @var string[]
   */
  public $excludeRegex;
  /**
   * A list of regular expressions matching file paths to include. All files in
   * the bucket that match at least one of these regular expressions will be
   * included in the set of files, except for those that also match an item in
   * `exclude_regex`. Leaving this field empty will match all files by default
   * (this is equivalent to including `.*` in the list). Regular expressions use
   * RE2 [syntax](https://github.com/google/re2/wiki/Syntax); a guide can be
   * found under the google/re2 repository on GitHub.
   *
   * @var string[]
   */
  public $includeRegex;

  /**
   * The name of a Cloud Storage bucket. Required.
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * A list of regular expressions matching file paths to exclude. All files in
   * the bucket that match at least one of these regular expressions will be
   * excluded from the scan. Regular expressions use RE2
   * [syntax](https://github.com/google/re2/wiki/Syntax); a guide can be found
   * under the google/re2 repository on GitHub.
   *
   * @param string[] $excludeRegex
   */
  public function setExcludeRegex($excludeRegex)
  {
    $this->excludeRegex = $excludeRegex;
  }
  /**
   * @return string[]
   */
  public function getExcludeRegex()
  {
    return $this->excludeRegex;
  }
  /**
   * A list of regular expressions matching file paths to include. All files in
   * the bucket that match at least one of these regular expressions will be
   * included in the set of files, except for those that also match an item in
   * `exclude_regex`. Leaving this field empty will match all files by default
   * (this is equivalent to including `.*` in the list). Regular expressions use
   * RE2 [syntax](https://github.com/google/re2/wiki/Syntax); a guide can be
   * found under the google/re2 repository on GitHub.
   *
   * @param string[] $includeRegex
   */
  public function setIncludeRegex($includeRegex)
  {
    $this->includeRegex = $includeRegex;
  }
  /**
   * @return string[]
   */
  public function getIncludeRegex()
  {
    return $this->includeRegex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CloudStorageRegexFileSet::class, 'Google_Service_DLP_GooglePrivacyDlpV2CloudStorageRegexFileSet');
