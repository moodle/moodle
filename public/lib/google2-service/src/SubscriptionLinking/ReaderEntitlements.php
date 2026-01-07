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

namespace Google\Service\SubscriptionLinking;

class ReaderEntitlements extends \Google\Collection
{
  protected $collection_key = 'entitlements';
  protected $entitlementsType = Entitlement::class;
  protected $entitlementsDataType = 'array';
  /**
   * Output only. The resource name of the singleton.
   *
   * @var string
   */
  public $name;

  /**
   * All of the entitlements for a publication reader.
   *
   * @param Entitlement[] $entitlements
   */
  public function setEntitlements($entitlements)
  {
    $this->entitlements = $entitlements;
  }
  /**
   * @return Entitlement[]
   */
  public function getEntitlements()
  {
    return $this->entitlements;
  }
  /**
   * Output only. The resource name of the singleton.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReaderEntitlements::class, 'Google_Service_SubscriptionLinking_ReaderEntitlements');
