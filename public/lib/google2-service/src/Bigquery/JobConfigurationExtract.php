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

namespace Google\Service\Bigquery;

class JobConfigurationExtract extends \Google\Collection
{
  protected $collection_key = 'destinationUris';
  /**
   * Optional. The compression type to use for exported files. Possible values
   * include DEFLATE, GZIP, NONE, SNAPPY, and ZSTD. The default value is NONE.
   * Not all compression formats are support for all file formats. DEFLATE is
   * only supported for Avro. ZSTD is only supported for Parquet. Not applicable
   * when extracting models.
   *
   * @var string
   */
  public $compression;
  /**
   * Optional. The exported file format. Possible values include CSV,
   * NEWLINE_DELIMITED_JSON, PARQUET, or AVRO for tables and ML_TF_SAVED_MODEL
   * or ML_XGBOOST_BOOSTER for models. The default value for tables is CSV.
   * Tables with nested or repeated fields cannot be exported as CSV. The
   * default value for models is ML_TF_SAVED_MODEL.
   *
   * @var string
   */
  public $destinationFormat;
  /**
   * [Pick one] DEPRECATED: Use destinationUris instead, passing only one URI as
   * necessary. The fully-qualified Google Cloud Storage URI where the extracted
   * table should be written.
   *
   * @var string
   */
  public $destinationUri;
  /**
   * [Pick one] A list of fully-qualified Google Cloud Storage URIs where the
   * extracted table should be written.
   *
   * @var string[]
   */
  public $destinationUris;
  /**
   * Optional. When extracting data in CSV format, this defines the delimiter to
   * use between fields in the exported data. Default is ','. Not applicable
   * when extracting models.
   *
   * @var string
   */
  public $fieldDelimiter;
  protected $modelExtractOptionsType = ModelExtractOptions::class;
  protected $modelExtractOptionsDataType = '';
  /**
   * Optional. Whether to print out a header row in the results. Default is
   * true. Not applicable when extracting models.
   *
   * @var bool
   */
  public $printHeader;
  protected $sourceModelType = ModelReference::class;
  protected $sourceModelDataType = '';
  protected $sourceTableType = TableReference::class;
  protected $sourceTableDataType = '';
  /**
   * Whether to use logical types when extracting to AVRO format. Not applicable
   * when extracting models.
   *
   * @var bool
   */
  public $useAvroLogicalTypes;

  /**
   * Optional. The compression type to use for exported files. Possible values
   * include DEFLATE, GZIP, NONE, SNAPPY, and ZSTD. The default value is NONE.
   * Not all compression formats are support for all file formats. DEFLATE is
   * only supported for Avro. ZSTD is only supported for Parquet. Not applicable
   * when extracting models.
   *
   * @param string $compression
   */
  public function setCompression($compression)
  {
    $this->compression = $compression;
  }
  /**
   * @return string
   */
  public function getCompression()
  {
    return $this->compression;
  }
  /**
   * Optional. The exported file format. Possible values include CSV,
   * NEWLINE_DELIMITED_JSON, PARQUET, or AVRO for tables and ML_TF_SAVED_MODEL
   * or ML_XGBOOST_BOOSTER for models. The default value for tables is CSV.
   * Tables with nested or repeated fields cannot be exported as CSV. The
   * default value for models is ML_TF_SAVED_MODEL.
   *
   * @param string $destinationFormat
   */
  public function setDestinationFormat($destinationFormat)
  {
    $this->destinationFormat = $destinationFormat;
  }
  /**
   * @return string
   */
  public function getDestinationFormat()
  {
    return $this->destinationFormat;
  }
  /**
   * [Pick one] DEPRECATED: Use destinationUris instead, passing only one URI as
   * necessary. The fully-qualified Google Cloud Storage URI where the extracted
   * table should be written.
   *
   * @param string $destinationUri
   */
  public function setDestinationUri($destinationUri)
  {
    $this->destinationUri = $destinationUri;
  }
  /**
   * @return string
   */
  public function getDestinationUri()
  {
    return $this->destinationUri;
  }
  /**
   * [Pick one] A list of fully-qualified Google Cloud Storage URIs where the
   * extracted table should be written.
   *
   * @param string[] $destinationUris
   */
  public function setDestinationUris($destinationUris)
  {
    $this->destinationUris = $destinationUris;
  }
  /**
   * @return string[]
   */
  public function getDestinationUris()
  {
    return $this->destinationUris;
  }
  /**
   * Optional. When extracting data in CSV format, this defines the delimiter to
   * use between fields in the exported data. Default is ','. Not applicable
   * when extracting models.
   *
   * @param string $fieldDelimiter
   */
  public function setFieldDelimiter($fieldDelimiter)
  {
    $this->fieldDelimiter = $fieldDelimiter;
  }
  /**
   * @return string
   */
  public function getFieldDelimiter()
  {
    return $this->fieldDelimiter;
  }
  /**
   * Optional. Model extract options only applicable when extracting models.
   *
   * @param ModelExtractOptions $modelExtractOptions
   */
  public function setModelExtractOptions(ModelExtractOptions $modelExtractOptions)
  {
    $this->modelExtractOptions = $modelExtractOptions;
  }
  /**
   * @return ModelExtractOptions
   */
  public function getModelExtractOptions()
  {
    return $this->modelExtractOptions;
  }
  /**
   * Optional. Whether to print out a header row in the results. Default is
   * true. Not applicable when extracting models.
   *
   * @param bool $printHeader
   */
  public function setPrintHeader($printHeader)
  {
    $this->printHeader = $printHeader;
  }
  /**
   * @return bool
   */
  public function getPrintHeader()
  {
    return $this->printHeader;
  }
  /**
   * A reference to the model being exported.
   *
   * @param ModelReference $sourceModel
   */
  public function setSourceModel(ModelReference $sourceModel)
  {
    $this->sourceModel = $sourceModel;
  }
  /**
   * @return ModelReference
   */
  public function getSourceModel()
  {
    return $this->sourceModel;
  }
  /**
   * A reference to the table being exported.
   *
   * @param TableReference $sourceTable
   */
  public function setSourceTable(TableReference $sourceTable)
  {
    $this->sourceTable = $sourceTable;
  }
  /**
   * @return TableReference
   */
  public function getSourceTable()
  {
    return $this->sourceTable;
  }
  /**
   * Whether to use logical types when extracting to AVRO format. Not applicable
   * when extracting models.
   *
   * @param bool $useAvroLogicalTypes
   */
  public function setUseAvroLogicalTypes($useAvroLogicalTypes)
  {
    $this->useAvroLogicalTypes = $useAvroLogicalTypes;
  }
  /**
   * @return bool
   */
  public function getUseAvroLogicalTypes()
  {
    return $this->useAvroLogicalTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobConfigurationExtract::class, 'Google_Service_Bigquery_JobConfigurationExtract');
