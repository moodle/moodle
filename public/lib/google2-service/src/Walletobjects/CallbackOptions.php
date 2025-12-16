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

namespace Google\Service\Walletobjects;

class CallbackOptions extends \Google\Model
{
  /**
   * URL for the merchant endpoint that would be called to request updates. The
   * URL should be hosted on HTTPS and robots.txt should allow the URL path to
   * be accessible by UserAgent:Googlebot. Deprecated.
   *
   * @deprecated
   * @var string
   */
  public $updateRequestUrl;
  /**
   * The HTTPS url configured by the merchant. The URL should be hosted on HTTPS
   * and robots.txt should allow the URL path to be accessible by
   * UserAgent:Googlebot.
   *
   * @var string
   */
  public $url;

  /**
   * URL for the merchant endpoint that would be called to request updates. The
   * URL should be hosted on HTTPS and robots.txt should allow the URL path to
   * be accessible by UserAgent:Googlebot. Deprecated.
   *
   * @deprecated
   * @param string $updateRequestUrl
   */
  public function setUpdateRequestUrl($updateRequestUrl)
  {
    $this->updateRequestUrl = $updateRequestUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getUpdateRequestUrl()
  {
    return $this->updateRequestUrl;
  }
  /**
   * The HTTPS url configured by the merchant. The URL should be hosted on HTTPS
   * and robots.txt should allow the URL path to be accessible by
   * UserAgent:Googlebot.
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
class_alias(CallbackOptions::class, 'Google_Service_Walletobjects_CallbackOptions');
