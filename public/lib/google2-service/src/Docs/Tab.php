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

namespace Google\Service\Docs;

class Tab extends \Google\Collection
{
  protected $collection_key = 'childTabs';
  protected $childTabsType = Tab::class;
  protected $childTabsDataType = 'array';
  protected $documentTabType = DocumentTab::class;
  protected $documentTabDataType = '';
  protected $tabPropertiesType = TabProperties::class;
  protected $tabPropertiesDataType = '';

  /**
   * The child tabs nested within this tab.
   *
   * @param Tab[] $childTabs
   */
  public function setChildTabs($childTabs)
  {
    $this->childTabs = $childTabs;
  }
  /**
   * @return Tab[]
   */
  public function getChildTabs()
  {
    return $this->childTabs;
  }
  /**
   * A tab with document contents, like text and images.
   *
   * @param DocumentTab $documentTab
   */
  public function setDocumentTab(DocumentTab $documentTab)
  {
    $this->documentTab = $documentTab;
  }
  /**
   * @return DocumentTab
   */
  public function getDocumentTab()
  {
    return $this->documentTab;
  }
  /**
   * The properties of the tab, like ID and title.
   *
   * @param TabProperties $tabProperties
   */
  public function setTabProperties(TabProperties $tabProperties)
  {
    $this->tabProperties = $tabProperties;
  }
  /**
   * @return TabProperties
   */
  public function getTabProperties()
  {
    return $this->tabProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tab::class, 'Google_Service_Docs_Tab');
