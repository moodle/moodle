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

class ExternalDataConfiguration extends \Google\Collection
{
  /**
   * This option expands source URIs by listing files from the object store. It
   * is the default behavior if FileSetSpecType is not set.
   */
  public const FILE_SET_SPEC_TYPE_FILE_SET_SPEC_TYPE_FILE_SYSTEM_MATCH = 'FILE_SET_SPEC_TYPE_FILE_SYSTEM_MATCH';
  /**
   * This option indicates that the provided URIs are newline-delimited manifest
   * files, with one URI per line. Wildcard URIs are not supported.
   */
  public const FILE_SET_SPEC_TYPE_FILE_SET_SPEC_TYPE_NEW_LINE_DELIMITED_MANIFEST = 'FILE_SET_SPEC_TYPE_NEW_LINE_DELIMITED_MANIFEST';
  /**
   * The default if provided value is not one included in the enum, or the value
   * is not specified. The source format is parsed without any modification.
   */
  public const JSON_EXTENSION_JSON_EXTENSION_UNSPECIFIED = 'JSON_EXTENSION_UNSPECIFIED';
  /**
   * Use GeoJSON variant of JSON. See https://tools.ietf.org/html/rfc7946.
   */
  public const JSON_EXTENSION_GEOJSON = 'GEOJSON';
  /**
   * Unspecified metadata cache mode.
   */
  public const METADATA_CACHE_MODE_METADATA_CACHE_MODE_UNSPECIFIED = 'METADATA_CACHE_MODE_UNSPECIFIED';
  /**
   * Set this mode to trigger automatic background refresh of metadata cache
   * from the external source. Queries will use the latest available cache
   * version within the table's maxStaleness interval.
   */
  public const METADATA_CACHE_MODE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Set this mode to enable triggering manual refresh of the metadata cache
   * from external source. Queries will use the latest manually triggered cache
   * version within the table's maxStaleness interval.
   */
  public const METADATA_CACHE_MODE_MANUAL = 'MANUAL';
  /**
   * Unspecified by default.
   */
  public const OBJECT_METADATA_OBJECT_METADATA_UNSPECIFIED = 'OBJECT_METADATA_UNSPECIFIED';
  /**
   * A synonym for `SIMPLE`.
   */
  public const OBJECT_METADATA_DIRECTORY = 'DIRECTORY';
  /**
   * Directory listing of objects.
   */
  public const OBJECT_METADATA_SIMPLE = 'SIMPLE';
  protected $collection_key = 'timestampTargetPrecision';
  /**
   * Try to detect schema and format options automatically. Any option specified
   * explicitly will be honored.
   *
   * @var bool
   */
  public $autodetect;
  protected $avroOptionsType = AvroOptions::class;
  protected $avroOptionsDataType = '';
  protected $bigtableOptionsType = BigtableOptions::class;
  protected $bigtableOptionsDataType = '';
  /**
   * Optional. The compression type of the data source. Possible values include
   * GZIP and NONE. The default value is NONE. This setting is ignored for
   * Google Cloud Bigtable, Google Cloud Datastore backups, Avro, ORC and
   * Parquet formats. An empty string is an invalid value.
   *
   * @var string
   */
  public $compression;
  /**
   * Optional. The connection specifying the credentials to be used to read
   * external storage, such as Azure Blob, Cloud Storage, or S3. The
   * connection_id can have the form
   * `{project_id}.{location_id};{connection_id}` or `projects/{project_id}/loca
   * tions/{location_id}/connections/{connection_id}`.
   *
   * @var string
   */
  public $connectionId;
  protected $csvOptionsType = CsvOptions::class;
  protected $csvOptionsDataType = '';
  /**
   * Optional. Format used to parse DATE values. Supports C-style and SQL-style
   * values.
   *
   * @var string
   */
  public $dateFormat;
  /**
   * Optional. Format used to parse DATETIME values. Supports C-style and SQL-
   * style values.
   *
   * @var string
   */
  public $datetimeFormat;
  /**
   * Defines the list of possible SQL data types to which the source decimal
   * values are converted. This list and the precision and the scale parameters
   * of the decimal field determine the target type. In the order of NUMERIC,
   * BIGNUMERIC, and STRING, a type is picked if it is in the specified list and
   * if it supports the precision and the scale. STRING supports all precision
   * and scale values. If none of the listed types supports the precision and
   * the scale, the type supporting the widest range in the specified list is
   * picked, and if a value exceeds the supported range when reading the data,
   * an error will be thrown. Example: Suppose the value of this field is
   * ["NUMERIC", "BIGNUMERIC"]. If (precision,scale) is: * (38,9) -> NUMERIC; *
   * (39,9) -> BIGNUMERIC (NUMERIC cannot hold 30 integer digits); * (38,10) ->
   * BIGNUMERIC (NUMERIC cannot hold 10 fractional digits); * (76,38) ->
   * BIGNUMERIC; * (77,38) -> BIGNUMERIC (error if value exceeds supported
   * range). This field cannot contain duplicate types. The order of the types
   * in this field is ignored. For example, ["BIGNUMERIC", "NUMERIC"] is the
   * same as ["NUMERIC", "BIGNUMERIC"] and NUMERIC always takes precedence over
   * BIGNUMERIC. Defaults to ["NUMERIC", "STRING"] for ORC and ["NUMERIC"] for
   * the other file formats.
   *
   * @var string[]
   */
  public $decimalTargetTypes;
  /**
   * Optional. Specifies how source URIs are interpreted for constructing the
   * file set to load. By default source URIs are expanded against the
   * underlying storage. Other options include specifying manifest files. Only
   * applicable to object storage systems.
   *
   * @var string
   */
  public $fileSetSpecType;
  protected $googleSheetsOptionsType = GoogleSheetsOptions::class;
  protected $googleSheetsOptionsDataType = '';
  protected $hivePartitioningOptionsType = HivePartitioningOptions::class;
  protected $hivePartitioningOptionsDataType = '';
  /**
   * Optional. Indicates if BigQuery should allow extra values that are not
   * represented in the table schema. If true, the extra values are ignored. If
   * false, records with extra columns are treated as bad records, and if there
   * are too many bad records, an invalid error is returned in the job result.
   * The default value is false. The sourceFormat property determines what
   * BigQuery treats as an extra value: CSV: Trailing columns JSON: Named values
   * that don't match any column names Google Cloud Bigtable: This setting is
   * ignored. Google Cloud Datastore backups: This setting is ignored. Avro:
   * This setting is ignored. ORC: This setting is ignored. Parquet: This
   * setting is ignored.
   *
   * @var bool
   */
  public $ignoreUnknownValues;
  /**
   * Optional. Load option to be used together with source_format newline-
   * delimited JSON to indicate that a variant of JSON is being loaded. To load
   * newline-delimited GeoJSON, specify GEOJSON (and source_format must be set
   * to NEWLINE_DELIMITED_JSON).
   *
   * @var string
   */
  public $jsonExtension;
  protected $jsonOptionsType = JsonOptions::class;
  protected $jsonOptionsDataType = '';
  /**
   * Optional. The maximum number of bad records that BigQuery can ignore when
   * reading data. If the number of bad records exceeds this value, an invalid
   * error is returned in the job result. The default value is 0, which requires
   * that all records are valid. This setting is ignored for Google Cloud
   * Bigtable, Google Cloud Datastore backups, Avro, ORC and Parquet formats.
   *
   * @var int
   */
  public $maxBadRecords;
  /**
   * Optional. Metadata Cache Mode for the table. Set this to enable caching of
   * metadata from external data source.
   *
   * @var string
   */
  public $metadataCacheMode;
  /**
   * Optional. ObjectMetadata is used to create Object Tables. Object Tables
   * contain a listing of objects (with their metadata) found at the
   * source_uris. If ObjectMetadata is set, source_format should be omitted.
   * Currently SIMPLE is the only supported Object Metadata type.
   *
   * @var string
   */
  public $objectMetadata;
  protected $parquetOptionsType = ParquetOptions::class;
  protected $parquetOptionsDataType = '';
  /**
   * Optional. When creating an external table, the user can provide a reference
   * file with the table schema. This is enabled for the following formats:
   * AVRO, PARQUET, ORC.
   *
   * @var string
   */
  public $referenceFileSchemaUri;
  protected $schemaType = TableSchema::class;
  protected $schemaDataType = '';
  /**
   * [Required] The data format. For CSV files, specify "CSV". For Google
   * sheets, specify "GOOGLE_SHEETS". For newline-delimited JSON, specify
   * "NEWLINE_DELIMITED_JSON". For Avro files, specify "AVRO". For Google Cloud
   * Datastore backups, specify "DATASTORE_BACKUP". For Apache Iceberg tables,
   * specify "ICEBERG". For ORC files, specify "ORC". For Parquet files, specify
   * "PARQUET". [Beta] For Google Cloud Bigtable, specify "BIGTABLE".
   *
   * @var string
   */
  public $sourceFormat;
  /**
   * [Required] The fully-qualified URIs that point to your data in Google
   * Cloud. For Google Cloud Storage URIs: Each URI can contain one '*' wildcard
   * character and it must come after the 'bucket' name. Size limits related to
   * load jobs apply to external data sources. For Google Cloud Bigtable URIs:
   * Exactly one URI can be specified and it has be a fully specified and valid
   * HTTPS URL for a Google Cloud Bigtable table. For Google Cloud Datastore
   * backups, exactly one URI can be specified. Also, the '*' wildcard character
   * is not allowed.
   *
   * @var string[]
   */
  public $sourceUris;
  /**
   * Optional. Format used to parse TIME values. Supports C-style and SQL-style
   * values.
   *
   * @var string
   */
  public $timeFormat;
  /**
   * Optional. Time zone used when parsing timestamp values that do not have
   * specific time zone information (e.g. 2024-04-20 12:34:56). The expected
   * format is a IANA timezone string (e.g. America/Los_Angeles).
   *
   * @var string
   */
  public $timeZone;
  /**
   * Optional. Format used to parse TIMESTAMP values. Supports C-style and SQL-
   * style values.
   *
   * @var string
   */
  public $timestampFormat;
  /**
   * Precisions (maximum number of total digits in base 10) for seconds of
   * TIMESTAMP types that are allowed to the destination table for autodetection
   * mode. Available for the formats: CSV. For the CSV Format, Possible values
   * include: Not Specified, [], or [6]: timestamp(6) for all auto detected
   * TIMESTAMP columns [6, 12]: timestamp(6) for all auto detected TIMESTAMP
   * columns that have less than 6 digits of subseconds. timestamp(12) for all
   * auto detected TIMESTAMP columns that have more than 6 digits of subseconds.
   * [12]: timestamp(12) for all auto detected TIMESTAMP columns. The order of
   * the elements in this array is ignored. Inputs that have higher precision
   * than the highest target precision in this array will be truncated.
   *
   * @var int[]
   */
  public $timestampTargetPrecision;

