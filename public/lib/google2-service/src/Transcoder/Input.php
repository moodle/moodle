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

namespace Google\Service\Transcoder;

class Input extends \Google\Model
{
  protected $attributesType = InputAttributes::class;
  protected $attributesDataType = '';
  /**
   * A unique key for this input. Must be specified when using advanced mapping
   * and edit lists.
   *
   * @var string
   */
  public $key;
  protected $preprocessingConfigType = PreprocessingConfig::class;
  protected $preprocessingConfigDataType = '';
  /**
   * URI of the media. Input files must be at least 5 seconds in duration and
   * stored in Cloud Storage (for example, `gs://bucket/inputs/file.mp4`). If
   * empty, the value is populated from Job.input_uri. See [Supported input and
   * output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats).
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. Input Attributes.
   *
   * @param InputAttributes $attributes
   */
  public function setAttributes(InputAttributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return InputAttributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * A unique key for this input. Must be specified when using advanced mapping
   * and edit lists.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Preprocessing configurations.
   *
   * @param PreprocessingConfig $preprocessingConfig
   */
  public function setPreprocessingConfig(PreprocessingConfig $preprocessingConfig)
  {
    $this->preprocessingConfig = $preprocessingConfig;
  }
  /**
   * @return PreprocessingConfig
   */
  public function getPreprocessingConfig()
  {
    return $this->preprocessingConfig;
  }
  /**
   * URI of the media. Input files must be at least 5 seconds in duration and
   * stored in Cloud Storage (for example, `gs://bucket/inputs/file.mp4`). If
   * empty, the value is populated from Job.input_uri. See [Supported input and
   * output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats).
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
class_alias(Input::class, 'Google_Service_Transcoder_Input');
