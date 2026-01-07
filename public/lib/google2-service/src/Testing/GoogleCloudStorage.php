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

namespace Google\Service\Testing;

class GoogleCloudStorage extends \Google\Model
{
  /**
   * Required. The path to a directory in GCS that will eventually contain the
   * results for this test. The requesting user must have write access on the
   * bucket in the supplied path.
   *
   * @var string
   */
  public $gcsPath;

  /**
   * Required. The path to a directory in GCS that will eventually contain the
   * results for this test. The requesting user must have write access on the
   * bucket in the supplied path.
   *
   * @param string $gcsPath
   */
  public function setGcsPath($gcsPath)
  {
    $this->gcsPath = $gcsPath;
  }
  /**
   * @return string
   */
  public function getGcsPath()
  {
    return $this->gcsPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudStorage::class, 'Google_Service_Testing_GoogleCloudStorage');
