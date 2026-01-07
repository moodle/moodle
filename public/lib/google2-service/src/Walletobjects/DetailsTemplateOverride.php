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

class DetailsTemplateOverride extends \Google\Collection
{
  protected $collection_key = 'detailsItemInfos';
  protected $detailsItemInfosType = DetailsItemInfo::class;
  protected $detailsItemInfosDataType = 'array';

  /**
   * Information for the "nth" item displayed in the details list.
   *
   * @param DetailsItemInfo[] $detailsItemInfos
   */
  public function setDetailsItemInfos($detailsItemInfos)
  {
    $this->detailsItemInfos = $detailsItemInfos;
  }
  /**
   * @return DetailsItemInfo[]
   */
  public function getDetailsItemInfos()
  {
    return $this->detailsItemInfos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DetailsTemplateOverride::class, 'Google_Service_Walletobjects_DetailsTemplateOverride');
