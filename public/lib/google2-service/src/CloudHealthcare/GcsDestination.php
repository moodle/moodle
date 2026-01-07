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

namespace Google\Service\CloudHealthcare;

class GcsDestination extends \Google\Model
{
  /**
   * If the content structure is not specified, the default value `MESSAGE_JSON`
   * will be used.
   */
  public const CONTENT_STRUCTURE_CONTENT_STRUCTURE_UNSPECIFIED = 'CONTENT_STRUCTURE_UNSPECIFIED';
  /**
   * Messages are printed using the JSON format returned from the `GetMessage`
   * API. Messages are delimited with newlines.
   */
  public const CONTENT_STRUCTURE_MESSAGE_JSON = 'MESSAGE_JSON';
  /**
   * Not specified, equivalent to FULL.
   */
  public const MESSAGE_VIEW_MESSAGE_VIEW_UNSPECIFIED = 'MESSAGE_VIEW_UNSPECIFIED';
  /**
   * Server responses include all the message fields except parsed_data field,
   * and schematized_data fields.
   */
  public const MESSAGE_VIEW_RAW_ONLY = 'RAW_ONLY';
  /**
   * Server responses include all the message fields except data field, and
   * schematized_data fields.
   */
  public const MESSAGE_VIEW_PARSED_ONLY = 'PARSED_ONLY';
  /**
   * Server responses include all the message fields.
   */
  public const MESSAGE_VIEW_FULL = 'FULL';
  /**
   * Server responses include all the message fields except data and parsed_data
   * fields.
   */
  public const MESSAGE_VIEW_SCHEMATIZED_ONLY = 'SCHEMATIZED_ONLY';
  /**
   * Server responses include only the name field.
   */
  public const MESSAGE_VIEW_BASIC = 'BASIC';
  /**
   * The format of the exported HL7v2 message files.
   *
   * @var string
   */
  public $contentStructure;
  /**
   * Specifies the parts of the Message resource to include in the export. If
   * not specified, FULL is used.
   *
   * @var string
   */
  public $messageView;
  /**
   * URI of an existing Cloud Storage directory where the server writes result
   * files, in the format `gs://{bucket-id}/{path/to/destination/dir}`. If there
   * is no trailing slash, the service appends one when composing the object
   * path.
   *
   * @var string
   */
  public $uriPrefix;

  /**
   * The format of the exported HL7v2 message files.
   *
   * Accepted values: CONTENT_STRUCTURE_UNSPECIFIED, MESSAGE_JSON
   *
   * @param self::CONTENT_STRUCTURE_* $contentStructure
   */
  public function setContentStructure($contentStructure)
  {
    $this->contentStructure = $contentStructure;
  }
  /**
   * @return self::CONTENT_STRUCTURE_*
   */
  public function getContentStructure()
  {
    return $this->contentStructure;
  }
  /**
   * Specifies the parts of the Message resource to include in the export. If
   * not specified, FULL is used.
   *
   * Accepted values: MESSAGE_VIEW_UNSPECIFIED, RAW_ONLY, PARSED_ONLY, FULL,
   * SCHEMATIZED_ONLY, BASIC
   *
   * @param self::MESSAGE_VIEW_* $messageView
   */
  public function setMessageView($messageView)
  {
    $this->messageView = $messageView;
  }
  /**
   * @return self::MESSAGE_VIEW_*
   */
  public function getMessageView()
  {
    return $this->messageView;
  }
  /**
   * URI of an existing Cloud Storage directory where the server writes result
   * files, in the format `gs://{bucket-id}/{path/to/destination/dir}`. If there
   * is no trailing slash, the service appends one when composing the object
   * path.
   *
   * @param string $uriPrefix
   */
  public function setUriPrefix($uriPrefix)
  {
    $this->uriPrefix = $uriPrefix;
  }
  /**
   * @return string
   */
  public function getUriPrefix()
  {
    return $this->uriPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcsDestination::class, 'Google_Service_CloudHealthcare_GcsDestination');
