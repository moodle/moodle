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

namespace Google\Service\ServiceConsumerManagement;

class Documentation extends \Google\Collection
{
  protected $collection_key = 'sectionOverrides';
  /**
   * Optional information about the IAM configuration. This is typically used to
   * link to documentation about a product's IAM roles and permissions.
   *
   * @var string
   */
  public $additionalIamInfo;
  /**
   * The URL to the root of documentation.
   *
   * @var string
   */
  public $documentationRootUrl;
  /**
   * Declares a single overview page. For example: documentation: summary: ...
   * overview: (== include overview.md ==) This is a shortcut for the following
   * declaration (using pages style): documentation: summary: ... pages: - name:
   * Overview content: (== include overview.md ==) Note: you cannot specify both
   * `overview` field and `pages` field.
   *
   * @var string
   */
  public $overview;
  protected $pagesType = Page::class;
  protected $pagesDataType = 'array';
  protected $rulesType = DocumentationRule::class;
  protected $rulesDataType = 'array';
  protected $sectionOverridesType = Page::class;
  protected $sectionOverridesDataType = 'array';
  /**
   * Specifies the service root url if the default one (the service name from
   * the yaml file) is not suitable. This can be seen in any fully specified
   * service urls as well as sections that show a base that other urls are
   * relative to.
   *
   * @var string
   */
  public $serviceRootUrl;
  /**
   * A short description of what the service does. The summary must be plain
   * text. It becomes the overview of the service displayed in Google Cloud
   * Console. NOTE: This field is equivalent to the standard field
   * `description`.
   *
   * @var string
   */
  public $summary;

  /**
   * Optional information about the IAM configuration. This is typically used to
   * link to documentation about a product's IAM roles and permissions.
   *
   * @param string $additionalIamInfo
   */
  public function setAdditionalIamInfo($additionalIamInfo)
  {
    $this->additionalIamInfo = $additionalIamInfo;
  }
  /**
   * @return string
   */
  public function getAdditionalIamInfo()
  {
    return $this->additionalIamInfo;
  }
  /**
   * The URL to the root of documentation.
   *
   * @param string $documentationRootUrl
   */
  public function setDocumentationRootUrl($documentationRootUrl)
  {
    $this->documentationRootUrl = $documentationRootUrl;
  }
  /**
   * @return string
   */
  public function getDocumentationRootUrl()
  {
    return $this->documentationRootUrl;
  }
  /**
   * Declares a single overview page. For example: documentation: summary: ...
   * overview: (== include overview.md ==) This is a shortcut for the following
   * declaration (using pages style): documentation: summary: ... pages: - name:
   * Overview content: (== include overview.md ==) Note: you cannot specify both
   * `overview` field and `pages` field.
   *
   * @param string $overview
   */
  public function setOverview($overview)
  {
    $this->overview = $overview;
  }
  /**
   * @return string
   */
  public function getOverview()
  {
    return $this->overview;
  }
  /**
   * The top level pages for the documentation set.
   *
   * @param Page[] $pages
   */
  public function setPages($pages)
  {
    $this->pages = $pages;
  }
  /**
   * @return Page[]
   */
  public function getPages()
  {
    return $this->pages;
  }
  /**
   * A list of documentation rules that apply to individual API elements.
   * **NOTE:** All service configuration rules follow "last one wins" order.
   *
   * @param DocumentationRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return DocumentationRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Specifies section and content to override the boilerplate content.
   * Currently overrides following sections: 1. rest.service.client_libraries
   *
   * @param Page[] $sectionOverrides
   */
  public function setSectionOverrides($sectionOverrides)
  {
    $this->sectionOverrides = $sectionOverrides;
  }
  /**
   * @return Page[]
   */
  public function getSectionOverrides()
  {
    return $this->sectionOverrides;
  }
  /**
   * Specifies the service root url if the default one (the service name from
   * the yaml file) is not suitable. This can be seen in any fully specified
   * service urls as well as sections that show a base that other urls are
   * relative to.
   *
   * @param string $serviceRootUrl
   */
  public function setServiceRootUrl($serviceRootUrl)
  {
    $this->serviceRootUrl = $serviceRootUrl;
  }
  /**
   * @return string
   */
  public function getServiceRootUrl()
  {
    return $this->serviceRootUrl;
  }
  /**
   * A short description of what the service does. The summary must be plain
   * text. It becomes the overview of the service displayed in Google Cloud
   * Console. NOTE: This field is equivalent to the standard field
   * `description`.
   *
   * @param string $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return string
   */
  public function getSummary()
  {
    return $this->summary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Documentation::class, 'Google_Service_ServiceConsumerManagement_Documentation');