  /**
   * Try to detect schema and format options automatically. Any option specified
   * explicitly will be honored.
   *
   * @param bool $autodetect
   */
  public function setAutodetect($autodetect)
  {
    $this->autodetect = $autodetect;
  }
  /**
   * @return bool
   */
  public function getAutodetect()
  {
    return $this->autodetect;
  }
  /**
   * Optional. Additional properties to set if sourceFormat is set to AVRO.
   *
   * @param AvroOptions $avroOptions
   */
  public function setAvroOptions(AvroOptions $avroOptions)
  {
    $this->avroOptions = $avroOptions;
  }
  /**
   * @return AvroOptions
   */
  public function getAvroOptions()
  {
    return $this->avroOptions;
  }
  /**
   * Optional. Additional options if sourceFormat is set to BIGTABLE.
   *
   * @param BigtableOptions $bigtableOptions
   */
  public function setBigtableOptions(BigtableOptions $bigtableOptions)
  {
    $this->bigtableOptions = $bigtableOptions;
  }
  /**
   * @return BigtableOptions
   */
  public function getBigtableOptions()
  {
    return $this->bigtableOptions;
  }
  /**
   * Optional. The compression type of the data source. Possible values include
   * GZIP and NONE. The default value is NONE. This setting is ignored for
   * Google Cloud Bigtable, Google Cloud Datastore backups, Avro, ORC and
   * Parquet formats. An empty string is an invalid value.
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
   * Optional. The connection specifying the credentials to be used to read
   * external storage, such as Azure Blob, Cloud Storage, or S3. The
   * connection_id can have the form
   * `{project_id}.{location_id};{connection_id}` or `projects/{project_id}/loca
   * tions/{location_id}/connections/{connection_id}`.
   *
   * @param string $connectionId
   */
  public function setConnectionId($connectionId)
  {
    $this->connectionId = $connectionId;
  }
  /**
   * @return string
   */
  public function getConnectionId()
  {
    return $this->connectionId;
  }
  /**
   * Optional. Additional properties to set if sourceFormat is set to CSV.
   *
   * @param CsvOptions $csvOptions
   */
  public function setCsvOptions(CsvOptions $csvOptions)
  {
    $this->csvOptions = $csvOptions;
  }
  /**
   * @return CsvOptions
   */
  public function getCsvOptions()
  {
    return $this->csvOptions;
  }
  /**
   * Optional. Format used to parse DATE values. Supports C-style and SQL-style
   * values.
   *
   * @param string $dateFormat
   */
  public function setDateFormat($dateFormat)
  {
    $this->dateFormat = $dateFormat;
  }
  /**
   * @return string
   */
  public function getDateFormat()
  {
    return $this->dateFormat;
  }
  /**
   * Optional. Format used to parse DATETIME values. Supports C-style and SQL-
   * style values.
   *
   * @param string $datetimeFormat
   */
  public function setDatetimeFormat($datetimeFormat)
  {
    $this->datetimeFormat = $datetimeFormat;
  }
  /**
   * @return string
   */
  public function getDatetimeFormat()
  {
    return $this->datetimeFormat;
  }
  /**
   * Defines the list of possible SQL data types to which the source decimal
   * values are converted. This list and the precision and the scale parameters
   * of the decimal field determine the target type. In the order of NUMERIC,
   * BIGNUMERIC, and STRING, a type is picked if it is in the specified list and
   * if it supports the precision and the scale. STRING supports all precision
   * and scale values. If none of the listed types supports the precision and
   * the scale, the type supporting the widest range in the specified list is
   * picked, and if a value exceeds the supported range when reading the data,
   * an error will be thrown. Example: Suppose the value of this field is
   * ["NUMERIC", "BIGNUMERIC"]. If (precision,scale) is: * (38,9) -> NUMERIC; *
   * (39,9) -> BIGNUMERIC (NUMERIC cannot hold 30 integer digits); * (38,10) ->
   * BIGNUMERIC (NUMERIC cannot hold 10 fractional digits); * (76,38) ->
   * BIGNUMERIC; * (77,38) -> BIGNUMERIC (error if value exceeds supported
   * range). This field cannot contain duplicate types. The order of the types
   * in this field is ignored. For example, ["BIGNUMERIC", "NUMERIC"] is the
   * same as ["NUMERIC", "BIGNUMERIC"] and NUMERIC always takes precedence over
   * BIGNUMERIC. Defaults to ["NUMERIC", "STRING"] for ORC and ["NUMERIC"] for
   * the other file formats.
   *
   * @param string[] $decimalTargetTypes
   */
  public function setDecimalTargetTypes($decimalTargetTypes)
  {
    $this->decimalTargetTypes = $decimalTargetTypes;
  }
  /**
   * @return string[]
   */
  public function getDecimalTargetTypes()
  {
    return $this->decimalTargetTypes;
  }
  /**
   * Optional. Specifies how source URIs are interpreted for constructing the
   * file set to load. By default source URIs are expanded against the
   * underlying storage. Other options include specifying manifest files. Only
   * applicable to object storage systems.
   *
   * Accepted values: FILE_SET_SPEC_TYPE_FILE_SYSTEM_MATCH,
   * FILE_SET_SPEC_TYPE_NEW_LINE_DELIMITED_MANIFEST
   *
   * @param self::FILE_SET_SPEC_TYPE_* $fileSetSpecType
   */
  public function setFileSetSpecType($fileSetSpecType)
  {
    $this->fileSetSpecType = $fileSetSpecType;
  }
  /**
   * @return self::FILE_SET_SPEC_TYPE_*
   */
  public function getFileSetSpecType()
  {
    return $this->fileSetSpecType;
  }
  /**
   * Optional. Additional options if sourceFormat is set to GOOGLE_SHEETS.
   *
   * @param GoogleSheetsOptions $googleSheetsOptions
   */
  public function setGoogleSheetsOptions(GoogleSheetsOptions $googleSheetsOptions)
  {
    $this->googleSheetsOptions = $googleSheetsOptions;
  }
  /**
   * @return GoogleSheetsOptions
   */
  public function getGoogleSheetsOptions()
  {
    return $this->googleSheetsOptions;
  }
  /**
   * Optional. When set, configures hive partitioning support. Not all storage
   * formats support hive partitioning -- requesting hive partitioning on an
   * unsupported format will lead to an error, as will providing an invalid
   * specification.
   *
   * @param HivePartitioningOptions $hivePartitioningOptions
   */
  public function setHivePartitioningOptions(HivePartitioningOptions $hivePartitioningOptions)
  {
    $this->hivePartitioningOptions = $hivePartitioningOptions;
  }
  /**
   * @return HivePartitioningOptions
   */
  public function getHivePartitioningOptions()
  {
    return $this->hivePartitioningOptions;
  }
  /**
   * Optional. Indicates if BigQuery should allow extra values that are not
   * represented in the table schema. If true, the extra values are ignored. If
   * false, records with extra columns are treated as bad records, and if there
   * are too many bad records, an invalid error is returned in the job result.
   * The default value is false. The sourceFormat property determines what
   * BigQuery treats as an extra value: CSV: Trailing columns JSON: Named values
   * that don't match any column names Google Cloud Bigtable: This setting is
   * ignored. Google Cloud Datastore backups: This setting is ignored. Avro:
   * This setting is ignored. ORC: This setting is ignored. Parquet: This
   * setting is ignored.
   *
   * @param bool $ignoreUnknownValues
   */
  public function setIgnoreUnknownValues($ignoreUnknownValues)
  {
    $this->ignoreUnknownValues = $ignoreUnknownValues;
  }
  /**
   * @return bool
   */
  public function getIgnoreUnknownValues()
  {
    return $this->ignoreUnknownValues;
  }
  /**
   * Optional. Load option to be used together with source_format newline-
   * delimited JSON to indicate that a variant of JSON is being loaded. To load
   * newline-delimited GeoJSON, specify GEOJSON (and source_format must be set
   * to NEWLINE_DELIMITED_JSON).
   *
   * Accepted values: JSON_EXTENSION_UNSPECIFIED, GEOJSON
   *
   * @param self::JSON_EXTENSION_* $jsonExtension
   */
  public function setJsonExtension($jsonExtension)
  {
    $this->jsonExtension = $jsonExtension;
  }
  /**
   * @return self::JSON_EXTENSION_*
   */
  public function getJsonExtension()
  {
    return $this->jsonExtension;
  }
  /**
   * Optional. Additional properties to set if sourceFormat is set to JSON.
   *
   * @param JsonOptions $jsonOptions
   */
  public function setJsonOptions(JsonOptions $jsonOptions)
  {
    $this->jsonOptions = $jsonOptions;
  }
  /**
   * @return JsonOptions
   */
  public function getJsonOptions()
  {
    return $this->jsonOptions;
  }
  /**
   * Optional. The maximum number of bad records that BigQuery can ignore when
   * reading data. If the number of bad records exceeds this value, an invalid
   * error is returned in the job result. The default value is 0, which requires
   * that all records are valid. This setting is ignored for Google Cloud
   * Bigtable, Google Cloud Datastore backups, Avro, ORC and Parquet formats.
   *
   * @param int $maxBadRecords
   */
  public function setMaxBadRecords($maxBadRecords)
  {
    $this->maxBadRecords = $maxBadRecords;
  }
  /**
   * @return int
   */
  public function getMaxBadRecords()
  {
    return $this->maxBadRecords;
  }
  /**
   * Optional. Metadata Cache Mode for the table. Set this to enable caching of
   * metadata from external data source.
   *
   * Accepted values: METADATA_CACHE_MODE_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @param self::METADATA_CACHE_MODE_* $metadataCacheMode
   */
  public function setMetadataCacheMode($metadataCacheMode)
  {
    $this->metadataCacheMode = $metadataCacheMode;
  }
  /**
   * @return self::METADATA_CACHE_MODE_*
   */
  public function getMetadataCacheMode()
  {
    return $this->metadataCacheMode;
  }
  /**
   * Optional. ObjectMetadata is used to create Object Tables. Object Tables
   * contain a listing of objects (with their metadata) found at the
   * source_uris. If ObjectMetadata is set, source_format should be omitted.
   * Currently SIMPLE is the only supported Object Metadata type.
   *
   * Accepted values: OBJECT_METADATA_UNSPECIFIED, DIRECTORY, SIMPLE
   *
   * @param self::OBJECT_METADATA_* $objectMetadata
   */
  public function setObjectMetadata($objectMetadata)
  {
    $this->objectMetadata = $objectMetadata;
  }
  /**
   * @return self::OBJECT_METADATA_*
   */
  public function getObjectMetadata()
  {
    return $this->objectMetadata;
  }
  /**
   * Optional. Additional properties to set if sourceFormat is set to PARQUET.
   *
   * @param ParquetOptions $parquetOptions
   */
  public function setParquetOptions(ParquetOptions $parquetOptions)
  {
    $this->parquetOptions = $parquetOptions;
  }
  /**
   * @return ParquetOptions
   */
  public function getParquetOptions()
  {
    return $this->parquetOptions;
  }
  /**
   * Optional. When creating an external table, the user can provide a reference
   * file with the table schema. This is enabled for the following formats:
   * AVRO, PARQUET, ORC.
   *
   * @param string $referenceFileSchemaUri
   */
  public function setReferenceFileSchemaUri($referenceFileSchemaUri)
  {
    $this->referenceFileSchemaUri = $referenceFileSchemaUri;
  }
  /**
   * @return string
   */
  public function getReferenceFileSchemaUri()
  {
    return $this->referenceFileSchemaUri;
  }
  /**
   * Optional. The schema for the data. Schema is required for CSV and JSON
   * formats if autodetect is not on. Schema is disallowed for Google Cloud
   * Bigtable, Cloud Datastore backups, Avro, ORC and Parquet formats.
   *
   * @param TableSchema $schema
   */
  public function setSchema(TableSchema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return TableSchema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * [Required] The data format. For CSV files, specify "CSV". For Google
   * sheets, specify "GOOGLE_SHEETS". For newline-delimited JSON, specify
   * "NEWLINE_DELIMITED_JSON". For Avro files, specify "AVRO". For Google Cloud
   * Datastore backups, specify "DATASTORE_BACKUP". For Apache Iceberg tables,
   * specify "ICEBERG". For ORC files, specify "ORC". For Parquet files, specify
   * "PARQUET". [Beta] For Google Cloud Bigtable, specify "BIGTABLE".
   *
   * @param string $sourceFormat
   */
  public function setSourceFormat($sourceFormat)
  {
    $this->sourceFormat = $sourceFormat;
  }
  /**
   * @return string
   */
  public function getSourceFormat()
  {
    return $this->sourceFormat;
  }
  /**
   * [Required] The fully-qualified URIs that point to your data in Google
   * Cloud. For Google Cloud Storage URIs: Each URI can contain one '*' wildcard
   * character and it must come after the 'bucket' name. Size limits related to
   * load jobs apply to external data sources. For Google Cloud Bigtable URIs:
   * Exactly one URI can be specified and it has be a fully specified and valid
   * HTTPS URL for a Google Cloud Bigtable table. For Google Cloud Datastore
   * backups, exactly one URI can be specified. Also, the '*' wildcard character
   * is not allowed.
   *
   * @param string[] $sourceUris
   */
  public function setSourceUris($sourceUris)
  {
    $this->sourceUris = $sourceUris;
  }
  /**
   * @return string[]
   */
  public function getSourceUris()
  {
    return $this->sourceUris;
  }
  /**
   * Optional. Format used to parse TIME values. Supports C-style and SQL-style
   * values.
   *
   * @param string $timeFormat
   */
  public function setTimeFormat($timeFormat)
  {
    $this->timeFormat = $timeFormat;
  }
  /**
   * @return string
   */
  public function getTimeFormat()
  {
    return $this->timeFormat;
  }
  /**
   * Optional. Time zone used when parsing timestamp values that do not have
   * specific time zone information (e.g. 2024-04-20 12:34:56). The expected
   * format is a IANA timezone string (e.g. America/Los_Angeles).
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Optional. Format used to parse TIMESTAMP values. Supports C-style and SQL-
   * style values.
   *
   * @param string $timestampFormat
   */
  public function setTimestampFormat($timestampFormat)
  {
    $this->timestampFormat = $timestampFormat;
  }
  /**
   * @return string
   */
  public function getTimestampFormat()
  {
    return $this->timestampFormat;
  }
  /**
   * Precisions (maximum number of total digits in base 10) for seconds of
   * TIMESTAMP types that are allowed to the destination table for autodetection
   * mode. Available for the formats: CSV. For the CSV Format, Possible values
   * include: Not Specified, [], or [6]: timestamp(6) for all auto detected
   * TIMESTAMP columns [6, 12]: timestamp(6) for all auto detected TIMESTAMP
   * columns that have less than 6 digits of subseconds. timestamp(12) for all
   * auto detected TIMESTAMP columns that have more than 6 digits of subseconds.
   * [12]: timestamp(12) for all auto detected TIMESTAMP columns. The order of
   * the elements in this array is ignored. Inputs that have higher precision
   * than the highest target precision in this array will be truncated.
   *
   * @param int[] $timestampTargetPrecision
   */
  public function setTimestampTargetPrecision($timestampTargetPrecision)
  {
    $this->timestampTargetPrecision = $timestampTargetPrecision;
  }
  /**
   * @return int[]
   */
  public function getTimestampTargetPrecision()
  {
    return $this->timestampTargetPrecision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalDataConfiguration::class, 'Google_Service_Bigquery_ExternalDataConfiguration');
