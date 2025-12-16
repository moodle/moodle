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

class DiscoverableProgramMerchantSigninInfo extends \Google\Model
{
  protected $signinWebsiteType = Uri::class;
  protected $signinWebsiteDataType = '';

  /**
   * The URL to direct the user to for the merchant's signin site.
   *
   * @param Uri $signinWebsite
   */
  public function setSigninWebsite(Uri $signinWebsite)
  {
    $this->signinWebsite = $signinWebsite;
  }
  /**
   * @return Uri
   */
  public function getSigninWebsite()
  {
    return $this->signinWebsite;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoverableProgramMerchantSigninInfo::class, 'Google_Service_Walletobjects_DiscoverableProgramMerchantSigninInfo');
