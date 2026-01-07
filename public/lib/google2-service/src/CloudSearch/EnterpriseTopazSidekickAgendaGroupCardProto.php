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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickAgendaGroupCardProto extends \Google\Collection
{
  protected $collection_key = 'agendaItem';
  protected $agendaItemType = EnterpriseTopazSidekickAgendaItem::class;
  protected $agendaItemDataType = 'array';
  protected $contextType = EnterpriseTopazSidekickAgendaGroupCardProtoContext::class;
  protected $contextDataType = '';
  protected $currentAgendaItemType = EnterpriseTopazSidekickAgendaItem::class;
  protected $currentAgendaItemDataType = '';

  /**
   * @param EnterpriseTopazSidekickAgendaItem[] $agendaItem
   */
  public function setAgendaItem($agendaItem)
  {
    $this->agendaItem = $agendaItem;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaItem[]
   */
  public function getAgendaItem()
  {
    return $this->agendaItem;
  }
  /**
   * @param EnterpriseTopazSidekickAgendaGroupCardProtoContext $context
   */
  public function setContext(EnterpriseTopazSidekickAgendaGroupCardProtoContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaGroupCardProtoContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * @param EnterpriseTopazSidekickAgendaItem $currentAgendaItem
   */
  public function setCurrentAgendaItem(EnterpriseTopazSidekickAgendaItem $currentAgendaItem)
  {
    $this->currentAgendaItem = $currentAgendaItem;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaItem
   */
  public function getCurrentAgendaItem()
  {
    return $this->currentAgendaItem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAgendaGroupCardProto::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAgendaGroupCardProto');
