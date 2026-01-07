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

class DiscoverableProgramMerchantSignupInfo extends \Google\Collection
{
  protected $collection_key = 'signupSharedDatas';
  /**
   * User data that is sent in a POST request to the signup website URL. This
   * information is encoded and then shared so that the merchant's website can
   * prefill fields used to enroll the user for the discoverable program.
   *
   * @var string[]
   */
  public $signupSharedDatas;
  protected $signupWebsiteType = Uri::class;
  protected $signupWebsiteDataType = '';

  /**
   * User data that is sent in a POST request to the signup website URL. This
   * information is encoded and then shared so that the merchant's website can
   * prefill fields used to enroll the user for the discoverable program.
   *
   * @param string[] $signupSharedDatas
   */
  public function setSignupSharedDatas($signupSharedDatas)
  {
    $this->signupSharedDatas = $signupSharedDatas;
  }
  /**
   * @return string[]
   */
  public function getSignupSharedDatas()
  {
    return $this->signupSharedDatas;
  }
  /**
   * The URL to direct the user to for the merchant's signup site.
   *
   * @param Uri $signupWebsite
   */
  public function setSignupWebsite(Uri $signupWebsite)
  {
    $this->signupWebsite = $signupWebsite;
  }
  /**
   * @return Uri
   */
  public function getSignupWebsite()
  {
    return $this->signupWebsite;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoverableProgramMerchantSignupInfo::class, 'Google_Service_Walletobjects_DiscoverableProgramMerchantSignupInfo');
