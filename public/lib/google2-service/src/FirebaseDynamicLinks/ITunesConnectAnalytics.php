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

namespace Google\Service\FirebaseDynamicLinks;

class ITunesConnectAnalytics extends \Google\Model
{
  /**
   * Affiliate token used to create affiliate-coded links.
   *
   * @var string
   */
  public $at;
  /**
   * Campaign text that developers can optionally add to any link in order to
   * track sales from a specific marketing campaign.
   *
   * @var string
   */
  public $ct;
  /**
   * iTune media types, including music, podcasts, audiobooks and so on.
   *
   * @var string
   */
  public $mt;
  /**
   * Provider token that enables analytics for Dynamic Links from within iTunes
   * Connect.
   *
   * @var string
   */
  public $pt;

  /**
   * Affiliate token used to create affiliate-coded links.
   *
   * @param string $at
   */
  public function setAt($at)
  {
    $this->at = $at;
  }
  /**
   * @return string
   */
  public function getAt()
  {
    return $this->at;
  }
  /**
   * Campaign text that developers can optionally add to any link in order to
   * track sales from a specific marketing campaign.
   *
   * @param string $ct
   */
  public function setCt($ct)
  {
    $this->ct = $ct;
  }
  /**
   * @return string
   */
  public function getCt()
  {
    return $this->ct;
  }
  /**
   * iTune media types, including music, podcasts, audiobooks and so on.
   *
   * @param string $mt
   */
  public function setMt($mt)
  {
    $this->mt = $mt;
  }
  /**
   * @return string
   */
  public function getMt()
  {
    return $this->mt;
  }
  /**
   * Provider token that enables analytics for Dynamic Links from within iTunes
   * Connect.
   *
   * @param string $pt
   */
  public function setPt($pt)
  {
    $this->pt = $pt;
  }
  /**
   * @return string
   */
  public function getPt()
  {
    return $this->pt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ITunesConnectAnalytics::class, 'Google_Service_FirebaseDynamicLinks_ITunesConnectAnalytics');
