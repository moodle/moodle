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

namespace Google\Service\PaymentsResellerSubscription;

class GoogleHomePayload extends \Google\Model
{
  /**
   * Output only. This identifies whether the subscription is attached to a
   * Google Home structure.
   *
   * @var bool
   */
  public $attachedToGoogleStructure;
  /**
   * Optional. Structure identifier on Google side.
   *
   * @var string
   */
  public $googleStructureId;
  /**
   * Optional. This identifies the structure ID on partner side that the
   * subscription should be applied to. Only required when the partner requires
   * structure mapping.
   *
   * @var string
   */
  public $partnerStructureId;

  /**
   * Output only. This identifies whether the subscription is attached to a
   * Google Home structure.
   *
   * @param bool $attachedToGoogleStructure
   */
  public function setAttachedToGoogleStructure($attachedToGoogleStructure)
  {
    $this->attachedToGoogleStructure = $attachedToGoogleStructure;
  }
  /**
   * @return bool
   */
  public function getAttachedToGoogleStructure()
  {
    return $this->attachedToGoogleStructure;
  }
  /**
   * Optional. Structure identifier on Google side.
   *
   * @param string $googleStructureId
   */
  public function setGoogleStructureId($googleStructureId)
  {
    $this->googleStructureId = $googleStructureId;
  }
  /**
   * @return string
   */
  public function getGoogleStructureId()
  {
    return $this->googleStructureId;
  }
  /**
   * Optional. This identifies the structure ID on partner side that the
   * subscription should be applied to. Only required when the partner requires
   * structure mapping.
   *
   * @param string $partnerStructureId
   */
  public function setPartnerStructureId($partnerStructureId)
  {
    $this->partnerStructureId = $partnerStructureId;
  }
  /**
   * @return string
   */
  public function getPartnerStructureId()
  {
    return $this->partnerStructureId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleHomePayload::class, 'Google_Service_PaymentsResellerSubscription_GoogleHomePayload');
