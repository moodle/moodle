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

class BatchDocumentInputConfig extends \Google\Model
{
  protected $gcsSourceType = GcsSource::class;
  protected $gcsSourceDataType = '';

  /**
   * Google Cloud Storage location for the source input. This can be a single
   * file (for example, `gs://translation-test/input.docx`) or a wildcard (for
   * example, `gs://translation-test`). File mime type is determined based on
   * extension. Supported mime type includes: - `pdf`, application/pdf - `docx`,
   * application/vnd.openxmlformats-officedocument.wordprocessingml.document -
   * `pptx`, application/vnd.openxmlformats-
   * officedocument.presentationml.presentation - `xlsx`,
   * application/vnd.openxmlformats-officedocument.spreadsheetml.sheet The max
   * file size to support for `.docx`, `.pptx` and `.xlsx` is 100MB. The max
   * file size to support for `.pdf` is 1GB and the max page limit is 1000
   * pages. The max file size to support for all input documents is 1GB.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchDocumentInputConfig::class, 'Google_Service_Translate_BatchDocumentInputConfig');
