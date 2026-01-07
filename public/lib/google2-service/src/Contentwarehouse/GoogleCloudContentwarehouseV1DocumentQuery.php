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

class GoogleCloudContentwarehouseV1DocumentQuery extends \Google\Collection
{
  protected $collection_key = 'timeFilters';
  /**
   * This filter specifies a structured syntax to match against the
   * [PropertyDefinition].is_filterable marked as `true`. The syntax for this
   * expression is a subset of SQL syntax. Supported operators are: `=`, `!=`,
   * `<`, `<=`, `>`, and `>=` where the left of the operator is a property name
   * and the right of the operator is a number or a quoted string. You must
   * escape backslash (\\) and quote (\") characters. Supported functions are
   * `LOWER([property_name])` to perform a case insensitive match and
   * `EMPTY([property_name])` to filter on the existence of a key. Boolean
   * expressions (AND/OR/NOT) are supported up to 3 levels of nesting (for
   * example, "((A AND B AND C) OR NOT D) AND E"), a maximum of 100 comparisons
   * or functions are allowed in the expression. The expression must be < 6000
   * bytes in length. Sample Query: `(LOWER(driving_license)="class \"a\"" OR
   * EMPTY(driving_license)) AND driving_years > 10`
   *
   * @deprecated
   * @var string
   */
  public $customPropertyFilter;
  protected $customWeightsMetadataType = GoogleCloudContentwarehouseV1CustomWeightsMetadata::class;
  protected $customWeightsMetadataDataType = '';
  /**
   * The exact creator(s) of the documents to search against. If a value isn't
   * specified, documents within the search results are associated with any
   * creator. If multiple values are specified, documents within the search
   * results may be associated with any of the specified creators.
   *
   * @var string[]
   */
  public $documentCreatorFilter;
  /**
   * Search the documents in the list. Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}.
   *
   * @var string[]
   */
  public $documentNameFilter;
  /**
   * This filter specifies the exact document schema
   * Document.document_schema_name of the documents to search against. If a
   * value isn't specified, documents within the search results are associated
   * with any schema. If multiple values are specified, documents within the
   * search results may be associated with any of the specified schemas. At most
   * 20 document schema names are allowed.
   *
   * @var string[]
   */
  public $documentSchemaNames;
  protected $fileTypeFilterType = GoogleCloudContentwarehouseV1FileTypeFilter::class;
  protected $fileTypeFilterDataType = '';
  /**
   * Search all the documents under this specified folder. Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}.
   *
   * @var string
   */
  public $folderNameFilter;
  /**
   * Experimental, do not use. If the query is a natural language question.
   * False by default. If true, then the question-answering feature will be used
   * instead of search, and `result_count` in SearchDocumentsRequest must be
   * set. In addition, all other input fields related to search (pagination,
   * histograms, etc.) will be ignored.
   *
   * @var bool
   */
  public $isNlQuery;
  protected $propertyFilterType = GoogleCloudContentwarehouseV1PropertyFilter::class;
  protected $propertyFilterDataType = 'array';
  /**
   * The query string that matches against the full text of the document and the
   * searchable properties. The query partially supports [Google AIP style
   * syntax](https://google.aip.dev/160). Specifically, the query supports
   * literals, logical operators, negation operators, comparison operators, and
   * functions. Literals: A bare literal value (examples: "42", "Hugo") is a
   * value to be matched against. It searches over the full text of the document
   * and the searchable properties. Logical operators: "AND", "and", "OR", and
   * "or" are binary logical operators (example: "engineer OR developer").
   * Negation operators: "NOT" and "!" are negation operators (example: "NOT
   * software"). Comparison operators: support the binary comparison operators
   * =, !=, <, >, <= and >= for string, numeric, enum, boolean. Also support
   * like operator `~~` for string. It provides semantic search functionality by
   * parsing, stemming and doing synonyms expansion against the input query. To
   * specify a property in the query, the left hand side expression in the
   * comparison must be the property ID including the parent. The right hand
   * side must be literals. For example:
   * "\"projects/123/locations/us\".property_a < 1" matches results whose
   * "property_a" is less than 1 in project 123 and us location. The literals
   * and comparison expression can be connected in a single query (example:
   * "software engineer \"projects/123/locations/us\".salary > 100"). Functions:
   * supported functions are `LOWER([property_name])` to perform a case
   * insensitive match and `EMPTY([property_name])` to filter on the existence
   * of a key. Support nested expressions connected using parenthesis and
   * logical operators. The default logical operators is `AND` if there is no
   * operators between expressions. The query can be used with other filters
   * e.g. `time_filters` and `folder_name_filter`. They are connected with `AND`
   * operator under the hood. The maximum number of allowed characters is 255.
   *
   * @var string
   */
  public $query;
  /**
   * For custom synonyms. Customers provide the synonyms based on context. One
   * customer can provide multiple set of synonyms based on different context.
   * The search query will be expanded based on the custom synonyms of the query
   * context set. By default, no custom synonyms wll be applied if no query
   * context is provided. It is not supported for CMEK compliant deployment.
   *
   * @var string[]
   */
  public $queryContext;
  protected $timeFiltersType = GoogleCloudContentwarehouseV1TimeFilter::class;
  protected $timeFiltersDataType = 'array';

