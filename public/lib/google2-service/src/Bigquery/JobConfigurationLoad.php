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

class JobConfigurationLoad extends \Google\Collection
{
  /**
   * Unspecified column name character map.
   */
  public const COLUMN_NAME_CHARACTER_MAP_COLUMN_NAME_CHARACTER_MAP_UNSPECIFIED = 'COLUMN_NAME_CHARACTER_MAP_UNSPECIFIED';
  /**
   * Support flexible column name and reject invalid column names.
   */
  public const COLUMN_NAME_CHARACTER_MAP_STRICT = 'STRICT';
  /**
   * Support alphanumeric + underscore characters and names must start with a
   * letter or underscore. Invalid column names will be normalized.
   */
  public const COLUMN_NAME_CHARACTER_MAP_V1 = 'V1';
  /**
   * Support flexible column name. Invalid column names will be normalized.
   */
  public const COLUMN_NAME_CHARACTER_MAP_V2 = 'V2';
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
   * Uses sensible defaults based on how the schema is provided. If autodetect
   * is used, then columns are matched by name. Otherwise, columns are matched
   * by position. This is done to keep the behavior backward-compatible.
   */
  public const SOURCE_COLUMN_MATCH_SOURCE_COLUMN_MATCH_UNSPECIFIED = 'SOURCE_COLUMN_MATCH_UNSPECIFIED';
  /**
   * Matches by position. This assumes that the columns are ordered the same way
   * as the schema.
   */
  public const SOURCE_COLUMN_MATCH_POSITION = 'POSITION';
  /**
   * Matches by name. This reads the header row as column names and reorders
   * columns to match the field names in the schema.
   */
  public const SOURCE_COLUMN_MATCH_NAME = 'NAME';
  protected $collection_key = 'timestampTargetPrecision';
  /**
   * Optional. Accept rows that are missing trailing optional columns. The
   * missing values are treated as nulls. If false, records with missing
   * trailing columns are treated as bad records, and if there are too many bad
   * records, an invalid error is returned in the job result. The default value
   * is false. Only applicable to CSV, ignored for other formats.
   *
   * @var bool
   */
  public $allowJaggedRows;
  /**
   * Indicates if BigQuery should allow quoted data sections that contain
   * newline characters in a CSV file. The default value is false.
   *
   * @var bool
   */
  public $allowQuotedNewlines;
  /**
   * Optional. Indicates if we should automatically infer the options and schema
   * for CSV and JSON sources.
   *
   * @var bool
   */
  public $autodetect;
  protected $clusteringType = Clustering::class;
  protected $clusteringDataType = '';
  /**
   * Optional. Character map supported for column names in CSV/Parquet loads.
   * Defaults to STRICT and can be overridden by Project Config Service. Using
   * this option with unsupporting load formats will result in an error.
   *
   * @var string
   */
  public $columnNameCharacterMap;
  protected $connectionPropertiesType = ConnectionProperty::class;
  protected $connectionPropertiesDataType = 'array';
  /**
   * Optional. [Experimental] Configures the load job to copy files directly to
   * the destination BigLake managed table, bypassing file content reading and
   * rewriting. Copying files only is supported when all the following are true:
   * * `source_uris` are located in the same Cloud Storage location as the
   * destination table's `storage_uri` location. * `source_format` is `PARQUET`.
   * * `destination_table` is an existing BigLake managed table. The table's
   * schema does not have flexible column names. The table's columns do not have
   * type parameters other than precision and scale. * No options other than the
   * above are specified.
   *
   * @var bool
   */
  public $copyFilesOnly;
  /**
   * Optional. Specifies whether the job is allowed to create new tables. The
   * following values are supported: * CREATE_IF_NEEDED: If the table does not
   * exist, BigQuery creates the table. * CREATE_NEVER: The table must already
   * exist. If it does not, a 'notFound' error is returned in the job result.
   * The default value is CREATE_IF_NEEDED. Creation, truncation and append
   * actions occur as one atomic update upon job completion.
   *
   * @var string
   */
  public $createDisposition;
  /**
   * Optional. If this property is true, the job creates a new session using a
   * randomly generated session_id. To continue using a created session with
   * subsequent queries, pass the existing session identifier as a
   * `ConnectionProperty` value. The session identifier is returned as part of
   * the `SessionInfo` message within the query statistics. The new session's
   * location will be set to `Job.JobReference.location` if it is present,
   * otherwise it's set to the default location based on existing routing logic.
   *
   * @var bool
   */
  public $createSession;
  /**
   * Optional. Date format used for parsing DATE values.
   *
   * @var string
   */
  public $dateFormat;
  /**
   * Optional. Date format used for parsing DATETIME values.
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
  protected $destinationEncryptionConfigurationType = EncryptionConfiguration::class;
  protected $destinationEncryptionConfigurationDataType = '';
  protected $destinationTableType = TableReference::class;
  protected $destinationTableDataType = '';
  protected $destinationTablePropertiesType = DestinationTableProperties::class;
  protected $destinationTablePropertiesDataType = '';
  /**
   * Optional. The character encoding of the data. The supported values are
   * UTF-8, ISO-8859-1, UTF-16BE, UTF-16LE, UTF-32BE, and UTF-32LE. The default
   * value is UTF-8. BigQuery decodes the data after the raw, binary data has
   * been split using the values of the `quote` and `fieldDelimiter` properties.
   * If you don't specify an encoding, or if you specify a UTF-8 encoding when
   * the CSV file is not UTF-8 encoded, BigQuery attempts to convert the data to
   * UTF-8. Generally, your data loads successfully, but it may not match byte-
   * for-byte what you expect. To avoid this, specify the correct encoding by
   * using the `--encoding` flag. If BigQuery can't convert a character other
   * than the ASCII `0` character, BigQuery converts the character to the
   * standard Unicode replacement character: �.
   *
   * @var string
   */
  public $encoding;
  /**
   * Optional. The separator character for fields in a CSV file. The separator
   * is interpreted as a single byte. For files encoded in ISO-8859-1, any
   * single character can be used as a separator. For files encoded in UTF-8,
   * characters represented in decimal range 1-127 (U+0001-U+007F) can be used
   * without any modification. UTF-8 characters encoded with multiple bytes
   * (i.e. U+0080 and above) will have only the first byte used for separating
   * fields. The remaining bytes will be treated as a part of the field.
   * BigQuery also supports the escape sequence "\t" (U+0009) to specify a tab
   * separator. The default value is comma (",", U+002C).
   *
   * @var string
   */
  public $fieldDelimiter;
  /**
   * Optional. Specifies how source URIs are interpreted for constructing the
   * file set to load. By default, source URIs are expanded against the
   * underlying storage. You can also specify manifest files to control how the
   * file set is constructed. This option is only applicable to object storage
   * systems.
   *
   * @var string
   */
  public $fileSetSpecType;
  protected $hivePartitioningOptionsType = HivePartitioningOptions::class;
  protected $hivePartitioningOptionsDataType = '';
  /**
   * Optional. Indicates if BigQuery should allow extra values that are not
   * represented in the table schema. If true, the extra values are ignored. If
   * false, records with extra columns are treated as bad records, and if there
   * are too many bad records, an invalid error is returned in the job result.
   * The default value is false. The sourceFormat property determines what
   * BigQuery treats as an extra value: CSV: Trailing columns JSON: Named values
   * that don't match any column names in the table schema Avro, Parquet, ORC:
   * Fields in the file schema that don't exist in the table schema.
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
  /**
   * Optional. The maximum number of bad records that BigQuery can ignore when
   * running the job. If the number of bad records exceeds this value, an
   * invalid error is returned in the job result. The default value is 0, which
   * requires that all records are valid. This is only supported for CSV and
   * NEWLINE_DELIMITED_JSON file formats.
   *
   * @var int
   */
  public $maxBadRecords;
  /**
   * Optional. Specifies a string that represents a null value in a CSV file.
   * For example, if you specify "\N", BigQuery interprets "\N" as a null value
   * when loading a CSV file. The default value is the empty string. If you set
   * this property to a custom value, BigQuery throws an error if an empty
   * string is present for all data types except for STRING and BYTE. For STRING
   * and BYTE columns, BigQuery interprets the empty string as an empty value.
   *
   * @var string
   */
  public $nullMarker;
  /**
   * Optional. A list of strings represented as SQL NULL value in a CSV file.
   * null_marker and null_markers can't be set at the same time. If null_marker
   * is set, null_markers has to be not set. If null_markers is set, null_marker
   * has to be not set. If both null_marker and null_markers are set at the same
   * time, a user error would be thrown. Any strings listed in null_markers,
   * including empty string would be interpreted as SQL NULL. This applies to
   * all column types.
   *
   * @var string[]
   */
  public $nullMarkers;
  protected $parquetOptionsType = ParquetOptions::class;
  protected $parquetOptionsDataType = '';
  /**
   * Optional. When sourceFormat is set to "CSV", this indicates whether the
   * embedded ASCII control characters (the first 32 characters in the ASCII-
   * table, from '\x00' to '\x1F') are preserved.
   *
   * @var bool
   */
  public $preserveAsciiControlCharacters;
  /**
   * If sourceFormat is set to "DATASTORE_BACKUP", indicates which entity
   * properties to load into BigQuery from a Cloud Datastore backup. Property
   * names are case sensitive and must be top-level properties. If no properties
   * are specified, BigQuery loads all properties. If any named property isn't
   * found in the Cloud Datastore backup, an invalid error is returned in the
   * job result.
   *
   * @var string[]
   */
  public $projectionFields;
  /**
   * Optional. The value that is used to quote data sections in a CSV file.
   * BigQuery converts the string to ISO-8859-1 encoding, and then uses the
   * first byte of the encoded string to split the data in its raw, binary
   * state. The default value is a double-quote ('"'). If your data does not
   * contain quoted sections, set the property value to an empty string. If your
   * data contains quoted newline characters, you must also set the
   * allowQuotedNewlines property to true. To include the specific quote
   * character within a quoted value, precede it with an additional matching
   * quote character. For example, if you want to escape the default character '
   * " ', use ' "" '. @default "
   *
   * @var string
   */
  public $quote;
  protected $rangePartitioningType = RangePartitioning::class;
  protected $rangePartitioningDataType = '';
  /**
   * Optional. The user can provide a reference file with the reader schema.
   * This file is only loaded if it is part of source URIs, but is not loaded
   * otherwise. It is enabled for the following formats: AVRO, PARQUET, ORC.
   *
   * @var string
   */
  public $referenceFileSchemaUri;
  protected $schemaType = TableSchema::class;
  protected $schemaDataType = '';
  /**
   * [Deprecated] The inline schema. For CSV schemas, specify as
   * "Field1:Type1[,Field2:Type2]*". For example, "foo:STRING, bar:INTEGER,
   * baz:FLOAT".
   *
   * @var string
   */
  public $schemaInline;
  /**
   * [Deprecated] The format of the schemaInline property.
   *
   * @var string
   */
  public $schemaInlineFormat;
  /**
   * Allows the schema of the destination table to be updated as a side effect
   * of the load job if a schema is autodetected or supplied in the job
   * configuration. Schema update options are supported in three cases: when
   * writeDisposition is WRITE_APPEND; when writeDisposition is
   * WRITE_TRUNCATE_DATA; when writeDisposition is WRITE_TRUNCATE and the
   * destination table is a partition of a table, specified by partition
   * decorators. For normal tables, WRITE_TRUNCATE will always overwrite the
   * schema. One or more of the following values are specified: *
   * ALLOW_FIELD_ADDITION: allow adding a nullable field to the schema. *
   * ALLOW_FIELD_RELAXATION: allow relaxing a required field in the original
   * schema to nullable.
   *
   * @var string[]
   */
  public $schemaUpdateOptions;
  /**
   * Optional. The number of rows at the top of a CSV file that BigQuery will
   * skip when loading the data. The default value is 0. This property is useful
   * if you have header rows in the file that should be skipped. When autodetect
   * is on, the behavior is the following: * skipLeadingRows unspecified -
   * Autodetect tries to detect headers in the first row. If they are not
   * detected, the row is read as data. Otherwise data is read starting from the
   * second row. * skipLeadingRows is 0 - Instructs autodetect that there are no
   * headers and data should be read starting from the first row. *
   * skipLeadingRows = N > 0 - Autodetect skips N-1 rows and tries to detect
   * headers in row N. If headers are not detected, row N is just skipped.
   * Otherwise row N is used to extract column names for the detected schema.
   *
   * @var int
   */
  public $skipLeadingRows;
  /**
   * Optional. Controls the strategy used to match loaded columns to the schema.
   * If not set, a sensible default is chosen based on how the schema is
   * provided. If autodetect is used, then columns are matched by name.
   * Otherwise, columns are matched by position. This is done to keep the
   * behavior backward-compatible.
   *
   * @var string
   */
  public $sourceColumnMatch;
  /**
   * Optional. The format of the data files. For CSV files, specify "CSV". For
   * datastore backups, specify "DATASTORE_BACKUP". For newline-delimited JSON,
   * specify "NEWLINE_DELIMITED_JSON". For Avro, specify "AVRO". For parquet,
   * specify "PARQUET". For orc, specify "ORC". The default value is CSV.
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
   * backups: Exactly one URI can be specified. Also, the '*' wildcard character
   * is not allowed.
   *
   * @var string[]
   */
  public $sourceUris;
  /**
   * Optional. Date format used for parsing TIME values.
   *
   * @var string
   */
  public $timeFormat;
  protected $timePartitioningType = TimePartitioning::class;
  protected $timePartitioningDataType = '';
  /**
   * Optional. Default time zone that will apply when parsing timestamp values
   * that have no specific time zone.
   *
   * @var string
   */
  public $timeZone;
  /**
   * Optional. Date format used for parsing TIMESTAMP values.
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
   * Optional. If sourceFormat is set to "AVRO", indicates whether to interpret
   * logical types as the corresponding BigQuery data type (for example,
   * TIMESTAMP), instead of using the raw type (for example, INTEGER).
   *
   * @var bool
   */
  public $useAvroLogicalTypes;
  /**
   * Optional. Specifies the action that occurs if the destination table already
   * exists. The following values are supported: * WRITE_TRUNCATE: If the table
   * already exists, BigQuery overwrites the data, removes the constraints and
   * uses the schema from the load job. * WRITE_TRUNCATE_DATA: If the table
   * already exists, BigQuery overwrites the data, but keeps the constraints and
   * schema of the existing table. * WRITE_APPEND: If the table already exists,
   * BigQuery appends the data to the table. * WRITE_EMPTY: If the table already
   * exists and contains data, a 'duplicate' error is returned in the job
   * result. The default value is WRITE_APPEND. Each action is atomic and only
   * occurs if BigQuery is able to complete the job successfully. Creation,
   * truncation and append actions occur as one atomic update upon job
   * completion.
   *
   * @var string
   */
  public $writeDisposition;

