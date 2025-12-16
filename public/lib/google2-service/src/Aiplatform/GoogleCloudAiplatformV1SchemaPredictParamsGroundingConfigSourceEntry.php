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

class GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfigSourceEntry extends \Google\Model
{
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Uses Web Search to check the grounding.
   */
  public const TYPE_WEB = 'WEB';
  /**
   * Uses Vertex AI Search to check the grounding. Deprecated. Use
   * VERTEX_AI_SEARCH instead.
   *
   * @deprecated
   */
  public const TYPE_ENTERPRISE = 'ENTERPRISE';
  /**
   * Uses Vertex AI Search to check the grounding
   */
  public const TYPE_VERTEX_AI_SEARCH = 'VERTEX_AI_SEARCH';
  /**
   * Uses inline context to check the grounding.
   */
  public const TYPE_INLINE = 'INLINE';
  /**
   * The uri of the Vertex AI Search data source. Deprecated. Use
   * vertex_ai_search_datastore instead.
   *
   * @deprecated
   * @var string
   */
  public $enterpriseDatastore;
  /**
   * The grounding text passed inline with the Predict API. It can support up to
   * 1 million bytes.
   *
   * @var string
   */
  public $inlineContext;
  /**
   * The type of the grounding checking source.
   *
   * @var string
   */
  public $type;
  /**
   * The uri of the Vertex AI Search data source.
   *
   * @var string
   */
  public $vertexAiSearchDatastore;

  /**
   * The uri of the Vertex AI Search data source. Deprecated. Use
   * vertex_ai_search_datastore instead.
   *
   * @deprecated
   * @param string $enterpriseDatastore
   */
  public function setEnterpriseDatastore($enterpriseDatastore)
  {
    $this->enterpriseDatastore = $enterpriseDatastore;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEnterpriseDatastore()
  {
    return $this->enterpriseDatastore;
  }
  /**
   * The grounding text passed inline with the Predict API. It can support up to
   * 1 million bytes.
   *
   * @param string $inlineContext
   */
  public function setInlineContext($inlineContext)
  {
    $this->inlineContext = $inlineContext;
  }
  /**
   * @return string
   */
  public function getInlineContext()
  {
    return $this->inlineContext;
  }
  /**
   * The type of the grounding checking source.
   *
   * Accepted values: UNSPECIFIED, WEB, ENTERPRISE, VERTEX_AI_SEARCH, INLINE
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
  /**
   * The uri of the Vertex AI Search data source.
   *
   * @param string $vertexAiSearchDatastore
   */
  public function setVertexAiSearchDatastore($vertexAiSearchDatastore)
  {
    $this->vertexAiSearchDatastore = $vertexAiSearchDatastore;
  }
  /**
   * @return string
   */
  public function getVertexAiSearchDatastore()
  {
    return $this->vertexAiSearchDatastore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfigSourceEntry::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictParamsGroundingConfigSourceEntry');
