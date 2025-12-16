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

namespace Google\Service\Digitalassetlinks;

class WebAsset extends \Google\Model
{
  /**
   * Web assets are identified by a URL that contains only the scheme, hostname
   * and port parts. The format is http[s]://[:] Hostnames must be fully
   * qualified: they must end in a single period ("`.`"). Only the schemes
   * "http" and "https" are currently allowed. Port numbers are given as a
   * decimal number, and they must be omitted if the standard port numbers are
   * used: 80 for http and 443 for https. We call this limited URL the "site".
   * All URLs that share the same scheme, hostname and port are considered to be
   * a part of the site and thus belong to the web asset. Example: the asset
   * with the site `https://www.google.com` contains all these URLs: *
   * `https://www.google.com/` * `https://www.google.com:443/` *
   * `https://www.google.com/foo` * `https://www.google.com/foo?bar` *
   * `https://www.google.com/foo#bar` * `https://user@password:www.google.com/`
   * But it does not contain these URLs: * `http://www.google.com/` (wrong
   * scheme) * `https://google.com/` (hostname does not match) *
   * `https://www.google.com:444/` (port does not match) REQUIRED
   *
   * @var string
   */
  public $site;

  /**
   * Web assets are identified by a URL that contains only the scheme, hostname
   * and port parts. The format is http[s]://[:] Hostnames must be fully
   * qualified: they must end in a single period ("`.`"). Only the schemes
   * "http" and "https" are currently allowed. Port numbers are given as a
   * decimal number, and they must be omitted if the standard port numbers are
   * used: 80 for http and 443 for https. We call this limited URL the "site".
   * All URLs that share the same scheme, hostname and port are considered to be
   * a part of the site and thus belong to the web asset. Example: the asset
   * with the site `https://www.google.com` contains all these URLs: *
   * `https://www.google.com/` * `https://www.google.com:443/` *
   * `https://www.google.com/foo` * `https://www.google.com/foo?bar` *
   * `https://www.google.com/foo#bar` * `https://user@password:www.google.com/`
   * But it does not contain these URLs: * `http://www.google.com/` (wrong
   * scheme) * `https://google.com/` (hostname does not match) *
   * `https://www.google.com:444/` (port does not match) REQUIRED
   *
   * @param string $site
   */
  public function setSite($site)
  {
    $this->site = $site;
  }
  /**
   * @return string
   */
  public function getSite()
  {
    return $this->site;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WebAsset::class, 'Google_Service_Digitalassetlinks_WebAsset');
