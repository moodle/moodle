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

class GooglePrivacyDlpV2FileSet extends \Google\Model
{
  protected $regexFileSetType = GooglePrivacyDlpV2CloudStorageRegexFileSet::class;
  protected $regexFileSetDataType = '';
  /**
   * The Cloud Storage url of the file(s) to scan, in the format `gs:`. Trailing
   * wildcard in the path is allowed. If the url ends in a trailing slash, the
   * bucket or directory represented by the url will be scanned non-recursively
   * (content in sub-directories will not be scanned). This means that
   * `gs://mybucket/` is equivalent to `gs://mybucket`, and
   * `gs://mybucket/directory/` is equivalent to `gs://mybucket/directory`.
   * Exactly one of `url` or `regex_file_set` must be set.
   *
   * @var string
   */
  public $url;

  /**
   * The regex-filtered set of files to scan. Exactly one of `url` or
   * `regex_file_set` must be set.
   *
   * @param GooglePrivacyDlpV2CloudStorageRegexFileSet $regexFileSet
   */
  public function setRegexFileSet(GooglePrivacyDlpV2CloudStorageRegexFileSet $regexFileSet)
  {
    $this->regexFileSet = $regexFileSet;
  }
  /**
   * @return GooglePrivacyDlpV2CloudStorageRegexFileSet
   */
  public function getRegexFileSet()
  {
    return $this->regexFileSet;
  }
  /**
   * The Cloud Storage url of the file(s) to scan, in the format `gs:`. Trailing
   * wildcard in the path is allowed. If the url ends in a trailing slash, the
   * bucket or directory represented by the url will be scanned non-recursively
   * (content in sub-directories will not be scanned). This means that
   * `gs://mybucket/` is equivalent to `gs://mybucket`, and
   * `gs://mybucket/directory/` is equivalent to `gs://mybucket/directory`.
   * Exactly one of `url` or `regex_file_set` must be set.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2FileSet::class, 'Google_Service_DLP_GooglePrivacyDlpV2FileSet');
