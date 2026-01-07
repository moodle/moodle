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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Presets extends \Google\Model
{
  /**
   * Should not be set. Added as a recommended best practice for enums
   */
  public const MODALITY_MODALITY_UNSPECIFIED = 'MODALITY_UNSPECIFIED';
  /**
   * IMAGE modality
   */
  public const MODALITY_IMAGE = 'IMAGE';
  /**
   * TEXT modality
   */
  public const MODALITY_TEXT = 'TEXT';
  /**
   * TABULAR modality
   */
  public const MODALITY_TABULAR = 'TABULAR';
  /**
   * More precise neighbors as a trade-off against slower response.
   */
  public const QUERY_PRECISE = 'PRECISE';
  /**
   * Faster response as a trade-off against less precise neighbors.
   */
  public const QUERY_FAST = 'FAST';
  /**
   * The modality of the uploaded model, which automatically configures the
   * distance measurement and feature normalization for the underlying example
   * index and queries. If your model does not precisely fit one of these types,
   * it is okay to choose the closest type.
   *
   * @var string
   */
  public $modality;
  /**
   * Preset option controlling parameters for speed-precision trade-off when
   * querying for examples. If omitted, defaults to `PRECISE`.
   *
   * @var string
   */
  public $query;

  /**
   * The modality of the uploaded model, which automatically configures the
   * distance measurement and feature normalization for the underlying example
   * index and queries. If your model does not precisely fit one of these types,
   * it is okay to choose the closest type.
   *
   * Accepted values: MODALITY_UNSPECIFIED, IMAGE, TEXT, TABULAR
   *
   * @param self::MODALITY_* $modality
   */
  public function setModality($modality)
  {
    $this->modality = $modality;
  }
  /**
   * @return self::MODALITY_*
   */
  public function getModality()
  {
    return $this->modality;
  }
  /**
   * Preset option controlling parameters for speed-precision trade-off when
   * querying for examples. If omitted, defaults to `PRECISE`.
   *
   * Accepted values: PRECISE, FAST
   *
   * @param self::QUERY_* $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return self::QUERY_*
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Presets::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Presets');
