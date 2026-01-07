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

class CardTemplateOverride extends \Google\Collection
{
  protected $collection_key = 'cardRowTemplateInfos';
  protected $cardRowTemplateInfosType = CardRowTemplateInfo::class;
  protected $cardRowTemplateInfosDataType = 'array';

  /**
   * Template information for rows in the card view. At most three rows are
   * allowed to be specified.
   *
   * @param CardRowTemplateInfo[] $cardRowTemplateInfos
   */
  public function setCardRowTemplateInfos($cardRowTemplateInfos)
  {
    $this->cardRowTemplateInfos = $cardRowTemplateInfos;
  }
  /**
   * @return CardRowTemplateInfo[]
   */
  public function getCardRowTemplateInfos()
  {
    return $this->cardRowTemplateInfos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CardTemplateOverride::class, 'Google_Service_Walletobjects_CardTemplateOverride');
