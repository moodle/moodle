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

namespace Google\Service\Translate;

class DetectLanguageRequest extends \Google\Model
{
  /**
   * The content of the input stored as a string.
   *
   * @var string
   */
  public $content;
  /**
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. Label values are optional. Label keys
   * must start with a letter. See
   * https://cloud.google.com/translate/docs/advanced/labels for more
   * information.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The format of the source text, for example, "text/html",
   * "text/plain". If left blank, the MIME type defaults to "text/html".
   *
   * @var string
   */
  public $mimeType;
  /**
   * Optional. The language detection model to be used. Format:
   * `projects/{project-number-or-id}/locations/{location-id}/models/language-
   * detection/{model-id}` Only one language detection model is currently
   * supported: `projects/{project-number-or-id}/locations/{location-
   * id}/models/language-detection/default`. If not specified, the default model
   * is used.
   *
   * @var string
   */
  public $model;

  /**
   * The content of the input stored as a string.
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
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. Label values are optional. Label keys
   * must start with a letter. See
   * https://cloud.google.com/translate/docs/advanced/labels for more
   * information.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The format of the source text, for example, "text/html",
   * "text/plain". If left blank, the MIME type defaults to "text/html".
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
  /**
   * Optional. The language detection model to be used. Format:
   * `projects/{project-number-or-id}/locations/{location-id}/models/language-
   * detection/{model-id}` Only one language detection model is currently
   * supported: `projects/{project-number-or-id}/locations/{location-
   * id}/models/language-detection/default`. If not specified, the default model
   * is used.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DetectLanguageRequest::class, 'Google_Service_Translate_DetectLanguageRequest');
