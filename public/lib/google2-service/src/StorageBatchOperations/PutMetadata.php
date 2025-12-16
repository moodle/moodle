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

namespace Google\Service\StorageBatchOperations;

class PutMetadata extends \Google\Model
{
  /**
   * Optional. Updates objects Cache-Control fixed metadata. Unset values will
   * be ignored. Set empty values to clear the metadata. Additionally, the value
   * for Custom-Time cannot decrease. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#caching_data.
   *
   * @var string
   */
  public $cacheControl;
  /**
   * Optional. Updates objects Content-Disposition fixed metadata. Unset values
   * will be ignored. Set empty values to clear the metadata. Refer
   * https://cloud.google.com/storage/docs/metadata#content-disposition for
   * additional documentation.
   *
   * @var string
   */
  public $contentDisposition;
  /**
   * Optional. Updates objects Content-Encoding fixed metadata. Unset values
   * will be ignored. Set empty values to clear the metadata. Refer to
   * documentation in https://cloud.google.com/storage/docs/metadata#content-
   * encoding.
   *
   * @var string
   */
  public $contentEncoding;
  /**
   * Optional. Updates objects Content-Language fixed metadata. Refer to ISO
   * 639-1 language codes for typical values of this metadata. Max length 100
   * characters. Unset values will be ignored. Set empty values to clear the
   * metadata. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#content-language.
   *
   * @var string
   */
  public $contentLanguage;
  /**
   * Optional. Updates objects Content-Type fixed metadata. Unset values will be
   * ignored. Set empty values to clear the metadata. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#content-type
   *
   * @var string
   */
  public $contentType;
  /**
   * Optional. Updates objects custom metadata. Adds or sets individual custom
   * metadata key value pairs on objects. Keys that are set with empty custom
   * metadata values will have its value cleared. Existing custom metadata not
   * specified with this flag is not changed. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#custom-metadata
   *
   * @var string[]
   */
  public $customMetadata;
  /**
   * Optional. Updates objects Custom-Time fixed metadata. Unset values will be
   * ignored. Set empty values to clear the metadata. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#custom-time.
   *
   * @var string
   */
  public $customTime;
  protected $objectRetentionType = ObjectRetention::class;
  protected $objectRetentionDataType = '';

  /**
   * Optional. Updates objects Cache-Control fixed metadata. Unset values will
   * be ignored. Set empty values to clear the metadata. Additionally, the value
   * for Custom-Time cannot decrease. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#caching_data.
   *
   * @param string $cacheControl
   */
  public function setCacheControl($cacheControl)
  {
    $this->cacheControl = $cacheControl;
  }
  /**
   * @return string
   */
  public function getCacheControl()
  {
    return $this->cacheControl;
  }
  /**
   * Optional. Updates objects Content-Disposition fixed metadata. Unset values
   * will be ignored. Set empty values to clear the metadata. Refer
   * https://cloud.google.com/storage/docs/metadata#content-disposition for
   * additional documentation.
   *
   * @param string $contentDisposition
   */
  public function setContentDisposition($contentDisposition)
  {
    $this->contentDisposition = $contentDisposition;
  }
  /**
   * @return string
   */
  public function getContentDisposition()
  {
    return $this->contentDisposition;
  }
  /**
   * Optional. Updates objects Content-Encoding fixed metadata. Unset values
   * will be ignored. Set empty values to clear the metadata. Refer to
   * documentation in https://cloud.google.com/storage/docs/metadata#content-
   * encoding.
   *
   * @param string $contentEncoding
   */
  public function setContentEncoding($contentEncoding)
  {
    $this->contentEncoding = $contentEncoding;
  }
  /**
   * @return string
   */
  public function getContentEncoding()
  {
    return $this->contentEncoding;
  }
  /**
   * Optional. Updates objects Content-Language fixed metadata. Refer to ISO
   * 639-1 language codes for typical values of this metadata. Max length 100
   * characters. Unset values will be ignored. Set empty values to clear the
   * metadata. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#content-language.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * Optional. Updates objects Content-Type fixed metadata. Unset values will be
   * ignored. Set empty values to clear the metadata. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#content-type
   *
   * @param string $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * Optional. Updates objects custom metadata. Adds or sets individual custom
   * metadata key value pairs on objects. Keys that are set with empty custom
   * metadata values will have its value cleared. Existing custom metadata not
   * specified with this flag is not changed. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#custom-metadata
   *
   * @param string[] $customMetadata
   */
  public function setCustomMetadata($customMetadata)
  {
    $this->customMetadata = $customMetadata;
  }
  /**
   * @return string[]
   */
  public function getCustomMetadata()
  {
    return $this->customMetadata;
  }
  /**
   * Optional. Updates objects Custom-Time fixed metadata. Unset values will be
   * ignored. Set empty values to clear the metadata. Refer to documentation in
   * https://cloud.google.com/storage/docs/metadata#custom-time.
   *
   * @param string $customTime
   */
  public function setCustomTime($customTime)
  {
    $this->customTime = $customTime;
  }
  /**
   * @return string
   */
  public function getCustomTime()
  {
    return $this->customTime;
  }
  /**
   * Optional. Updates objects retention lock configuration. Unset values will
   * be ignored. Set empty values to clear the retention for the object with
   * existing `Unlocked` retention mode. Object with existing `Locked` retention
   * mode cannot be cleared or reduce retain_until_time. Refer to documentation
   * in https://cloud.google.com/storage/docs/object-lock
   *
   * @param ObjectRetention $objectRetention
   */
  public function setObjectRetention(ObjectRetention $objectRetention)
  {
    $this->objectRetention = $objectRetention;
  }
  /**
   * @return ObjectRetention
   */
  public function getObjectRetention()
  {
    return $this->objectRetention;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PutMetadata::class, 'Google_Service_StorageBatchOperations_PutMetadata');
