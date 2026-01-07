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

namespace Google\Service\AndroidEnterprise;

class StorePage extends \Google\Collection
{
  protected $collection_key = 'name';
  /**
   * Unique ID of this page. Assigned by the server. Immutable once assigned.
   *
   * @var string
   */
  public $id;
  /**
   * Ordered list of pages a user should be able to reach from this page. The
   * list can't include this page. It is recommended that the basic pages are
   * created first, before adding the links between pages. The API doesn't
   * verify that the pages exist or the pages are reachable.
   *
   * @var string[]
   */
  public $link;
  protected $nameType = LocalizedText::class;
  protected $nameDataType = 'array';

  /**
   * Unique ID of this page. Assigned by the server. Immutable once assigned.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Ordered list of pages a user should be able to reach from this page. The
   * list can't include this page. It is recommended that the basic pages are
   * created first, before adding the links between pages. The API doesn't
   * verify that the pages exist or the pages are reachable.
   *
   * @param string[] $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string[]
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * Ordered list of localized strings giving the name of this page. The text
   * displayed is the one that best matches the user locale, or the first entry
   * if there is no good match. There needs to be at least one entry.
   *
   * @param LocalizedText[] $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return LocalizedText[]
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorePage::class, 'Google_Service_AndroidEnterprise_StorePage');
