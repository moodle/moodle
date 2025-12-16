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

namespace Google\Service\WorkspaceEvents;

class PayloadOptions extends \Google\Model
{
  /**
   * Optional. If `include_resource` is set to `true`, the list of fields to
   * include in the event payload. Separate fields with a comma. For example, to
   * include a Google Chat message's sender and create time, enter
   * `message.sender,message.createTime`. If omitted, the payload includes all
   * fields for the resource. If you specify a field that doesn't exist for the
   * resource, the system ignores the field.
   *
   * @var string
   */
  public $fieldMask;
  /**
   * Optional. Whether the event payload includes data about the resource that
   * changed. For example, for an event where a Google Chat message was created,
   * whether the payload contains data about the [`Message`](https://developers.
   * google.com/chat/api/reference/rest/v1/spaces.messages) resource. If false,
   * the event payload only includes the name of the changed resource.
   *
   * @var bool
   */
  public $includeResource;

  /**
   * Optional. If `include_resource` is set to `true`, the list of fields to
   * include in the event payload. Separate fields with a comma. For example, to
   * include a Google Chat message's sender and create time, enter
   * `message.sender,message.createTime`. If omitted, the payload includes all
   * fields for the resource. If you specify a field that doesn't exist for the
   * resource, the system ignores the field.
   *
   * @param string $fieldMask
   */
  public function setFieldMask($fieldMask)
  {
    $this->fieldMask = $fieldMask;
  }
  /**
   * @return string
   */
  public function getFieldMask()
  {
    return $this->fieldMask;
  }
  /**
   * Optional. Whether the event payload includes data about the resource that
   * changed. For example, for an event where a Google Chat message was created,
   * whether the payload contains data about the [`Message`](https://developers.
   * google.com/chat/api/reference/rest/v1/spaces.messages) resource. If false,
   * the event payload only includes the name of the changed resource.
   *
   * @param bool $includeResource
   */
  public function setIncludeResource($includeResource)
  {
    $this->includeResource = $includeResource;
  }
  /**
   * @return bool
   */
  public function getIncludeResource()
  {
    return $this->includeResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PayloadOptions::class, 'Google_Service_WorkspaceEvents_PayloadOptions');
