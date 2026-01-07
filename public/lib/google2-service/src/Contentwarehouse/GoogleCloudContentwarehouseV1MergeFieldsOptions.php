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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1MergeFieldsOptions extends \Google\Model
{
  /**
   * When merging message fields, the default behavior is to merge the content
   * of two message fields together. If you instead want to use the field from
   * the source message to replace the corresponding field in the destination
   * message, set this flag to true. When this flag is set, specified submessage
   * fields that are missing in source will be cleared in destination.
   *
   * @var bool
   */
  public $replaceMessageFields;
  /**
   * When merging repeated fields, the default behavior is to append entries
   * from the source repeated field to the destination repeated field. If you
   * instead want to keep only the entries from the source repeated field, set
   * this flag to true. If you want to replace a repeated field within a message
   * field on the destination message, you must set both replace_repeated_fields
   * and replace_message_fields to true, otherwise the repeated fields will be
   * appended.
   *
   * @var bool
   */
  public $replaceRepeatedFields;

  /**
   * When merging message fields, the default behavior is to merge the content
   * of two message fields together. If you instead want to use the field from
   * the source message to replace the corresponding field in the destination
   * message, set this flag to true. When this flag is set, specified submessage
   * fields that are missing in source will be cleared in destination.
   *
   * @param bool $replaceMessageFields
   */
  public function setReplaceMessageFields($replaceMessageFields)
  {
    $this->replaceMessageFields = $replaceMessageFields;
  }
  /**
   * @return bool
   */
  public function getReplaceMessageFields()
  {
    return $this->replaceMessageFields;
  }
  /**
   * When merging repeated fields, the default behavior is to append entries
   * from the source repeated field to the destination repeated field. If you
   * instead want to keep only the entries from the source repeated field, set
   * this flag to true. If you want to replace a repeated field within a message
   * field on the destination message, you must set both replace_repeated_fields
   * and replace_message_fields to true, otherwise the repeated fields will be
   * appended.
   *
   * @param bool $replaceRepeatedFields
   */
  public function setReplaceRepeatedFields($replaceRepeatedFields)
  {
    $this->replaceRepeatedFields = $replaceRepeatedFields;
  }
  /**
   * @return bool
   */
  public function getReplaceRepeatedFields()
  {
    return $this->replaceRepeatedFields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1MergeFieldsOptions::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1MergeFieldsOptions');
