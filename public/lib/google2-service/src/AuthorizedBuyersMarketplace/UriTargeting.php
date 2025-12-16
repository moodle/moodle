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

namespace Google\Service\AuthorizedBuyersMarketplace;

class UriTargeting extends \Google\Collection
{
  protected $collection_key = 'targetedUris';
  /**
   * A list of URLs to be excluded.
   *
   * @var string[]
   */
  public $excludedUris;
  /**
   * A list of URLs to be included.
   *
   * @var string[]
   */
  public $targetedUris;

  /**
   * A list of URLs to be excluded.
   *
   * @param string[] $excludedUris
   */
  public function setExcludedUris($excludedUris)
  {
    $this->excludedUris = $excludedUris;
  }
  /**
   * @return string[]
   */
  public function getExcludedUris()
  {
    return $this->excludedUris;
  }
  /**
   * A list of URLs to be included.
   *
   * @param string[] $targetedUris
   */
  public function setTargetedUris($targetedUris)
  {
    $this->targetedUris = $targetedUris;
  }
  /**
   * @return string[]
   */
  public function getTargetedUris()
  {
    return $this->targetedUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UriTargeting::class, 'Google_Service_AuthorizedBuyersMarketplace_UriTargeting');
