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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ApiDoc extends \Google\Collection
{
  protected $collection_key = 'categoryIds';
  /**
   * Optional. Boolean flag that manages user access to the catalog item. When
   * true, the catalog item has public visibility and can be viewed anonymously;
   * otherwise, only registered users may view it. Note: when the parent portal
   * is enrolled in the [audience management
   * feature](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/portal-
   * audience#enrolling_in_the_beta_release_of_the_audience_management_feature),
   * and this flag is set to false, visibility is set to an indeterminate state
   * and must be explicitly specified in the management UI (see [Manage the
   * visibility of an API in your
   * portal](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/publish-apis#visibility)). Additionally, when
   * enrolled in the audience management feature, updates to this flag will be
   * ignored as visibility permissions must be updated in the management UI.
   *
   * @var bool
   */
  public $anonAllowed;
  /**
   * Required. Immutable. The `name` field of the associated [API product](/apig
   * ee/docs/reference/apis/apigee/rest/v1/organizations.apiproducts). A portal
   * may have only one catalog item associated with a given API product.
   *
   * @var string
   */
  public $apiProductName;
  /**
   * Optional. The IDs of the API categories to which this catalog item belongs.
   *
   * @var string[]
   */
  public $categoryIds;
  /**
   * Optional. Description of the catalog item. Max length is 10,000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Immutable. DEPRECATED: use the `apiProductName` field instead
   *
   * @var string
   */
  public $edgeAPIProductName;
  /**
   * Optional. DEPRECATED: manage documentation through the `getDocumentation`
   * and `updateDocumentation` methods
   *
   * @deprecated
   * @var string
   */
  public $graphqlEndpointUrl;
  /**
   * Optional. DEPRECATED: manage documentation through the `getDocumentation`
   * and `updateDocumentation` methods
   *
   * @deprecated
   * @var string
   */
  public $graphqlSchema;
  /**
   * Optional. DEPRECATED: manage documentation through the `getDocumentation`
   * and `updateDocumentation` methods
   *
   * @deprecated
   * @var string
   */
  public $graphqlSchemaDisplayName;
  /**
   * Output only. The ID of the catalog item.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Location of the image used for the catalog item in the catalog.
   * This can be either an image with an external URL or a file path for [image
   * files stored in the portal](/apigee/docs/api-
   * platform/publish/portal/portal-files"), for example, `/files/book-
   * tree.jpg`. When specifying the URL of an external image, the image won't be
   * uploaded to your assets; additionally, loading the image in the integrated
   * portal will be subject to its availability, which may be blocked or
   * restricted by [content security policies](/apigee/docs/api-
   * platform/publish/portal/csp). Max length of file path is 2,083 characters.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * Output only. Time the catalog item was last modified in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $modified;
  /**
   * Optional. Denotes whether the catalog item is published to the portal or is
   * in a draft state. When the parent portal is enrolled in the [audience
   * management feature](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/portal-
   * audience#enrolling_in_the_beta_release_of_the_audience_management_feature),
   * the visibility can be set to public on creation by setting the anonAllowed
   * flag to true or further managed in the management UI (see [Manage the
   * visibility of an API in your
   * portal](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/publish-apis#visibility)) before it can be visible
   * to any users. If not enrolled in the audience management feature, the
   * visibility is managed by the `anonAllowed` flag.
   *
   * @var bool
   */
  public $published;
  /**
   * Optional. Whether a callback URL is required when this catalog item's API
   * product is enabled in a developer app. When true, a portal user will be
   * required to input a URL when managing the app (this is typically used for
   * the app's OAuth flow).
   *
   * @var bool
   */
  public $requireCallbackUrl;
  /**
   * Output only. The ID of the parent portal.
   *
   * @var string
   */
  public $siteId;
  /**
   * Optional. DEPRECATED: DO NOT USE
   *
   * @deprecated
   * @var string
   */
  public $specId;
  /**
   * Required. The user-facing name of the catalog item. `title` must be a non-
   * empty string with a max length of 255 characters.
   *
   * @var string
   */
  public $title;
  /**
   * Optional. DEPRECATED: use the `published` field instead
   *
   * @var bool
   */
  public $visibility;

  /**
   * Optional. Boolean flag that manages user access to the catalog item. When
   * true, the catalog item has public visibility and can be viewed anonymously;
   * otherwise, only registered users may view it. Note: when the parent portal
   * is enrolled in the [audience management
   * feature](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/portal-
   * audience#enrolling_in_the_beta_release_of_the_audience_management_feature),
   * and this flag is set to false, visibility is set to an indeterminate state
   * and must be explicitly specified in the management UI (see [Manage the
   * visibility of an API in your
   * portal](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/publish-apis#visibility)). Additionally, when
   * enrolled in the audience management feature, updates to this flag will be
   * ignored as visibility permissions must be updated in the management UI.
   *
   * @param bool $anonAllowed
   */
  public function setAnonAllowed($anonAllowed)
  {
    $this->anonAllowed = $anonAllowed;
  }
  /**
   * @return bool
   */
  public function getAnonAllowed()
  {
    return $this->anonAllowed;
  }
  /**
   * Required. Immutable. The `name` field of the associated [API product](/apig
   * ee/docs/reference/apis/apigee/rest/v1/organizations.apiproducts). A portal
   * may have only one catalog item associated with a given API product.
   *
   * @param string $apiProductName
   */
  public function setApiProductName($apiProductName)
  {
    $this->apiProductName = $apiProductName;
  }
  /**
   * @return string
   */
  public function getApiProductName()
  {
    return $this->apiProductName;
  }
  /**
   * Optional. The IDs of the API categories to which this catalog item belongs.
   *
   * @param string[] $categoryIds
   */
  public function setCategoryIds($categoryIds)
  {
    $this->categoryIds = $categoryIds;
  }
  /**
   * @return string[]
   */
  public function getCategoryIds()
  {
    return $this->categoryIds;
  }
  /**
   * Optional. Description of the catalog item. Max length is 10,000 characters.
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
   * Optional. Immutable. DEPRECATED: use the `apiProductName` field instead
   *
   * @param string $edgeAPIProductName
   */
  public function setEdgeAPIProductName($edgeAPIProductName)
  {
    $this->edgeAPIProductName = $edgeAPIProductName;
  }
  /**
   * @return string
   */
  public function getEdgeAPIProductName()
  {
    return $this->edgeAPIProductName;
  }
  /**
   * Optional. DEPRECATED: manage documentation through the `getDocumentation`
   * and `updateDocumentation` methods
   *
   * @deprecated
   * @param string $graphqlEndpointUrl
   */
  public function setGraphqlEndpointUrl($graphqlEndpointUrl)
  {
    $this->graphqlEndpointUrl = $graphqlEndpointUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGraphqlEndpointUrl()
  {
    return $this->graphqlEndpointUrl;
  }
  /**
   * Optional. DEPRECATED: manage documentation through the `getDocumentation`
   * and `updateDocumentation` methods
   *
   * @deprecated
   * @param string $graphqlSchema
   */
  public function setGraphqlSchema($graphqlSchema)
  {
    $this->graphqlSchema = $graphqlSchema;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGraphqlSchema()
  {
    return $this->graphqlSchema;
  }
  /**
   * Optional. DEPRECATED: manage documentation through the `getDocumentation`
   * and `updateDocumentation` methods
   *
   * @deprecated
   * @param string $graphqlSchemaDisplayName
   */
  public function setGraphqlSchemaDisplayName($graphqlSchemaDisplayName)
  {
    $this->graphqlSchemaDisplayName = $graphqlSchemaDisplayName;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGraphqlSchemaDisplayName()
  {
    return $this->graphqlSchemaDisplayName;
  }
  /**
   * Output only. The ID of the catalog item.
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
   * Optional. Location of the image used for the catalog item in the catalog.
   * This can be either an image with an external URL or a file path for [image
   * files stored in the portal](/apigee/docs/api-
   * platform/publish/portal/portal-files"), for example, `/files/book-
   * tree.jpg`. When specifying the URL of an external image, the image won't be
   * uploaded to your assets; additionally, loading the image in the integrated
   * portal will be subject to its availability, which may be blocked or
   * restricted by [content security policies](/apigee/docs/api-
   * platform/publish/portal/csp). Max length of file path is 2,083 characters.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * Output only. Time the catalog item was last modified in milliseconds since
   * epoch.
   *
   * @param string $modified
   */
  public function setModified($modified)
  {
    $this->modified = $modified;
  }
  /**
   * @return string
   */
  public function getModified()
  {
    return $this->modified;
  }
  /**
   * Optional. Denotes whether the catalog item is published to the portal or is
   * in a draft state. When the parent portal is enrolled in the [audience
   * management feature](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/portal-
   * audience#enrolling_in_the_beta_release_of_the_audience_management_feature),
   * the visibility can be set to public on creation by setting the anonAllowed
   * flag to true or further managed in the management UI (see [Manage the
   * visibility of an API in your
   * portal](https://cloud.google.com/apigee/docs/api-
   * platform/publish/portal/publish-apis#visibility)) before it can be visible
   * to any users. If not enrolled in the audience management feature, the
   * visibility is managed by the `anonAllowed` flag.
   *
   * @param bool $published
   */
  public function setPublished($published)
  {
    $this->published = $published;
  }
  /**
   * @return bool
   */
  public function getPublished()
  {
    return $this->published;
  }
  /**
   * Optional. Whether a callback URL is required when this catalog item's API
   * product is enabled in a developer app. When true, a portal user will be
   * required to input a URL when managing the app (this is typically used for
   * the app's OAuth flow).
   *
   * @param bool $requireCallbackUrl
   */
  public function setRequireCallbackUrl($requireCallbackUrl)
  {
    $this->requireCallbackUrl = $requireCallbackUrl;
  }
  /**
   * @return bool
   */
  public function getRequireCallbackUrl()
  {
    return $this->requireCallbackUrl;
  }
  /**
   * Output only. The ID of the parent portal.
   *
   * @param string $siteId
   */
  public function setSiteId($siteId)
  {
    $this->siteId = $siteId;
  }
  /**
   * @return string
   */
  public function getSiteId()
  {
    return $this->siteId;
  }
  /**
   * Optional. DEPRECATED: DO NOT USE
   *
   * @deprecated
   * @param string $specId
   */
  public function setSpecId($specId)
  {
    $this->specId = $specId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getSpecId()
  {
    return $this->specId;
  }
  /**
   * Required. The user-facing name of the catalog item. `title` must be a non-
   * empty string with a max length of 255 characters.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Optional. DEPRECATED: use the `published` field instead
   *
   * @param bool $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return bool
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ApiDoc::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ApiDoc');