  /**
   * Optional. Accept rows that are missing trailing optional columns. The
   * missing values are treated as nulls. If false, records with missing
   * trailing columns are treated as bad records, and if there are too many bad
   * records, an invalid error is returned in the job result. The default value
   * is false. Only applicable to CSV, ignored for other formats.
   *
   * @param bool $allowJaggedRows
   */
  public function setAllowJaggedRows($allowJaggedRows)
  {
    $this->allowJaggedRows = $allowJaggedRows;
  }
  /**
   * @return bool
   */
  public function getAllowJaggedRows()
  {
    return $this->allowJaggedRows;
  }
  /**
   * Indicates if BigQuery should allow quoted data sections that contain
   * newline characters in a CSV file. The default value is false.
   *
   * @param bool $allowQuotedNewlines
   */
  public function setAllowQuotedNewlines($allowQuotedNewlines)
  {
    $this->allowQuotedNewlines = $allowQuotedNewlines;
  }
  /**
   * @return bool
   */
  public function getAllowQuotedNewlines()
  {
    return $this->allowQuotedNewlines;
  }
  /**
   * Optional. Indicates if we should automatically infer the options and schema
   * for CSV and JSON sources.
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
   * Clustering specification for the destination table.
   *
   * @param Clustering $clustering
   */
  public function setClustering(Clustering $clustering)
  {
    $this->clustering = $clustering;
  }
  /**
   * @return Clustering
   */
  public function getClustering()
  {
    return $this->clustering;
  }
  /**
   * Optional. Character map supported for column names in CSV/Parquet loads.
   * Defaults to STRICT and can be overridden by Project Config Service. Using
   * this option with unsupporting load formats will result in an error.
   *
   * Accepted values: COLUMN_NAME_CHARACTER_MAP_UNSPECIFIED, STRICT, V1, V2
   *
   * @param self::COLUMN_NAME_CHARACTER_MAP_* $columnNameCharacterMap
   */
  public function setColumnNameCharacterMap($columnNameCharacterMap)
  {
    $this->columnNameCharacterMap = $columnNameCharacterMap;
  }
  /**
   * @return self::COLUMN_NAME_CHARACTER_MAP_*
   */
  public function getColumnNameCharacterMap()
  {
    return $this->columnNameCharacterMap;
  }
  /**
   * Optional. Connection properties which can modify the load job behavior.
   * Currently, only the 'session_id' connection property is supported, and is
   * used to resolve _SESSION appearing as the dataset id.
   *
   * @param ConnectionProperty[] $connectionProperties
   */
  public function setConnectionProperties($connectionProperties)
  {
    $this->connectionProperties = $connectionProperties;
  }
  /**
   * @return ConnectionProperty[]
   */
  public function getConnectionProperties()
  {
    return $this->connectionProperties;
  }
  /**
   * Optional. [Experimental] Configures the load job to copy files directly to
   * the destination BigLake managed table, bypassing file content reading and
   * rewriting. Copying files only is supported when all the following are true:
   * * `source_uris` are located in the same Cloud Storage location as the
   * destination table's `storage_uri` location. * `source_format` is `PARQUET`.
   * * `destination_table` is an existing BigLake managed table. The table's
   * schema does not have flexible column names. The table's columns do not have
   * type parameters other than precision and scale. * No options other than the
   * above are specified.
   *
   * @param bool $copyFilesOnly
   */
  public function setCopyFilesOnly($copyFilesOnly)
  {
    $this->copyFilesOnly = $copyFilesOnly;
  }
  /**
   * @return bool
   */
  public function getCopyFilesOnly()
  {
    return $this->copyFilesOnly;
  }
  /**
   * Optional. Specifies whether the job is allowed to create new tables. The
   * following values are supported: * CREATE_IF_NEEDED: If the table does not
   * exist, BigQuery creates the table. * CREATE_NEVER: The table must already
   * exist. If it does not, a 'notFound' error is returned in the job result.
   * The default value is CREATE_IF_NEEDED. Creation, truncation and append
   * actions occur as one atomic update upon job completion.
   *
   * @param string $createDisposition
   */
  public function setCreateDisposition($createDisposition)
  {
    $this->createDisposition = $createDisposition;
  }
  /**
   * @return string
   */
  public function getCreateDisposition()
  {
    return $this->createDisposition;
  }
  /**
   * Optional. If this property is true, the job creates a new session using a
   * randomly generated session_id. To continue using a created session with
   * subsequent queries, pass the existing session identifier as a
   * `ConnectionProperty` value. The session identifier is returned as part of
   * the `SessionInfo` message within the query statistics. The new session's
   * location will be set to `Job.JobReference.location` if it is present,
   * otherwise it's set to the default location based on existing routing logic.
   *
   * @param bool $createSession
   */
  public function setCreateSession($createSession)
  {
    $this->createSession = $createSession;
  }
  /**
   * @return bool
   */
  public function getCreateSession()
  {
    return $this->createSession;
  }
  /**
   * Optional. Date format used for parsing DATE values.
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
   * Optional. Date format used for parsing DATETIME values.
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
   * Custom encryption configuration (e.g., Cloud KMS keys)
   *
   * @param EncryptionConfiguration $destinationEncryptionConfiguration
   */
  public function setDestinationEncryptionConfiguration(EncryptionConfiguration $destinationEncryptionConfiguration)
  {
    $this->destinationEncryptionConfiguration = $destinationEncryptionConfiguration;
  }
  /**
   * @return EncryptionConfiguration
   */
  public function getDestinationEncryptionConfiguration()
  {
    return $this->destinationEncryptionConfiguration;
  }
  /**
   * [Required] The destination table to load the data into.
   *
   * @param TableReference $destinationTable
   */
  public function setDestinationTable(TableReference $destinationTable)
  {
    $this->destinationTable = $destinationTable;
  }
  /**
   * @return TableReference
   */
  public function getDestinationTable()
  {
    return $this->destinationTable;
  }
  /**
   * Optional. [Experimental] Properties with which to create the destination
   * table if it is new.
   *
   * @param DestinationTableProperties $destinationTableProperties
   */
  public function setDestinationTableProperties(DestinationTableProperties $destinationTableProperties)
  {
    $this->destinationTableProperties = $destinationTableProperties;
  }
  /**
   * @return DestinationTableProperties
   */
  public function getDestinationTableProperties()
  {
    return $this->destinationTableProperties;
  }
  /**
   * Optional. The character encoding of the data. The supported values are
   * UTF-8, ISO-8859-1, UTF-16BE, UTF-16LE, UTF-32BE, and UTF-32LE. The default
   * value is UTF-8. BigQuery decodes the data after the raw, binary data has
   * been split using the values of the `quote` and `fieldDelimiter` properties.
   * If you don't specify an encoding, or if you specify a UTF-8 encoding when
   * the CSV file is not UTF-8 encoded, BigQuery attempts to convert the data to
   * UTF-8. Generally, your data loads successfully, but it may not match byte-
   * for-byte what you expect. To avoid this, specify the correct encoding by
   * using the `--encoding` flag. If BigQuery can't convert a character other
   * than the ASCII `0` character, BigQuery converts the character to the
   * standard Unicode replacement character: �.
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Optional. The separator character for fields in a CSV file. The separator
   * is interpreted as a single byte. For files encoded in ISO-8859-1, any
   * single character can be used as a separator. For files encoded in UTF-8,
   * characters represented in decimal range 1-127 (U+0001-U+007F) can be used
   * without any modification. UTF-8 characters encoded with multiple bytes
   * (i.e. U+0080 and above) will have only the first byte used for separating
   * fields. The remaining bytes will be treated as a part of the field.
   * BigQuery also supports the escape sequence "\t" (U+0009) to specify a tab
   * separator. The default value is comma (",", U+002C).
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
   * Optional. Specifies how source URIs are interpreted for constructing the
   * file set to load. By default, source URIs are expanded against the
   * underlying storage. You can also specify manifest files to control how the
   * file set is constructed. This option is only applicable to object storage
   * systems.
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
   * that don't match any column names in the table schema Avro, Parquet, ORC:
   * Fields in the file schema that don't exist in the table schema.
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
   * Optional. The maximum number of bad records that BigQuery can ignore when
   * running the job. If the number of bad records exceeds this value, an
   * invalid error is returned in the job result. The default value is 0, which
   * requires that all records are valid. This is only supported for CSV and
   * NEWLINE_DELIMITED_JSON file formats.
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
   * Optional. Specifies a string that represents a null value in a CSV file.
   * For example, if you specify "\N", BigQuery interprets "\N" as a null value
   * when loading a CSV file. The default value is the empty string. If you set
   * this property to a custom value, BigQuery throws an error if an empty
   * string is present for all data types except for STRING and BYTE. For STRING
   * and BYTE columns, BigQuery interprets the empty string as an empty value.
   *
   * @param string $nullMarker
   */
  public function setNullMarker($nullMarker)
  {
    $this->nullMarker = $nullMarker;
  }
  /**
   * @return string
   */
  public function getNullMarker()
  {
    return $this->nullMarker;
  }
  /**
   * Optional. A list of strings represented as SQL NULL value in a CSV file.
   * null_marker and null_markers can't be set at the same time. If null_marker
   * is set, null_markers has to be not set. If null_markers is set, null_marker
   * has to be not set. If both null_marker and null_markers are set at the same
   * time, a user error would be thrown. Any strings listed in null_markers,
   * including empty string would be interpreted as SQL NULL. This applies to
   * all column types.
   *
   * @param string[] $nullMarkers
   */
  public function setNullMarkers($nullMarkers)
  {
    $this->nullMarkers = $nullMarkers;
  }
  /**
   * @return string[]
   */
  public function getNullMarkers()
  {
    return $this->nullMarkers;
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
   * Optional. When sourceFormat is set to "CSV", this indicates whether the
   * embedded ASCII control characters (the first 32 characters in the ASCII-
   * table, from '\x00' to '\x1F') are preserved.
   *
   * @param bool $preserveAsciiControlCharacters
   */
  public function setPreserveAsciiControlCharacters($preserveAsciiControlCharacters)
  {
    $this->preserveAsciiControlCharacters = $preserveAsciiControlCharacters;
  }
  /**
   * @return bool
   */
  public function getPreserveAsciiControlCharacters()
  {
    return $this->preserveAsciiControlCharacters;
  }
  /**
   * If sourceFormat is set to "DATASTORE_BACKUP", indicates which entity
   * properties to load into BigQuery from a Cloud Datastore backup. Property
   * names are case sensitive and must be top-level properties. If no properties
   * are specified, BigQuery loads all properties. If any named property isn't
   * found in the Cloud Datastore backup, an invalid error is returned in the
   * job result.
   *
   * @param string[] $projectionFields
   */
  public function setProjectionFields($projectionFields)
  {
    $this->projectionFields = $projectionFields;
  }
  /**
   * @return string[]
   */
  public function getProjectionFields()
  {
    return $this->projectionFields;
  }
  /**
   * Optional. The value that is used to quote data sections in a CSV file.
   * BigQuery converts the string to ISO-8859-1 encoding, and then uses the
   * first byte of the encoded string to split the data in its raw, binary
   * state. The default value is a double-quote ('"'). If your data does not
   * contain quoted sections, set the property value to an empty string. If your
   * data contains quoted newline characters, you must also set the
   * allowQuotedNewlines property to true. To include the specific quote
   * character within a quoted value, precede it with an additional matching
   * quote character. For example, if you want to escape the default character '
   * " ', use ' "" '. @default "
   *
   * @param string $quote
   */
  public function setQuote($quote)
  {
    $this->quote = $quote;
  }
  /**
   * @return string
   */
  public function getQuote()
  {
    return $this->quote;
  }
  /**
   * Range partitioning specification for the destination table. Only one of
   * timePartitioning and rangePartitioning should be specified.
   *
   * @param RangePartitioning $rangePartitioning
   */
  public function setRangePartitioning(RangePartitioning $rangePartitioning)
  {
    $this->rangePartitioning = $rangePartitioning;
  }
  /**
   * @return RangePartitioning
   */
  public function getRangePartitioning()
  {
    return $this->rangePartitioning;
  }
  /**
   * Optional. The user can provide a reference file with the reader schema.
   * This file is only loaded if it is part of source URIs, but is not loaded
   * otherwise. It is enabled for the following formats: AVRO, PARQUET, ORC.
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
   * Optional. The schema for the destination table. The schema can be omitted
   * if the destination table already exists, or if you're loading data from
   * Google Cloud Datastore.
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
   * [Deprecated] The inline schema. For CSV schemas, specify as
   * "Field1:Type1[,Field2:Type2]*". For example, "foo:STRING, bar:INTEGER,
   * baz:FLOAT".
   *
   * @param string $schemaInline
   */
  public function setSchemaInline($schemaInline)
  {
    $this->schemaInline = $schemaInline;
  }
  /**
   * @return string
   */
  public function getSchemaInline()
  {
    return $this->schemaInline;
  }
  /**
   * [Deprecated] The format of the schemaInline property.
   *
   * @param string $schemaInlineFormat
   */
  public function setSchemaInlineFormat($schemaInlineFormat)
  {
    $this->schemaInlineFormat = $schemaInlineFormat;
  }
  /**
   * @return string
   */
  public function getSchemaInlineFormat()
  {
    return $this->schemaInlineFormat;
  }
  /**
   * Allows the schema of the destination table to be updated as a side effect
   * of the load job if a schema is autodetected or supplied in the job
   * configuration. Schema update options are supported in three cases: when
   * writeDisposition is WRITE_APPEND; when writeDisposition is
   * WRITE_TRUNCATE_DATA; when writeDisposition is WRITE_TRUNCATE and the
   * destination table is a partition of a table, specified by partition
   * decorators. For normal tables, WRITE_TRUNCATE will always overwrite the
   * schema. One or more of the following values are specified: *
   * ALLOW_FIELD_ADDITION: allow adding a nullable field to the schema. *
   * ALLOW_FIELD_RELAXATION: allow relaxing a required field in the original
   * schema to nullable.
   *
   * @param string[] $schemaUpdateOptions
   */
  public function setSchemaUpdateOptions($schemaUpdateOptions)
  {
    $this->schemaUpdateOptions = $schemaUpdateOptions;
  }
  /**
   * @return string[]
   */
  public function getSchemaUpdateOptions()
  {
    return $this->schemaUpdateOptions;
  }
  /**
   * Optional. The number of rows at the top of a CSV file that BigQuery will
   * skip when loading the data. The default value is 0. This property is useful
   * if you have header rows in the file that should be skipped. When autodetect
   * is on, the behavior is the following: * skipLeadingRows unspecified -
   * Autodetect tries to detect headers in the first row. If they are not
   * detected, the row is read as data. Otherwise data is read starting from the
   * second row. * skipLeadingRows is 0 - Instructs autodetect that there are no
   * headers and data should be read starting from the first row. *
   * skipLeadingRows = N > 0 - Autodetect skips N-1 rows and tries to detect
   * headers in row N. If headers are not detected, row N is just skipped.
   * Otherwise row N is used to extract column names for the detected schema.
   *
   * @param int $skipLeadingRows
   */
  public function setSkipLeadingRows($skipLeadingRows)
  {
    $this->skipLeadingRows = $skipLeadingRows;
  }
  /**
   * @return int
   */
  public function getSkipLeadingRows()
  {
    return $this->skipLeadingRows;
  }
  /**
   * Optional. Controls the strategy used to match loaded columns to the schema.
   * If not set, a sensible default is chosen based on how the schema is
   * provided. If autodetect is used, then columns are matched by name.
   * Otherwise, columns are matched by position. This is done to keep the
   * behavior backward-compatible.
   *
   * Accepted values: SOURCE_COLUMN_MATCH_UNSPECIFIED, POSITION, NAME
   *
   * @param self::SOURCE_COLUMN_MATCH_* $sourceColumnMatch
   */
  public function setSourceColumnMatch($sourceColumnMatch)
  {
    $this->sourceColumnMatch = $sourceColumnMatch;
  }
  /**
   * @return self::SOURCE_COLUMN_MATCH_*
   */
  public function getSourceColumnMatch()
  {
    return $this->sourceColumnMatch;
  }
  /**
   * Optional. The format of the data files. For CSV files, specify "CSV". For
   * datastore backups, specify "DATASTORE_BACKUP". For newline-delimited JSON,
   * specify "NEWLINE_DELIMITED_JSON". For Avro, specify "AVRO". For parquet,
   * specify "PARQUET". For orc, specify "ORC". The default value is CSV.
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
   * backups: Exactly one URI can be specified. Also, the '*' wildcard character
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
   * Optional. Date format used for parsing TIME values.
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
   * Time-based partitioning specification for the destination table. Only one
   * of timePartitioning and rangePartitioning should be specified.
   *
   * @param TimePartitioning $timePartitioning
   */
  public function setTimePartitioning(TimePartitioning $timePartitioning)
  {
    $this->timePartitioning = $timePartitioning;
  }
  /**
   * @return TimePartitioning
   */
  public function getTimePartitioning()
  {
    return $this->timePartitioning;
  }
  /**
   * Optional. Default time zone that will apply when parsing timestamp values
   * that have no specific time zone.
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
   * Optional. Date format used for parsing TIMESTAMP values.
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
  /**
   * Optional. If sourceFormat is set to "AVRO", indicates whether to interpret
   * logical types as the corresponding BigQuery data type (for example,
   * TIMESTAMP), instead of using the raw type (for example, INTEGER).
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
  /**
   * Optional. Specifies the action that occurs if the destination table already
   * exists. The following values are supported: * WRITE_TRUNCATE: If the table
   * already exists, BigQuery overwrites the data, removes the constraints and
   * uses the schema from the load job. * WRITE_TRUNCATE_DATA: If the table
   * already exists, BigQuery overwrites the data, but keeps the constraints and
   * schema of the existing table. * WRITE_APPEND: If the table already exists,
   * BigQuery appends the data to the table. * WRITE_EMPTY: If the table already
   * exists and contains data, a 'duplicate' error is returned in the job
   * result. The default value is WRITE_APPEND. Each action is atomic and only
   * occurs if BigQuery is able to complete the job successfully. Creation,
   * truncation and append actions occur as one atomic update upon job
   * completion.
   *
   * @param string $writeDisposition
   */
  public function setWriteDisposition($writeDisposition)
  {
    $this->writeDisposition = $writeDisposition;
  }
  /**
   * @return string
   */
  public function getWriteDisposition()
  {
    return $this->writeDisposition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobConfigurationLoad::class, 'Google_Service_Bigquery_JobConfigurationLoad');
