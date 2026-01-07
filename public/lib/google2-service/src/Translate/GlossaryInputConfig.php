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

class GlossaryInputConfig extends \Google\Model
{
  protected $gcsSourceType = GcsSource::class;
  protected $gcsSourceDataType = '';

  /**
   * Required. Google Cloud Storage location of glossary data. File format is
   * determined based on the filename extension. API returns
   * [google.rpc.Code.INVALID_ARGUMENT] for unsupported URI-s and file formats.
   * Wildcards are not allowed. This must be a single file in one of the
   * following formats: For unidirectional glossaries: - TSV/CSV
   * (`.tsv`/`.csv`): Two column file, tab- or comma-separated. The first column
   * is source text. The second column is target text. No headers in this file.
   * The first row contains data and not column names. - TMX (`.tmx`): TMX file
   * with parallel data defining source/target term pairs. For equivalent term
   * sets glossaries: - CSV (`.csv`): Multi-column CSV file defining equivalent
   * glossary terms in multiple languages. See documentation for more
   * information -
   * [glossaries](https://cloud.google.com/translate/docs/advanced/glossary).
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
class_alias(GlossaryInputConfig::class, 'Google_Service_Translate_GlossaryInputConfig');
