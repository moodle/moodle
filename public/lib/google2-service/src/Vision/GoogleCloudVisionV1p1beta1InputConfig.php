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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p1beta1InputConfig extends \Google\Model
{
  /**
   * File content, represented as a stream of bytes. Note: As with all `bytes`
   * fields, protobuffers use a pure binary representation, whereas JSON
   * representations use base64. Currently, this field only works for
   * BatchAnnotateFiles requests. It does not work for AsyncBatchAnnotateFiles
   * requests.
   *
   * @var string
   */
  public $content;
  protected $gcsSourceType = GoogleCloudVisionV1p1beta1GcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * The type of the file. Currently only "application/pdf", "image/tiff" and
   * "image/gif" are supported. Wildcards are not supported.
   *
   * @var string
   */
  public $mimeType;

  /**
   * File content, represented as a stream of bytes. Note: As with all `bytes`
   * fields, protobuffers use a pure binary representation, whereas JSON
   * representations use base64. Currently, this field only works for
   * BatchAnnotateFiles requests. It does not work for AsyncBatchAnnotateFiles
   * requests.
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
   * The Google Cloud Storage location to read the input from.
   *
   * @param GoogleCloudVisionV1p1beta1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudVisionV1p1beta1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudVisionV1p1beta1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * The type of the file. Currently only "application/pdf", "image/tiff" and
   * "image/gif" are supported. Wildcards are not supported.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p1beta1InputConfig::class, 'Google_Service_Vision_GoogleCloudVisionV1p1beta1InputConfig');
