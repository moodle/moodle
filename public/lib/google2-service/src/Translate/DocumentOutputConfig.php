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

class DocumentOutputConfig extends \Google\Model
{
  protected $gcsDestinationType = GcsDestination::class;
  protected $gcsDestinationDataType = '';
  /**
   * Optional. Specifies the translated document's mime_type. If not specified,
   * the translated file's mime type will be the same as the input file's mime
   * type. Currently only support the output mime type to be the same as input
   * mime type. - application/pdf - application/vnd.openxmlformats-
   * officedocument.wordprocessingml.document - application/vnd.openxmlformats-
   * officedocument.presentationml.presentation -
   * application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
   *
   * @var string
   */
  public $mimeType;

  /**
   * Optional. Google Cloud Storage destination for the translation output,
   * e.g., `gs://my_bucket/my_directory/`. The destination directory provided
   * does not have to be empty, but the bucket must exist. If a file with the
   * same name as the output file already exists in the destination an error
   * will be returned. For a DocumentInputConfig.contents provided document, the
   * output file will have the name "output_[trg]_translations.[ext]", where -
   * [trg] corresponds to the translated file's language code, - [ext]
   * corresponds to the translated file's extension according to its mime type.
   * For a DocumentInputConfig.gcs_uri provided document, the output file will
   * have a name according to its URI. For example: an input file with URI:
   * `gs://a/b/c.[extension]` stored in a gcs_destination bucket with name
   * "my_bucket" will have an output URI:
   * `gs://my_bucket/a_b_c_[trg]_translations.[ext]`, where - [trg] corresponds
   * to the translated file's language code, - [ext] corresponds to the
   * translated file's extension according to its mime type. If the document was
   * directly provided through the request, then the output document will have
   * the format: `gs://my_bucket/translated_document_[trg]_translations.[ext]`,
   * where - [trg] corresponds to the translated file's language code, - [ext]
   * corresponds to the translated file's extension according to its mime type.
   * If a glossary was provided, then the output URI for the glossary
   * translation will be equal to the default output URI but have
   * `glossary_translations` instead of `translations`. For the previous
   * example, its glossary URI would be:
   * `gs://my_bucket/a_b_c_[trg]_glossary_translations.[ext]`. Thus the max
   * number of output files will be 2 (Translated document, Glossary translated
   * document). Callers should expect no partial outputs. If there is any error
   * during document translation, no output will be stored in the Cloud Storage
   * bucket.
   *
   * @param GcsDestination $gcsDestination
   */
  public function setGcsDestination(GcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
  /**
   * Optional. Specifies the translated document's mime_type. If not specified,
   * the translated file's mime type will be the same as the input file's mime
   * type. Currently only support the output mime type to be the same as input
   * mime type. - application/pdf - application/vnd.openxmlformats-
   * officedocument.wordprocessingml.document - application/vnd.openxmlformats-
   * officedocument.presentationml.presentation -
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
class_alias(DocumentOutputConfig::class, 'Google_Service_Translate_DocumentOutputConfig');
