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

namespace Google\Service\ServiceUsage;

class Page extends \Google\Collection
{
  protected $collection_key = 'subpages';
  /**
   * The Markdown content of the page. You can use ```(== include {path} ==)```
   * to include content from a Markdown file. The content can be used to produce
   * the documentation page such as HTML format page.
   *
   * @var string
   */
  public $content;
  /**
   * The name of the page. It will be used as an identity of the page to
   * generate URI of the page, text of the link to this page in navigation, etc.
   * The full page name (start from the root page name to this page concatenated
   * with `.`) can be used as reference to the page in your documentation. For
   * example: pages: - name: Tutorial content: (== include tutorial.md ==)
   * subpages: - name: Java content: (== include tutorial_java.md ==) You can
   * reference `Java` page using Markdown reference link syntax: `Java`.
   *
   * @var string
   */
  public $name;
  protected $subpagesType = Page::class;
  protected $subpagesDataType = 'array';

  /**
   * The Markdown content of the page. You can use ```(== include {path} ==)```
   * to include content from a Markdown file. The content can be used to produce
   * the documentation page such as HTML format page.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The name of the page. It will be used as an identity of the page to
   * generate URI of the page, text of the link to this page in navigation, etc.
   * The full page name (start from the root page name to this page concatenated
   * with `.`) can be used as reference to the page in your documentation. For
   * example: pages: - name: Tutorial content: (== include tutorial.md ==)
   * subpages: - name: Java content: (== include tutorial_java.md ==) You can
   * reference `Java` page using Markdown reference link syntax: `Java`.
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
  /**
   * Subpages of this page. The order of subpages specified here will be honored
   * in the generated docset.
   *
   * @param Page[] $subpages
   */
  public function setSubpages($subpages)
  {
    $this->subpages = $subpages;
  }
  /**
   * @return Page[]
   */
  public function getSubpages()
  {
    return $this->subpages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Page::class, 'Google_Service_ServiceUsage_Page');
