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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter extends \Google\Collection
{
  /**
   * Count the documents per property name.
   */
  public const YA_XIS_HISTOGRAM_YAXIS_DOCUMENT = 'HISTOGRAM_YAXIS_DOCUMENT';
  /**
   * Count the properties per property name.
   */
  public const YA_XIS_HISTOGRAM_YAXIS_PROPERTY = 'HISTOGRAM_YAXIS_PROPERTY';
  protected $collection_key = 'propertyNames';
  /**
   * This filter specifies the exact document schema(s)
   * Document.document_schema_name to run histogram query against. It is
   * optional. It will perform histogram for property names for all the document
   * schemas if it is not set. At most 10 document schema names are allowed.
   * Format: projects/{project_number}/locations/{location}/documentSchemas/{doc
   * ument_schema_id}.
   *
   * @var string[]
   */
  public $documentSchemas;
  /**
   * It is optional. It will perform histogram for all the property names if it
   * is not set. The properties need to be defined with the is_filterable flag
   * set to true and the name of the property should be in the format:
   * "schemaId.propertyName". The property needs to be defined in the schema.
   * Example: the schema id is abc. Then the name of property for property
   * MORTGAGE_TYPE will be "abc.MORTGAGE_TYPE".
   *
   * @var string[]
   */
  public $propertyNames;
  /**
   * By default, the y_axis is HISTOGRAM_YAXIS_DOCUMENT if this field is not
   * set.
   *
   * @var string
   */
  public $yAxis;

  /**
   * This filter specifies the exact document schema(s)
   * Document.document_schema_name to run histogram query against. It is
   * optional. It will perform histogram for property names for all the document
   * schemas if it is not set. At most 10 document schema names are allowed.
   * Format: projects/{project_number}/locations/{location}/documentSchemas/{doc
   * ument_schema_id}.
   *
   * @param string[] $documentSchemas
   */
  public function setDocumentSchemas($documentSchemas)
  {
    $this->documentSchemas = $documentSchemas;
  }
  /**
   * @return string[]
   */
  public function getDocumentSchemas()
  {
    return $this->documentSchemas;
  }
  /**
   * It is optional. It will perform histogram for all the property names if it
   * is not set. The properties need to be defined with the is_filterable flag
   * set to true and the name of the property should be in the format:
   * "schemaId.propertyName". The property needs to be defined in the schema.
   * Example: the schema id is abc. Then the name of property for property
   * MORTGAGE_TYPE will be "abc.MORTGAGE_TYPE".
   *
   * @param string[] $propertyNames
   */
  public function setPropertyNames($propertyNames)
  {
    $this->propertyNames = $propertyNames;
  }
  /**
   * @return string[]
   */
  public function getPropertyNames()
  {
    return $this->propertyNames;
  }
  /**
   * By default, the y_axis is HISTOGRAM_YAXIS_DOCUMENT if this field is not
   * set.
   *
   * Accepted values: HISTOGRAM_YAXIS_DOCUMENT, HISTOGRAM_YAXIS_PROPERTY
   *
   * @param self::Y_AXIS_* $yAxis
   */
  public function setYAxis($yAxis)
  {
    $this->yAxis = $yAxis;
  }
  /**
   * @return self::Y_AXIS_*
   */
  public function getYAxis()
  {
    return $this->yAxis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter');
