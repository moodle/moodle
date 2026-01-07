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

namespace Google\Service\YouTube;

class ChannelAuditDetails extends \Google\Model
{
  /**
   * Whether or not the channel respects the community guidelines.
   *
   * @var bool
   */
  public $communityGuidelinesGoodStanding;
  /**
   * Whether or not the channel has any unresolved claims.
   *
   * @var bool
   */
  public $contentIdClaimsGoodStanding;
  /**
   * Whether or not the channel has any copyright strikes.
   *
   * @var bool
   */
  public $copyrightStrikesGoodStanding;

  /**
   * Whether or not the channel respects the community guidelines.
   *
   * @param bool $communityGuidelinesGoodStanding
   */
  public function setCommunityGuidelinesGoodStanding($communityGuidelinesGoodStanding)
  {
    $this->communityGuidelinesGoodStanding = $communityGuidelinesGoodStanding;
  }
  /**
   * @return bool
   */
  public function getCommunityGuidelinesGoodStanding()
  {
    return $this->communityGuidelinesGoodStanding;
  }
  /**
   * Whether or not the channel has any unresolved claims.
   *
   * @param bool $contentIdClaimsGoodStanding
   */
  public function setContentIdClaimsGoodStanding($contentIdClaimsGoodStanding)
  {
    $this->contentIdClaimsGoodStanding = $contentIdClaimsGoodStanding;
  }
  /**
   * @return bool
   */
  public function getContentIdClaimsGoodStanding()
  {
    return $this->contentIdClaimsGoodStanding;
  }
  /**
   * Whether or not the channel has any copyright strikes.
   *
   * @param bool $copyrightStrikesGoodStanding
   */
  public function setCopyrightStrikesGoodStanding($copyrightStrikesGoodStanding)
  {
    $this->copyrightStrikesGoodStanding = $copyrightStrikesGoodStanding;
  }
  /**
   * @return bool
   */
  public function getCopyrightStrikesGoodStanding()
  {
    return $this->copyrightStrikesGoodStanding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelAuditDetails::class, 'Google_Service_YouTube_ChannelAuditDetails');
