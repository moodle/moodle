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

namespace Google\Service\Speech;

class RecognitionAudio extends \Google\Model
{
  /**
   * The audio data bytes encoded as specified in `RecognitionConfig`. Note: as
   * with all bytes fields, proto buffers use a pure binary representation,
   * whereas JSON representations use base64.
   *
   * @var string
   */
  public $content;
  /**
   * URI that points to a file that contains audio data bytes as specified in
   * `RecognitionConfig`. The file must not be compressed (for example, gzip).
   * Currently, only Google Cloud Storage URIs are supported, which must be
   * specified in the following format: `gs://bucket_name/object_name` (other
   * URI formats return google.rpc.Code.INVALID_ARGUMENT). For more information,
   * see [Request URIs](https://cloud.google.com/storage/docs/reference-uris).
   *
   * @var string
   */
  public $uri;

  /**
   * The audio data bytes encoded as specified in `RecognitionConfig`. Note: as
   * with all bytes fields, proto buffers use a pure binary representation,
   * whereas JSON representations use base64.
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
   * URI that points to a file that contains audio data bytes as specified in
   * `RecognitionConfig`. The file must not be compressed (for example, gzip).
   * Currently, only Google Cloud Storage URIs are supported, which must be
   * specified in the following format: `gs://bucket_name/object_name` (other
   * URI formats return google.rpc.Code.INVALID_ARGUMENT). For more information,
   * see [Request URIs](https://cloud.google.com/storage/docs/reference-uris).
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecognitionAudio::class, 'Google_Service_Speech_RecognitionAudio');
