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

namespace Google\Service\Blogger;

class BlogList extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $blogUserInfosType = BlogUserInfo::class;
  protected $blogUserInfosDataType = 'array';
  protected $itemsType = Blog::class;
  protected $itemsDataType = 'array';
  /**
   * The kind of this entity. Always blogger#blogList.
   *
   * @var string
   */
  public $kind;

  /**
   * Admin level list of blog per-user information.
   *
   * @param BlogUserInfo[] $blogUserInfos
   */
  public function setBlogUserInfos($blogUserInfos)
  {
    $this->blogUserInfos = $blogUserInfos;
  }
  /**
   * @return BlogUserInfo[]
   */
  public function getBlogUserInfos()
  {
    return $this->blogUserInfos;
  }
  /**
   * The list of Blogs this user has Authorship or Admin rights over.
   *
   * @param Blog[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Blog[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * The kind of this entity. Always blogger#blogList.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlogList::class, 'Google_Service_Blogger_BlogList');
