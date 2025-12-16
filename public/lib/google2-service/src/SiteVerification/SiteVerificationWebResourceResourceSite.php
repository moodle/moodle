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

namespace Google\Service\SiteVerification;

class SiteVerificationWebResourceResourceSite extends \Google\Model
{
  /**
   * The site identifier. If the type is set to SITE, the identifier is a URL.
   * If the type is set to INET_DOMAIN, the site identifier is a domain name.
   *
   * @var string
   */
  public $identifier;
  /**
   * The site type. Can be SITE or INET_DOMAIN (domain name).
   *
   * @var string
   */
  public $type;

  /**
   * The site identifier. If the type is set to SITE, the identifier is a URL.
   * If the type is set to INET_DOMAIN, the site identifier is a domain name.
   *
   * @param string $identifier
   */
  public function setIdentifier($identifier)
  {
    $this->identifier = $identifier;
  }
  /**
   * @return string
   */
  public function getIdentifier()
  {
    return $this->identifier;
  }
  /**
   * The site type. Can be SITE or INET_DOMAIN (domain name).
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SiteVerificationWebResourceResourceSite::class, 'Google_Service_SiteVerification_SiteVerificationWebResourceResourceSite');
