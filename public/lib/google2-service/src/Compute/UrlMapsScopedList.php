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

namespace Google\Service\Compute;

class UrlMapsScopedList extends \Google\Collection
{
  protected $collection_key = 'urlMaps';
  protected $urlMapsType = UrlMap::class;
  protected $urlMapsDataType = 'array';
  protected $warningType = UrlMapsScopedListWarning::class;
  protected $warningDataType = '';

  /**
   * A list of UrlMaps contained in this scope.
   *
   * @param UrlMap[] $urlMaps
   */
  public function setUrlMaps($urlMaps)
  {
    $this->urlMaps = $urlMaps;
  }
  /**
   * @return UrlMap[]
   */
  public function getUrlMaps()
  {
    return $this->urlMaps;
  }
  /**
   * Informational warning which replaces the list of backend services when the
   * list is empty.
   *
   * @param UrlMapsScopedListWarning $warning
   */
  public function setWarning(UrlMapsScopedListWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return UrlMapsScopedListWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlMapsScopedList::class, 'Google_Service_Compute_UrlMapsScopedList');
