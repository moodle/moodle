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

namespace Google\Service\DisplayVideo;

class EditGuaranteedOrderReadAccessorsRequest extends \Google\Collection
{
  protected $collection_key = 'removedAdvertisers';
  /**
   * The advertisers to add as read accessors to the guaranteed order.
   *
   * @var string[]
   */
  public $addedAdvertisers;
  /**
   * Required. The partner context in which the change is being made.
   *
   * @var string
   */
  public $partnerId;
  /**
   * Whether to give all advertisers of the read/write accessor partner read
   * access to the guaranteed order. Only applicable if read_write_partner_id is
   * set in the guaranteed order.
   *
   * @var bool
   */
  public $readAccessInherited;
  /**
   * The advertisers to remove as read accessors to the guaranteed order.
   *
   * @var string[]
   */
  public $removedAdvertisers;

  /**
   * The advertisers to add as read accessors to the guaranteed order.
   *
   * @param string[] $addedAdvertisers
   */
  public function setAddedAdvertisers($addedAdvertisers)
  {
    $this->addedAdvertisers = $addedAdvertisers;
  }
  /**
   * @return string[]
   */
  public function getAddedAdvertisers()
  {
    return $this->addedAdvertisers;
  }
  /**
   * Required. The partner context in which the change is being made.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
  /**
   * Whether to give all advertisers of the read/write accessor partner read
   * access to the guaranteed order. Only applicable if read_write_partner_id is
   * set in the guaranteed order.
   *
   * @param bool $readAccessInherited
   */
  public function setReadAccessInherited($readAccessInherited)
  {
    $this->readAccessInherited = $readAccessInherited;
  }
  /**
   * @return bool
   */
  public function getReadAccessInherited()
  {
    return $this->readAccessInherited;
  }
  /**
   * The advertisers to remove as read accessors to the guaranteed order.
   *
   * @param string[] $removedAdvertisers
   */
  public function setRemovedAdvertisers($removedAdvertisers)
  {
    $this->removedAdvertisers = $removedAdvertisers;
  }
  /**
   * @return string[]
   */
  public function getRemovedAdvertisers()
  {
    return $this->removedAdvertisers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EditGuaranteedOrderReadAccessorsRequest::class, 'Google_Service_DisplayVideo_EditGuaranteedOrderReadAccessorsRequest');
