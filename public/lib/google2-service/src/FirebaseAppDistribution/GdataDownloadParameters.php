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

namespace Google\Service\FirebaseAppDistribution;

class GdataDownloadParameters extends \Google\Model
{
  /**
   * A boolean to be returned in the response to Scotty. Allows/disallows gzip
   * encoding of the payload content when the server thinks it's advantageous
   * (hence, does not guarantee compression) which allows Scotty to GZip the
   * response to the client.
   *
   * @var bool
   */
  public $allowGzipCompression;
  /**
   * Determining whether or not Apiary should skip the inclusion of any Content-
   * Range header on its response to Scotty.
   *
   * @var bool
   */
  public $ignoreRange;

  /**
   * A boolean to be returned in the response to Scotty. Allows/disallows gzip
   * encoding of the payload content when the server thinks it's advantageous
   * (hence, does not guarantee compression) which allows Scotty to GZip the
   * response to the client.
   *
   * @param bool $allowGzipCompression
   */
  public function setAllowGzipCompression($allowGzipCompression)
  {
    $this->allowGzipCompression = $allowGzipCompression;
  }
  /**
   * @return bool
   */
  public function getAllowGzipCompression()
  {
    return $this->allowGzipCompression;
  }
  /**
   * Determining whether or not Apiary should skip the inclusion of any Content-
   * Range header on its response to Scotty.
   *
   * @param bool $ignoreRange
   */
  public function setIgnoreRange($ignoreRange)
  {
    $this->ignoreRange = $ignoreRange;
  }
  /**
   * @return bool
   */
  public function getIgnoreRange()
  {
    return $this->ignoreRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GdataDownloadParameters::class, 'Google_Service_FirebaseAppDistribution_GdataDownloadParameters');
