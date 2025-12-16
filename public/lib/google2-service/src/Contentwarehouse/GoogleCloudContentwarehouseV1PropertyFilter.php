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

class GoogleCloudContentwarehouseV1PropertyFilter extends \Google\Model
{
  /**
   * The filter condition. The syntax for this expression is a subset of SQL
   * syntax. Supported operators are: `=`, `!=`, `<`, `<=`, `>`, `>=`, and `~~`
   * where the left of the operator is a property name and the right of the
   * operator is a number or a quoted string. You must escape backslash (\\) and
   * quote (\") characters. `~~` is the LIKE operator. The right of the operator
   * must be a string. The only supported property data type for LIKE is
   * text_values. It provides semantic search functionality by parsing, stemming
   * and doing synonyms expansion against the input query. It matches if the
   * property contains semantic similar content to the query. It is not regex
   * matching or wildcard matching. For example, "property.company ~~
   * \"google\"" will match records whose property `property.compnay` have
   * values like "Google Inc.", "Google LLC" or "Google Company". Supported
   * functions are `LOWER([property_name])` to perform a case insensitive match
   * and `EMPTY([property_name])` to filter on the existence of a key. Boolean
   * expressions (AND/OR/NOT) are supported up to 3 levels of nesting (for
   * example, "((A AND B AND C) OR NOT D) AND E"), a maximum of 100 comparisons
   * or functions are allowed in the expression. The expression must be < 6000
   * bytes in length. Only properties that are marked filterable are allowed
   * (PropertyDefinition.is_filterable). Property names do not need to be
   * prefixed by the document schema id (as is the case with histograms),
   * however property names will need to be prefixed by its parent hierarchy, if
   * any. For example: top_property_name.sub_property_name. Sample Query:
   * `(LOWER(driving_license)="class \"a\"" OR EMPTY(driving_license)) AND
   * driving_years > 10` CMEK compliant deployment only supports: * Operators:
   * `=`, `<`, `<=`, `>`, and `>=`. * Boolean expressions: AND and OR.
   *
   * @var string
   */
  public $condition;
  /**
   * The Document schema name Document.document_schema_name. Format: projects/{p
   * roject_number}/locations/{location}/documentSchemas/{document_schema_id}.
   *
   * @var string
   */
  public $documentSchemaName;

  /**
   * The filter condition. The syntax for this expression is a subset of SQL
   * syntax. Supported operators are: `=`, `!=`, `<`, `<=`, `>`, `>=`, and `~~`
   * where the left of the operator is a property name and the right of the
   * operator is a number or a quoted string. You must escape backslash (\\) and
   * quote (\") characters. `~~` is the LIKE operator. The right of the operator
   * must be a string. The only supported property data type for LIKE is
   * text_values. It provides semantic search functionality by parsing, stemming
   * and doing synonyms expansion against the input query. It matches if the
   * property contains semantic similar content to the query. It is not regex
   * matching or wildcard matching. For example, "property.company ~~
   * \"google\"" will match records whose property `property.compnay` have
   * values like "Google Inc.", "Google LLC" or "Google Company". Supported
   * functions are `LOWER([property_name])` to perform a case insensitive match
   * and `EMPTY([property_name])` to filter on the existence of a key. Boolean
   * expressions (AND/OR/NOT) are supported up to 3 levels of nesting (for
   * example, "((A AND B AND C) OR NOT D) AND E"), a maximum of 100 comparisons
   * or functions are allowed in the expression. The expression must be < 6000
   * bytes in length. Only properties that are marked filterable are allowed
   * (PropertyDefinition.is_filterable). Property names do not need to be
   * prefixed by the document schema id (as is the case with histograms),
   * however property names will need to be prefixed by its parent hierarchy, if
   * any. For example: top_property_name.sub_property_name. Sample Query:
   * `(LOWER(driving_license)="class \"a\"" OR EMPTY(driving_license)) AND
   * driving_years > 10` CMEK compliant deployment only supports: * Operators:
   * `=`, `<`, `<=`, `>`, and `>=`. * Boolean expressions: AND and OR.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * The Document schema name Document.document_schema_name. Format: projects/{p
   * roject_number}/locations/{location}/documentSchemas/{document_schema_id}.
   *
   * @param string $documentSchemaName
   */
  public function setDocumentSchemaName($documentSchemaName)
  {
    $this->documentSchemaName = $documentSchemaName;
  }
  /**
   * @return string
   */
  public function getDocumentSchemaName()
  {
    return $this->documentSchemaName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1PropertyFilter::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1PropertyFilter');