  /**
   * This filter specifies a structured syntax to match against the
   * [PropertyDefinition].is_filterable marked as `true`. The syntax for this
   * expression is a subset of SQL syntax. Supported operators are: `=`, `!=`,
   * `<`, `<=`, `>`, and `>=` where the left of the operator is a property name
   * and the right of the operator is a number or a quoted string. You must
   * escape backslash (\\) and quote (\") characters. Supported functions are
   * `LOWER([property_name])` to perform a case insensitive match and
   * `EMPTY([property_name])` to filter on the existence of a key. Boolean
   * expressions (AND/OR/NOT) are supported up to 3 levels of nesting (for
   * example, "((A AND B AND C) OR NOT D) AND E"), a maximum of 100 comparisons
   * or functions are allowed in the expression. The expression must be < 6000
   * bytes in length. Sample Query: `(LOWER(driving_license)="class \"a\"" OR
   * EMPTY(driving_license)) AND driving_years > 10`
   *
   * @deprecated
   * @param string $customPropertyFilter
   */
  public function setCustomPropertyFilter($customPropertyFilter)
  {
    $this->customPropertyFilter = $customPropertyFilter;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCustomPropertyFilter()
  {
    return $this->customPropertyFilter;
  }
  /**
   * To support the custom weighting across document schemas, customers need to
   * provide the properties to be used to boost the ranking in the search
   * request. For a search query with CustomWeightsMetadata specified, only the
   * RetrievalImportance for the properties in the CustomWeightsMetadata will be
   * honored.
   *
   * @param GoogleCloudContentwarehouseV1CustomWeightsMetadata $customWeightsMetadata
   */
  public function setCustomWeightsMetadata(GoogleCloudContentwarehouseV1CustomWeightsMetadata $customWeightsMetadata)
  {
    $this->customWeightsMetadata = $customWeightsMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1CustomWeightsMetadata
   */
  public function getCustomWeightsMetadata()
  {
    return $this->customWeightsMetadata;
  }
  /**
   * The exact creator(s) of the documents to search against. If a value isn't
   * specified, documents within the search results are associated with any
   * creator. If multiple values are specified, documents within the search
   * results may be associated with any of the specified creators.
   *
   * @param string[] $documentCreatorFilter
   */
  public function setDocumentCreatorFilter($documentCreatorFilter)
  {
    $this->documentCreatorFilter = $documentCreatorFilter;
  }
  /**
   * @return string[]
   */
  public function getDocumentCreatorFilter()
  {
    return $this->documentCreatorFilter;
  }
  /**
   * Search the documents in the list. Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}.
   *
   * @param string[] $documentNameFilter
   */
  public function setDocumentNameFilter($documentNameFilter)
  {
    $this->documentNameFilter = $documentNameFilter;
  }
  /**
   * @return string[]
   */
  public function getDocumentNameFilter()
  {
    return $this->documentNameFilter;
  }
  /**
   * This filter specifies the exact document schema
   * Document.document_schema_name of the documents to search against. If a
   * value isn't specified, documents within the search results are associated
   * with any schema. If multiple values are specified, documents within the
   * search results may be associated with any of the specified schemas. At most
   * 20 document schema names are allowed.
   *
   * @param string[] $documentSchemaNames
   */
  public function setDocumentSchemaNames($documentSchemaNames)
  {
    $this->documentSchemaNames = $documentSchemaNames;
  }
  /**
   * @return string[]
   */
  public function getDocumentSchemaNames()
  {
    return $this->documentSchemaNames;
  }
  /**
   * This filter specifies the types of files to return: ALL, FOLDER, or FILE.
   * If FOLDER or FILE is specified, then only either folders or files will be
   * returned, respectively. If ALL is specified, both folders and files will be
   * returned. If no value is specified, ALL files will be returned.
   *
   * @param GoogleCloudContentwarehouseV1FileTypeFilter $fileTypeFilter
   */
  public function setFileTypeFilter(GoogleCloudContentwarehouseV1FileTypeFilter $fileTypeFilter)
  {
    $this->fileTypeFilter = $fileTypeFilter;
  }
  /**
   * @return GoogleCloudContentwarehouseV1FileTypeFilter
   */
  public function getFileTypeFilter()
  {
    return $this->fileTypeFilter;
  }
  /**
   * Search all the documents under this specified folder. Format:
   * projects/{project_number}/locations/{location}/documents/{document_id}.
   *
   * @param string $folderNameFilter
   */
  public function setFolderNameFilter($folderNameFilter)
  {
    $this->folderNameFilter = $folderNameFilter;
  }
  /**
   * @return string
   */
  public function getFolderNameFilter()
  {
    return $this->folderNameFilter;
  }
  /**
   * Experimental, do not use. If the query is a natural language question.
   * False by default. If true, then the question-answering feature will be used
   * instead of search, and `result_count` in SearchDocumentsRequest must be
   * set. In addition, all other input fields related to search (pagination,
   * histograms, etc.) will be ignored.
   *
   * @param bool $isNlQuery
   */
  public function setIsNlQuery($isNlQuery)
  {
    $this->isNlQuery = $isNlQuery;
  }
  /**
   * @return bool
   */
  public function getIsNlQuery()
  {
    return $this->isNlQuery;
  }
  /**
   * This filter specifies a structured syntax to match against the
   * PropertyDefinition.is_filterable marked as `true`. The relationship between
   * the PropertyFilters is OR.
   *
   * @param GoogleCloudContentwarehouseV1PropertyFilter[] $propertyFilter
   */
  public function setPropertyFilter($propertyFilter)
  {
    $this->propertyFilter = $propertyFilter;
  }
  /**
   * @return GoogleCloudContentwarehouseV1PropertyFilter[]
   */
  public function getPropertyFilter()
  {
    return $this->propertyFilter;
  }
  /**
   * The query string that matches against the full text of the document and the
   * searchable properties. The query partially supports [Google AIP style
   * syntax](https://google.aip.dev/160). Specifically, the query supports
   * literals, logical operators, negation operators, comparison operators, and
   * functions. Literals: A bare literal value (examples: "42", "Hugo") is a
   * value to be matched against. It searches over the full text of the document
   * and the searchable properties. Logical operators: "AND", "and", "OR", and
   * "or" are binary logical operators (example: "engineer OR developer").
   * Negation operators: "NOT" and "!" are negation operators (example: "NOT
   * software"). Comparison operators: support the binary comparison operators
   * =, !=, <, >, <= and >= for string, numeric, enum, boolean. Also support
   * like operator `~~` for string. It provides semantic search functionality by
   * parsing, stemming and doing synonyms expansion against the input query. To
   * specify a property in the query, the left hand side expression in the
   * comparison must be the property ID including the parent. The right hand
   * side must be literals. For example:
   * "\"projects/123/locations/us\".property_a < 1" matches results whose
   * "property_a" is less than 1 in project 123 and us location. The literals
   * and comparison expression can be connected in a single query (example:
   * "software engineer \"projects/123/locations/us\".salary > 100"). Functions:
   * supported functions are `LOWER([property_name])` to perform a case
   * insensitive match and `EMPTY([property_name])` to filter on the existence
   * of a key. Support nested expressions connected using parenthesis and
   * logical operators. The default logical operators is `AND` if there is no
   * operators between expressions. The query can be used with other filters
   * e.g. `time_filters` and `folder_name_filter`. They are connected with `AND`
   * operator under the hood. The maximum number of allowed characters is 255.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * For custom synonyms. Customers provide the synonyms based on context. One
   * customer can provide multiple set of synonyms based on different context.
   * The search query will be expanded based on the custom synonyms of the query
   * context set. By default, no custom synonyms wll be applied if no query
   * context is provided. It is not supported for CMEK compliant deployment.
   *
   * @param string[] $queryContext
   */
  public function setQueryContext($queryContext)
  {
    $this->queryContext = $queryContext;
  }
  /**
   * @return string[]
   */
  public function getQueryContext()
  {
    return $this->queryContext;
  }
  /**
   * Documents created/updated within a range specified by this filter are
   * searched against.
   *
   * @param GoogleCloudContentwarehouseV1TimeFilter[] $timeFilters
   */
  public function setTimeFilters($timeFilters)
  {
    $this->timeFilters = $timeFilters;
  }
  /**
   * @return GoogleCloudContentwarehouseV1TimeFilter[]
   */
  public function getTimeFilters()
  {
    return $this->timeFilters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1DocumentQuery::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1DocumentQuery');
