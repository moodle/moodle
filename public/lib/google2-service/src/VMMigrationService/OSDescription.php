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

namespace Google\Service\VMMigrationService;

class OSDescription extends \Google\Model
{
  /**
   * OS offer.
   *
   * @var string
   */
  public $offer;
  /**
   * OS plan.
   *
   * @var string
   */
  public $plan;
  /**
   * OS publisher.
   *
   * @var string
   */
  public $publisher;
  /**
   * OS type.
   *
   * @var string
   */
  public $type;

  /**
   * OS offer.
   *
   * @param string $offer
   */
  public function setOffer($offer)
  {
    $this->offer = $offer;
  }
  /**
   * @return string
   */
  public function getOffer()
  {
    return $this->offer;
  }
  /**
   * OS plan.
   *
   * @param string $plan
   */
  public function setPlan($plan)
  {
    $this->plan = $plan;
  }
  /**
   * @return string
   */
  public function getPlan()
  {
    return $this->plan;
  }
  /**
   * OS publisher.
   *
   * @param string $publisher
   */
  public function setPublisher($publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return string
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
  /**
   * OS type.
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
class_alias(OSDescription::class, 'Google_Service_VMMigrationService_OSDescription');
