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

namespace Google\Service\CloudNaturalLanguage;

class Document extends \Google\Model
{
  /**
   * The content type is not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Plain text
   */
  public const TYPE_PLAIN_TEXT = 'PLAIN_TEXT';
  /**
   * HTML
   */
  public const TYPE_HTML = 'HTML';
  /**
   * The content of the input in string format. Cloud audit logging exempt since
   * it is based on user data.
   *
   * @var string
   */
  public $content;
  /**
   * The Google Cloud Storage URI where the file content is located. This URI
   * must be of the form: gs://bucket_name/object_name. For more details, see
   * https://cloud.google.com/storage/docs/reference-uris. NOTE: Cloud Storage
   * object versioning is not supported.
   *
   * @var string
   */
  public $gcsContentUri;
  /**
   * Optional. The language of the document (if not specified, the language is
   * automatically detected). Both ISO and BCP-47 language codes are accepted.
   * [Language Support](https://cloud.google.com/natural-
   * language/docs/languages) lists currently supported languages for each API
   * method. If the language (either specified by the caller or automatically
   * detected) is not supported by the called API method, an `INVALID_ARGUMENT`
   * error is returned.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Required. If the type is not set or is `TYPE_UNSPECIFIED`, returns an
   * `INVALID_ARGUMENT` error.
   *
   * @var string
   */
  public $type;

  /**
   * The content of the input in string format. Cloud audit logging exempt since
   * it is based on user data.
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
   * The Google Cloud Storage URI where the file content is located. This URI
   * must be of the form: gs://bucket_name/object_name. For more details, see
   * https://cloud.google.com/storage/docs/reference-uris. NOTE: Cloud Storage
   * object versioning is not supported.
   *
   * @param string $gcsContentUri
   */
  public function setGcsContentUri($gcsContentUri)
  {
    $this->gcsContentUri = $gcsContentUri;
  }
  /**
   * @return string
   */
  public function getGcsContentUri()
  {
    return $this->gcsContentUri;
  }
  /**
   * Optional. The language of the document (if not specified, the language is
   * automatically detected). Both ISO and BCP-47 language codes are accepted.
   * [Language Support](https://cloud.google.com/natural-
   * language/docs/languages) lists currently supported languages for each API
   * method. If the language (either specified by the caller or automatically
   * detected) is not supported by the called API method, an `INVALID_ARGUMENT`
   * error is returned.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Required. If the type is not set or is `TYPE_UNSPECIFIED`, returns an
   * `INVALID_ARGUMENT` error.
   *
   * Accepted values: TYPE_UNSPECIFIED, PLAIN_TEXT, HTML
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Document::class, 'Google_Service_CloudNaturalLanguage_Document');
