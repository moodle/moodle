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

namespace Google\Service\Recommender;

class GoogleCloudRecommenderV1Operation extends \Google\Model
{
  /**
   * Type of this operation. Contains one of 'add', 'remove', 'replace', 'move',
   * 'copy', 'test' and custom operations. This field is case-insensitive and
   * always populated.
   *
   * @var string
   */
  public $action;
  /**
   * Path to the target field being operated on. If the operation is at the
   * resource level, then path should be "/". This field is always populated.
   *
   * @var string
   */
  public $path;
  /**
   * Set of filters to apply if `path` refers to array elements or nested array
   * elements in order to narrow down to a single unique element that is being
   * tested/modified. This is intended to be an exact match per filter. To
   * perform advanced matching, use path_value_matchers. * Example: ``` {
   * "/versions/name" : "it-123" "/versions/targetSize/percent": 20 } ``` *
   * Example: ``` { "/bindings/role": "roles/owner" "/bindings/condition" : null
   * } ``` * Example: ``` { "/bindings/role": "roles/owner" "/bindings/members"
   * : ["x@example.com", "y@example.com"] } ``` When both path_filters and
   * path_value_matchers are set, an implicit AND must be performed.
   *
   * @var array[]
   */
  public $pathFilters;
  protected $pathValueMatchersType = GoogleCloudRecommenderV1ValueMatcher::class;
  protected $pathValueMatchersDataType = 'map';
  /**
   * Contains the fully qualified resource name. This field is always populated.
   * ex: //cloudresourcemanager.googleapis.com/projects/foo.
   *
   * @var string
   */
  public $resource;
  /**
   * Type of GCP resource being modified/tested. This field is always populated.
   * Example: cloudresourcemanager.googleapis.com/Project,
   * compute.googleapis.com/Instance
   *
   * @var string
   */
  public $resourceType;
  /**
   * Can be set with action 'copy' or 'move' to indicate the source field within
   * resource or source_resource, ignored if provided for other operation types.
   *
   * @var string
   */
  public $sourcePath;
  /**
   * Can be set with action 'copy' to copy resource configuration across
   * different resources of the same type. Example: A resource clone can be done
   * via action = 'copy', path = "/", from = "/", source_resource = and
   * resource_name = . This field is empty for all other values of `action`.
   *
   * @var string
   */
  public $sourceResource;
  /**
   * Value for the `path` field. Will be set for actions:'add'/'replace'. Maybe
   * set for action: 'test'. Either this or `value_matcher` will be set for
   * 'test' operation. An exact match must be performed.
   *
   * @var array
   */
  public $value;
  protected $valueMatcherType = GoogleCloudRecommenderV1ValueMatcher::class;
  protected $valueMatcherDataType = '';

  /**
   * Type of this operation. Contains one of 'add', 'remove', 'replace', 'move',
   * 'copy', 'test' and custom operations. This field is case-insensitive and
   * always populated.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Path to the target field being operated on. If the operation is at the
   * resource level, then path should be "/". This field is always populated.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Set of filters to apply if `path` refers to array elements or nested array
   * elements in order to narrow down to a single unique element that is being
   * tested/modified. This is intended to be an exact match per filter. To
   * perform advanced matching, use path_value_matchers. * Example: ``` {
   * "/versions/name" : "it-123" "/versions/targetSize/percent": 20 } ``` *
   * Example: ``` { "/bindings/role": "roles/owner" "/bindings/condition" : null
   * } ``` * Example: ``` { "/bindings/role": "roles/owner" "/bindings/members"
   * : ["x@example.com", "y@example.com"] } ``` When both path_filters and
   * path_value_matchers are set, an implicit AND must be performed.
   *
   * @param array[] $pathFilters
   */
  public function setPathFilters($pathFilters)
  {
    $this->pathFilters = $pathFilters;
  }
  /**
   * @return array[]
   */
  public function getPathFilters()
  {
    return $this->pathFilters;
  }
  /**
   * Similar to path_filters, this contains set of filters to apply if `path`
   * field refers to array elements. This is meant to support value matching
   * beyond exact match. To perform exact match, use path_filters. When both
   * path_filters and path_value_matchers are set, an implicit AND must be
   * performed.
   *
   * @param GoogleCloudRecommenderV1ValueMatcher[] $pathValueMatchers
   */
  public function setPathValueMatchers($pathValueMatchers)
  {
    $this->pathValueMatchers = $pathValueMatchers;
  }
  /**
   * @return GoogleCloudRecommenderV1ValueMatcher[]
   */
  public function getPathValueMatchers()
  {
    return $this->pathValueMatchers;
  }
  /**
   * Contains the fully qualified resource name. This field is always populated.
   * ex: //cloudresourcemanager.googleapis.com/projects/foo.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Type of GCP resource being modified/tested. This field is always populated.
   * Example: cloudresourcemanager.googleapis.com/Project,
   * compute.googleapis.com/Instance
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Can be set with action 'copy' or 'move' to indicate the source field within
   * resource or source_resource, ignored if provided for other operation types.
   *
   * @param string $sourcePath
   */
  public function setSourcePath($sourcePath)
  {
    $this->sourcePath = $sourcePath;
  }
  /**
   * @return string
   */
  public function getSourcePath()
  {
    return $this->sourcePath;
  }
  /**
   * Can be set with action 'copy' to copy resource configuration across
   * different resources of the same type. Example: A resource clone can be done
   * via action = 'copy', path = "/", from = "/", source_resource = and
   * resource_name = . This field is empty for all other values of `action`.
   *
   * @param string $sourceResource
   */
  public function setSourceResource($sourceResource)
  {
    $this->sourceResource = $sourceResource;
  }
  /**
   * @return string
   */
  public function getSourceResource()
  {
    return $this->sourceResource;
  }
  /**
   * Value for the `path` field. Will be set for actions:'add'/'replace'. Maybe
   * set for action: 'test'. Either this or `value_matcher` will be set for
   * 'test' operation. An exact match must be performed.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Can be set for action 'test' for advanced matching for the value of 'path'
   * field. Either this or `value` will be set for 'test' operation.
   *
   * @param GoogleCloudRecommenderV1ValueMatcher $valueMatcher
   */
  public function setValueMatcher(GoogleCloudRecommenderV1ValueMatcher $valueMatcher)
  {
    $this->valueMatcher = $valueMatcher;
  }
  /**
   * @return GoogleCloudRecommenderV1ValueMatcher
   */
  public function getValueMatcher()
  {
    return $this->valueMatcher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommenderV1Operation::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1Operation');
