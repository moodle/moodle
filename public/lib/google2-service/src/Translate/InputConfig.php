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

class InputConfig extends \Google\Model
{
  protected $gcsSourceType = GcsSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Optional. Can be "text/plain" or "text/html". For `.tsv`, "text/html" is
   * used if mime_type is missing. For `.html`, this field must be "text/html"
   * or empty. For `.txt`, this field must be "text/plain" or empty.
   *
   * @var string
   */
  public $mimeType;

  /**
   * Required. Google Cloud Storage location for the source input. This can be a
   * single file (for example, `gs://translation-test/input.tsv`) or a wildcard
   * (for example, `gs://translation-test`). If a file extension is `.tsv`, it
   * can contain either one or two columns. The first column (optional) is the
   * id of the text request. If the first column is missing, we use the row
   * number (0-based) from the input file as the ID in the output file. The
   * second column is the actual text to be translated. We recommend each row be
   * <= 10K Unicode codepoints, otherwise an error might be returned. Note that
   * the input tsv must be RFC 4180 compliant. You could use
   * https://github.com/Clever/csvlint to check potential formatting errors in
   * your tsv file. csvlint --delimiter='\t' your_input_file.tsv The other
   * supported file extensions are `.txt` or `.html`, which is treated as a
   * single large chunk of text.
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
   * Optional. Can be "text/plain" or "text/html". For `.tsv`, "text/html" is
   * used if mime_type is missing. For `.html`, this field must be "text/html"
   * or empty. For `.txt`, this field must be "text/plain" or empty.
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
class_alias(InputConfig::class, 'Google_Service_Translate_InputConfig');
