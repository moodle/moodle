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

namespace Google\Service\YouTubeAnalytics;

class GroupItem extends \Google\Model
{
  protected $errorsType = Errors::class;
  protected $errorsDataType = '';
  /**
   * The Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the group that contains the
   * item.
   *
   * @var string
   */
  public $groupId;
  /**
   * The ID that YouTube uses to uniquely identify the `channel`, `video`,
   * `playlist`, or `asset` resource that is included in the group. Note that
   * this ID refers specifically to the inclusion of that resource in a
   * particular group and is different than the channel ID, video ID, playlist
   * ID, or asset ID that uniquely identifies the resource itself. The
   * `resource.id` property's value specifies the unique channel, video,
   * playlist, or asset ID.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies the API resource's type. The value will be `youtube#groupItem`.
   *
   * @var string
   */
  public $kind;
  protected $resourceType = GroupItemResource::class;
  protected $resourceDataType = '';

  /**
   * Apiary error details
   *
   * @param Errors $errors
   */
  public function setErrors(Errors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The Etag of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The ID that YouTube uses to uniquely identify the group that contains the
   * item.
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * The ID that YouTube uses to uniquely identify the `channel`, `video`,
   * `playlist`, or `asset` resource that is included in the group. Note that
   * this ID refers specifically to the inclusion of that resource in a
   * particular group and is different than the channel ID, video ID, playlist
   * ID, or asset ID that uniquely identifies the resource itself. The
   * `resource.id` property's value specifies the unique channel, video,
   * playlist, or asset ID.
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
   * Identifies the API resource's type. The value will be `youtube#groupItem`.
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
  /**
   * The `resource` object contains information that identifies the item being
   * added to the group.
   *
   * @param GroupItemResource $resource
   */
  public function setResource(GroupItemResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return GroupItemResource
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupItem::class, 'Google_Service_YouTubeAnalytics_GroupItem');
