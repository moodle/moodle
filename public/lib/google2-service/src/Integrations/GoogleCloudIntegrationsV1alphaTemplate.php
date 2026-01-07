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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaTemplate extends \Google\Collection
{
  /**
   * Visibility is unspecified
   */
  public const VISIBILITY_VISIBILITY_UNSPECIFIED = 'VISIBILITY_UNSPECIFIED';
  /**
   * Visibility is private
   */
  public const VISIBILITY_PRIVATE = 'PRIVATE';
  /**
   * Visibility is shared
   */
  public const VISIBILITY_SHARED = 'SHARED';
  /**
   * Visibility is public
   */
  public const VISIBILITY_PUBLIC = 'PUBLIC';
  protected $collection_key = 'tags';
  /**
   * Optional. Creator of the template.
   *
   * @var string
   */
  public $author;
  /**
   * Required. Categories associated with the Template. The categories listed
   * below will be utilized for the Template listing.
   *
   * @var string[]
   */
  public $categories;
  protected $componentsType = GoogleCloudIntegrationsV1alphaTemplateComponent::class;
  protected $componentsDataType = 'array';
  /**
   * Output only. Auto-generated.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the template. The length should not be more than
   * 255 characters
   *
   * @var string
   */
  public $description;
  /**
   * Required. The name of the template
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Link to template documentation.
   *
   * @var string
   */
  public $docLink;
  /**
   * Optional. Time the template was last used.
   *
   * @var string
   */
  public $lastUsedTime;
  /**
   * Identifier. Resource name of the template.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Resource names with which the template is shared for example
   * ProjectNumber/Ord id
   *
   * @var string[]
   */
  public $sharedWith;
  /**
   * Required. Tags which are used to identify templates. These tags could be
   * for business use case, connectors etc.
   *
   * @var string[]
   */
  public $tags;
  protected $templateBundleType = GoogleCloudIntegrationsV1alphaTemplateBundle::class;
  protected $templateBundleDataType = '';
  /**
   * Output only. Auto-generated
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. Number of template usages.
   *
   * @var string
   */
  public $usageCount;
  /**
   * Optional. Information on how to use the template. This should contain
   * detailed information about usage of the template.
   *
   * @var string
   */
  public $usageInfo;
  /**
   * Required. Visibility of the template.
   *
   * @var string
   */
  public $visibility;

  /**
   * Optional. Creator of the template.
   *
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }
  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Required. Categories associated with the Template. The categories listed
   * below will be utilized for the Template listing.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Optional. Components being used in the template. This could be used to
   * categorize and filter.
   *
   * @param GoogleCloudIntegrationsV1alphaTemplateComponent[] $components
   */
  public function setComponents($components)
  {
    $this->components = $components;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTemplateComponent[]
   */
  public function getComponents()
  {
    return $this->components;
  }
  /**
   * Output only. Auto-generated.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Description of the template. The length should not be more than
   * 255 characters
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The name of the template
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Link to template documentation.
   *
   * @param string $docLink
   */
  public function setDocLink($docLink)
  {
    $this->docLink = $docLink;
  }
  /**
   * @return string
   */
  public function getDocLink()
  {
    return $this->docLink;
  }
  /**
   * Optional. Time the template was last used.
   *
   * @param string $lastUsedTime
   */
  public function setLastUsedTime($lastUsedTime)
  {
    $this->lastUsedTime = $lastUsedTime;
  }
  /**
   * @return string
   */
  public function getLastUsedTime()
  {
    return $this->lastUsedTime;
  }
  /**
   * Identifier. Resource name of the template.
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
   * Required. Resource names with which the template is shared for example
   * ProjectNumber/Ord id
   *
   * @param string[] $sharedWith
   */
  public function setSharedWith($sharedWith)
  {
    $this->sharedWith = $sharedWith;
  }
  /**
   * @return string[]
   */
  public function getSharedWith()
  {
    return $this->sharedWith;
  }
  /**
   * Required. Tags which are used to identify templates. These tags could be
   * for business use case, connectors etc.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Required. Bundle which is part of the templates. The template entities in
   * the bundle would be converted to an actual entity.
   *
   * @param GoogleCloudIntegrationsV1alphaTemplateBundle $templateBundle
   */
  public function setTemplateBundle(GoogleCloudIntegrationsV1alphaTemplateBundle $templateBundle)
  {
    $this->templateBundle = $templateBundle;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTemplateBundle
   */
  public function getTemplateBundle()
  {
    return $this->templateBundle;
  }
  /**
   * Output only. Auto-generated
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. Number of template usages.
   *
   * @param string $usageCount
   */
  public function setUsageCount($usageCount)
  {
    $this->usageCount = $usageCount;
  }
  /**
   * @return string
   */
  public function getUsageCount()
  {
    return $this->usageCount;
  }
  /**
   * Optional. Information on how to use the template. This should contain
   * detailed information about usage of the template.
   *
   * @param string $usageInfo
   */
  public function setUsageInfo($usageInfo)
  {
    $this->usageInfo = $usageInfo;
  }
  /**
   * @return string
   */
  public function getUsageInfo()
  {
    return $this->usageInfo;
  }
  /**
   * Required. Visibility of the template.
   *
   * Accepted values: VISIBILITY_UNSPECIFIED, PRIVATE, SHARED, PUBLIC
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaTemplate::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTemplate');
