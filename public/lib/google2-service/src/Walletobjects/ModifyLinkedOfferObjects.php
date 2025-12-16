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

class ModifyLinkedOfferObjects extends \Google\Collection
{
  protected $collection_key = 'removeLinkedOfferObjectIds';
  /**
   * The linked offer object ids to add to the object.
   *
   * @var string[]
   */
  public $addLinkedOfferObjectIds;
  /**
   * The linked offer object ids to remove from the object.
   *
   * @var string[]
   */
  public $removeLinkedOfferObjectIds;

  /**
   * The linked offer object ids to add to the object.
   *
   * @param string[] $addLinkedOfferObjectIds
   */
  public function setAddLinkedOfferObjectIds($addLinkedOfferObjectIds)
  {
    $this->addLinkedOfferObjectIds = $addLinkedOfferObjectIds;
  }
  /**
   * @return string[]
   */
  public function getAddLinkedOfferObjectIds()
  {
    return $this->addLinkedOfferObjectIds;
  }
  /**
   * The linked offer object ids to remove from the object.
   *
   * @param string[] $removeLinkedOfferObjectIds
   */
  public function setRemoveLinkedOfferObjectIds($removeLinkedOfferObjectIds)
  {
    $this->removeLinkedOfferObjectIds = $removeLinkedOfferObjectIds;
  }
  /**
   * @return string[]
   */
  public function getRemoveLinkedOfferObjectIds()
  {
    return $this->removeLinkedOfferObjectIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModifyLinkedOfferObjects::class, 'Google_Service_Walletobjects_ModifyLinkedOfferObjects');
