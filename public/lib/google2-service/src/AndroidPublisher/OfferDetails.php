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

namespace Google\Service\AndroidPublisher;

class OfferDetails extends \Google\Collection
{
  protected $collection_key = 'offerTags';
  /**
   * The base plan ID. Present for all base plan and offers.
   *
   * @var string
   */
  public $basePlanId;
  /**
   * The offer ID. Only present for discounted offers.
   *
   * @var string
   */
  public $offerId;
  /**
   * The latest offer tags associated with the offer. It includes tags inherited
   * from the base plan.
   *
   * @var string[]
   */
  public $offerTags;

  /**
   * The base plan ID. Present for all base plan and offers.
   *
   * @param string $basePlanId
   */
  public function setBasePlanId($basePlanId)
  {
    $this->basePlanId = $basePlanId;
  }
  /**
   * @return string
   */
  public function getBasePlanId()
  {
    return $this->basePlanId;
  }
  /**
   * The offer ID. Only present for discounted offers.
   *
   * @param string $offerId
   */
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  /**
   * @return string
   */
  public function getOfferId()
  {
    return $this->offerId;
  }
  /**
   * The latest offer tags associated with the offer. It includes tags inherited
   * from the base plan.
   *
   * @param string[] $offerTags
   */
  public function setOfferTags($offerTags)
  {
    $this->offerTags = $offerTags;
  }
  /**
   * @return string[]
   */
  public function getOfferTags()
  {
    return $this->offerTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OfferDetails::class, 'Google_Service_AndroidPublisher_OfferDetails');
