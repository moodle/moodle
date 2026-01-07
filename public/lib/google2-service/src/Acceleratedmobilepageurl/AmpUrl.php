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

namespace Google\Service\Acceleratedmobilepageurl;

class AmpUrl extends \Google\Model
{
  /**
   * The AMP URL pointing to the publisher's web server.
   *
   * @var string
   */
  public $ampUrl;
  /**
   * The [AMP Cache URL](/amp/cache/overview#amp-cache-url-format) pointing to
   * the cached document in the Google AMP Cache.
   *
   * @var string
   */
  public $cdnAmpUrl;
  /**
   * The original non-AMP URL.
   *
   * @var string
   */
  public $originalUrl;

  /**
   * The AMP URL pointing to the publisher's web server.
   *
   * @param string $ampUrl
   */
  public function setAmpUrl($ampUrl)
  {
    $this->ampUrl = $ampUrl;
  }
  /**
   * @return string
   */
  public function getAmpUrl()
  {
    return $this->ampUrl;
  }
  /**
   * The [AMP Cache URL](/amp/cache/overview#amp-cache-url-format) pointing to
   * the cached document in the Google AMP Cache.
   *
   * @param string $cdnAmpUrl
   */
  public function setCdnAmpUrl($cdnAmpUrl)
  {
    $this->cdnAmpUrl = $cdnAmpUrl;
  }
  /**
   * @return string
   */
  public function getCdnAmpUrl()
  {
    return $this->cdnAmpUrl;
  }
  /**
   * The original non-AMP URL.
   *
   * @param string $originalUrl
   */
  public function setOriginalUrl($originalUrl)
  {
    $this->originalUrl = $originalUrl;
  }
  /**
   * @return string
   */
  public function getOriginalUrl()
  {
    return $this->originalUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AmpUrl::class, 'Google_Service_Acceleratedmobilepageurl_AmpUrl');
