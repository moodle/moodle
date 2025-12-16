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

class DocumentInputConfig extends \Google\Model
{
  /**
   * Document's content represented as a stream of bytes.
   *
   * @var string
   */
  public $content;
  protected $gcsSourceType = GcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Specifies the input document's mime_type. If not specified it will be
   * determined using the file extension for gcs_source provided files. For a
   * file provided through bytes content the mime_type must be provided.
   * Currently supported mime types are: - application/pdf -
   * application/vnd.openxmlformats-officedocument.wordprocessingml.document -
   * application/vnd.openxmlformats-officedocument.presentationml.presentation -
   * application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
   *
   * @var string
   */
  public $mimeType;

  /**
   * Document's content represented as a stream of bytes.
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
   * Google Cloud Storage location. This must be a single file. For example:
   * gs://example_bucket/example_file.pdf
   *
   * @param GcsSource $gcsSource
   */
  public function setGcsSource(GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Specifies the input document's mime_type. If not specified it will be
   * determined using the file extension for gcs_source provided files. For a
   * file provided through bytes content the mime_type must be provided.
   * Currently supported mime types are: - application/pdf -
   * application/vnd.openxmlformats-officedocument.wordprocessingml.document -
   * application/vnd.openxmlformats-officedocument.presentationml.presentation -
   * application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
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
class_alias(DocumentInputConfig::class, 'Google_Service_Translate_DocumentInputConfig');
