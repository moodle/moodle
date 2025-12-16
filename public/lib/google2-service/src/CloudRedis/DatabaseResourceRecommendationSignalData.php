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

namespace Google\Service\CloudRedis;

class DatabaseResourceRecommendationSignalData extends \Google\Model
{
  public const RECOMMENDATION_STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Recommendation is active and can be applied. ACTIVE recommendations can be
   * marked as CLAIMED, SUCCEEDED, or FAILED.
   */
  public const RECOMMENDATION_STATE_ACTIVE = 'ACTIVE';
  /**
   * Recommendation is in claimed state. Recommendations content is immutable
   * and cannot be updated by Google. CLAIMED recommendations can be marked as
   * CLAIMED, SUCCEEDED, or FAILED.
   */
  public const RECOMMENDATION_STATE_CLAIMED = 'CLAIMED';
  /**
   * Recommendation is in succeeded state. Recommendations content is immutable
   * and cannot be updated by Google. SUCCEEDED recommendations can be marked as
   * SUCCEEDED, or FAILED.
   */
  public const RECOMMENDATION_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Recommendation is in failed state. Recommendations content is immutable and
   * cannot be updated by Google. FAILED recommendations can be marked as
   * SUCCEEDED, or FAILED.
   */
  public const RECOMMENDATION_STATE_FAILED = 'FAILED';
  /**
   * Recommendation is in dismissed state. Recommendation content can be updated
   * by Google. DISMISSED recommendations can be marked as ACTIVE.
   */
  public const RECOMMENDATION_STATE_DISMISSED = 'DISMISSED';
  /**
   * Unspecified.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_UNSPECIFIED = 'SIGNAL_TYPE_UNSPECIFIED';
  /**
   * Represents if a resource is protected by automatic failover. Checks for
   * resources that are configured to have redundancy within a region that
   * enables automatic failover.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NOT_PROTECTED_BY_AUTOMATIC_FAILOVER = 'SIGNAL_TYPE_NOT_PROTECTED_BY_AUTOMATIC_FAILOVER';
  /**
   * Represents if a group is replicating across regions. Checks for resources
   * that are configured to have redundancy, and ongoing replication, across
   * regions.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_GROUP_NOT_REPLICATING_ACROSS_REGIONS = 'SIGNAL_TYPE_GROUP_NOT_REPLICATING_ACROSS_REGIONS';
  /**
   * Represents if the resource is available in multiple zones or not.
   *
   * @deprecated
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NOT_AVAILABLE_IN_MULTIPLE_ZONES = 'SIGNAL_TYPE_NOT_AVAILABLE_IN_MULTIPLE_ZONES';
  /**
   * Represents if a resource is available in multiple regions.
   *
   * @deprecated
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NOT_AVAILABLE_IN_MULTIPLE_REGIONS = 'SIGNAL_TYPE_NOT_AVAILABLE_IN_MULTIPLE_REGIONS';
  /**
   * Represents if a resource has a promotable replica.
   *
   * @deprecated
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_PROMOTABLE_REPLICA = 'SIGNAL_TYPE_NO_PROMOTABLE_REPLICA';
  /**
   * Represents if a resource has an automated backup policy.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_AUTOMATED_BACKUP_POLICY = 'SIGNAL_TYPE_NO_AUTOMATED_BACKUP_POLICY';
  /**
   * Represents if a resources has a short backup retention period.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_SHORT_BACKUP_RETENTION = 'SIGNAL_TYPE_SHORT_BACKUP_RETENTION';
  /**
   * Represents if the last backup of a resource failed.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LAST_BACKUP_FAILED = 'SIGNAL_TYPE_LAST_BACKUP_FAILED';
  /**
   * Represents if the last backup of a resource is older than some threshold
   * value.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LAST_BACKUP_OLD = 'SIGNAL_TYPE_LAST_BACKUP_OLD';
  /**
   * Represents if a resource violates CIS GCP Foundation 2.0.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_2_0 = 'SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_2_0';
  /**
   * Represents if a resource violates CIS GCP Foundation 1.3.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_3 = 'SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_3';
  /**
   * Represents if a resource violates CIS GCP Foundation 1.2.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_2 = 'SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_2';
  /**
   * Represents if a resource violates CIS GCP Foundation 1.1.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_1 = 'SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_1';
  /**
   * Represents if a resource violates CIS GCP Foundation 1.0.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_0 = 'SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_0';
  /**
   * Represents if a resource violates CIS Controls 8.0.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CIS_CONTROLS_V8_0 = 'SIGNAL_TYPE_VIOLATES_CIS_CONTROLS_V8_0';
  /**
   * Represents if a resource violates NIST 800-53.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_NIST_800_53 = 'SIGNAL_TYPE_VIOLATES_NIST_800_53';
  /**
   * Represents if a resource violates NIST 800-53 R5.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_NIST_800_53_R5 = 'SIGNAL_TYPE_VIOLATES_NIST_800_53_R5';
  /**
   * Represents if a resource violates NIST Cybersecurity Framework 1.0.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_NIST_CYBERSECURITY_FRAMEWORK_V1_0 = 'SIGNAL_TYPE_VIOLATES_NIST_CYBERSECURITY_FRAMEWORK_V1_0';
  /**
   * Represents if a resource violates ISO-27001.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_ISO_27001 = 'SIGNAL_TYPE_VIOLATES_ISO_27001';
  /**
   * Represents if a resource violates ISO 27001 2022.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_ISO_27001_V2022 = 'SIGNAL_TYPE_VIOLATES_ISO_27001_V2022';
  /**
   * Represents if a resource violates PCI-DSS v3.2.1.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_PCI_DSS_V3_2_1 = 'SIGNAL_TYPE_VIOLATES_PCI_DSS_V3_2_1';
  /**
   * Represents if a resource violates PCI-DSS v4.0.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_PCI_DSS_V4_0 = 'SIGNAL_TYPE_VIOLATES_PCI_DSS_V4_0';
  /**
   * Represents if a resource violates Cloud Controls Matrix v4.0.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_CLOUD_CONTROLS_MATRIX_V4 = 'SIGNAL_TYPE_VIOLATES_CLOUD_CONTROLS_MATRIX_V4';
  /**
   * Represents if a resource violates HIPAA.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_HIPAA = 'SIGNAL_TYPE_VIOLATES_HIPAA';
  /**
   * Represents if a resource violates SOC2 v2017.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATES_SOC2_V2017 = 'SIGNAL_TYPE_VIOLATES_SOC2_V2017';
  /**
   * Represents if log_checkpoints database flag for a Cloud SQL for PostgreSQL
   * instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LOGS_NOT_OPTIMIZED_FOR_TROUBLESHOOTING = 'SIGNAL_TYPE_LOGS_NOT_OPTIMIZED_FOR_TROUBLESHOOTING';
  /**
   * Represents if the log_duration database flag for a Cloud SQL for PostgreSQL
   * instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_QUERY_DURATIONS_NOT_LOGGED = 'SIGNAL_TYPE_QUERY_DURATIONS_NOT_LOGGED';
  /**
   * Represents if the log_error_verbosity database flag for a Cloud SQL for
   * PostgreSQL instance is not set to default or stricter (default or terse).
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VERBOSE_ERROR_LOGGING = 'SIGNAL_TYPE_VERBOSE_ERROR_LOGGING';
  /**
   * Represents if the log_lock_waits database flag for a Cloud SQL for
   * PostgreSQL instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_QUERY_LOCK_WAITS_NOT_LOGGED = 'SIGNAL_TYPE_QUERY_LOCK_WAITS_NOT_LOGGED';
  /**
   * Represents if the log_min_error_statement database flag for a Cloud SQL for
   * PostgreSQL instance is not set appropriately.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LOGGING_MOST_ERRORS = 'SIGNAL_TYPE_LOGGING_MOST_ERRORS';
  /**
   * Represents if the log_min_error_statement database flag for a Cloud SQL for
   * PostgreSQL instance does not have an appropriate severity level.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LOGGING_ONLY_CRITICAL_ERRORS = 'SIGNAL_TYPE_LOGGING_ONLY_CRITICAL_ERRORS';
  /**
   * Represents if the log_min_messages database flag for a Cloud SQL for
   * PostgreSQL instance is not set to warning or another recommended value.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_MINIMAL_ERROR_LOGGING = 'SIGNAL_TYPE_MINIMAL_ERROR_LOGGING';
  /**
   * Represents if the databaseFlags property of instance metadata for the
   * log_executor_status field is set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_QUERY_STATISTICS_LOGGED = 'SIGNAL_TYPE_QUERY_STATISTICS_LOGGED';
  /**
   * Represents if the log_hostname database flag for a Cloud SQL for PostgreSQL
   * instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_CLIENT_HOSTNAME = 'SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_CLIENT_HOSTNAME';
  /**
   * Represents if the log_parser_stats database flag for a Cloud SQL for
   * PostgreSQL instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_PARSER_STATISTICS = 'SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_PARSER_STATISTICS';
  /**
   * Represents if the log_planner_stats database flag for a Cloud SQL for
   * PostgreSQL instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_PLANNER_STATISTICS = 'SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_PLANNER_STATISTICS';
  /**
   * Represents if the log_statement database flag for a Cloud SQL for
   * PostgreSQL instance is not set to DDL (all data definition statements).
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NOT_LOGGING_ONLY_DDL_STATEMENTS = 'SIGNAL_TYPE_NOT_LOGGING_ONLY_DDL_STATEMENTS';
  /**
   * Represents if the log_statement_stats database flag for a Cloud SQL for
   * PostgreSQL instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LOGGING_QUERY_STATISTICS = 'SIGNAL_TYPE_LOGGING_QUERY_STATISTICS';
  /**
   * Represents if the log_temp_files database flag for a Cloud SQL for
   * PostgreSQL instance is not set to "0". (NOTE: 0 = ON)
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NOT_LOGGING_TEMPORARY_FILES = 'SIGNAL_TYPE_NOT_LOGGING_TEMPORARY_FILES';
  /**
   * Represents if the user connections database flag for a Cloud SQL for SQL
   * Server instance is configured.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_CONNECTION_MAX_NOT_CONFIGURED = 'SIGNAL_TYPE_CONNECTION_MAX_NOT_CONFIGURED';
  /**
   * Represents if the user options database flag for Cloud SQL SQL Server
   * instance is configured or not.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_USER_OPTIONS_CONFIGURED = 'SIGNAL_TYPE_USER_OPTIONS_CONFIGURED';
  /**
   * Represents if a resource is exposed to public access.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPOSED_TO_PUBLIC_ACCESS = 'SIGNAL_TYPE_EXPOSED_TO_PUBLIC_ACCESS';
  /**
   * Represents if a resources requires all incoming connections to use SSL or
   * not.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_UNENCRYPTED_CONNECTIONS = 'SIGNAL_TYPE_UNENCRYPTED_CONNECTIONS';
  /**
   * Represents if a Cloud SQL database has a password configured for the root
   * account or not.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_ROOT_PASSWORD = 'SIGNAL_TYPE_NO_ROOT_PASSWORD';
  /**
   * Represents if a Cloud SQL database has a weak password configured for the
   * root account.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_WEAK_ROOT_PASSWORD = 'SIGNAL_TYPE_WEAK_ROOT_PASSWORD';
  /**
   * Represents if a SQL database instance is not encrypted with customer-
   * managed encryption keys (CMEK).
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_ENCRYPTION_KEY_NOT_CUSTOMER_MANAGED = 'SIGNAL_TYPE_ENCRYPTION_KEY_NOT_CUSTOMER_MANAGED';
  /**
   * Represents if The contained database authentication database flag for a
   * Cloud SQL for SQL Server instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_SERVER_AUTHENTICATION_NOT_REQUIRED = 'SIGNAL_TYPE_SERVER_AUTHENTICATION_NOT_REQUIRED';
  /**
   * Represents if the cross_db_ownership_chaining database flag for a Cloud SQL
   * for SQL Server instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPOSED_BY_OWNERSHIP_CHAINING = 'SIGNAL_TYPE_EXPOSED_BY_OWNERSHIP_CHAINING';
  /**
   * Represents if he external scripts enabled database flag for a Cloud SQL for
   * SQL Server instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPOSED_TO_EXTERNAL_SCRIPTS = 'SIGNAL_TYPE_EXPOSED_TO_EXTERNAL_SCRIPTS';
  /**
   * Represents if the local_infile database flag for a Cloud SQL for MySQL
   * instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPOSED_TO_LOCAL_DATA_LOADS = 'SIGNAL_TYPE_EXPOSED_TO_LOCAL_DATA_LOADS';
  /**
   * Represents if the log_connections database flag for a Cloud SQL for
   * PostgreSQL instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_CONNECTION_ATTEMPTS_NOT_LOGGED = 'SIGNAL_TYPE_CONNECTION_ATTEMPTS_NOT_LOGGED';
  /**
   * Represents if the log_disconnections database flag for a Cloud SQL for
   * PostgreSQL instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DISCONNECTIONS_NOT_LOGGED = 'SIGNAL_TYPE_DISCONNECTIONS_NOT_LOGGED';
  /**
   * Represents if the log_min_duration_statement database flag for a Cloud SQL
   * for PostgreSQL instance is not set to -1.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LOGGING_EXCESSIVE_STATEMENT_INFO = 'SIGNAL_TYPE_LOGGING_EXCESSIVE_STATEMENT_INFO';
  /**
   * Represents if the remote access database flag for a Cloud SQL for SQL
   * Server instance is not set to off.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPOSED_TO_REMOTE_ACCESS = 'SIGNAL_TYPE_EXPOSED_TO_REMOTE_ACCESS';
  /**
   * Represents if the skip_show_database database flag for a Cloud SQL for
   * MySQL instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DATABASE_NAMES_EXPOSED = 'SIGNAL_TYPE_DATABASE_NAMES_EXPOSED';
  /**
   * Represents if the 3625 (trace flag) database flag for a Cloud SQL for SQL
   * Server instance is not set to on.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_SENSITIVE_TRACE_INFO_NOT_MASKED = 'SIGNAL_TYPE_SENSITIVE_TRACE_INFO_NOT_MASKED';
  /**
   * Represents if public IP is enabled.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_PUBLIC_IP_ENABLED = 'SIGNAL_TYPE_PUBLIC_IP_ENABLED';
  /**
   * Represents Idle instance helps to reduce costs.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_IDLE = 'SIGNAL_TYPE_IDLE';
  /**
   * Represents instances that are unnecessarily large for given workload.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_OVERPROVISIONED = 'SIGNAL_TYPE_OVERPROVISIONED';
  /**
   * Represents high number of concurrently opened tables.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HIGH_NUMBER_OF_OPEN_TABLES = 'SIGNAL_TYPE_HIGH_NUMBER_OF_OPEN_TABLES';
  /**
   * Represents high table count close to SLA limit.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HIGH_NUMBER_OF_TABLES = 'SIGNAL_TYPE_HIGH_NUMBER_OF_TABLES';
  /**
   * Represents high number of unvacuumed transactions
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HIGH_TRANSACTION_ID_UTILIZATION = 'SIGNAL_TYPE_HIGH_TRANSACTION_ID_UTILIZATION';
  /**
   * Represents need for more CPU and/or memory
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_UNDERPROVISIONED = 'SIGNAL_TYPE_UNDERPROVISIONED';
  /**
   * Represents out of disk.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_OUT_OF_DISK = 'SIGNAL_TYPE_OUT_OF_DISK';
  /**
   * Represents server certificate is near expiry.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_SERVER_CERTIFICATE_NEAR_EXPIRY = 'SIGNAL_TYPE_SERVER_CERTIFICATE_NEAR_EXPIRY';
  /**
   * Represents database auditing is disabled.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DATABASE_AUDITING_DISABLED = 'SIGNAL_TYPE_DATABASE_AUDITING_DISABLED';
  /**
   * Represents not restricted to authorized networks.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_RESTRICT_AUTHORIZED_NETWORKS = 'SIGNAL_TYPE_RESTRICT_AUTHORIZED_NETWORKS';
  /**
   * Represents violate org policy restrict public ip.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_VIOLATE_POLICY_RESTRICT_PUBLIC_IP = 'SIGNAL_TYPE_VIOLATE_POLICY_RESTRICT_PUBLIC_IP';
  /**
   * Cluster nearing quota limit
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_QUOTA_LIMIT = 'SIGNAL_TYPE_QUOTA_LIMIT';
  /**
   * No password policy set on resources
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_PASSWORD_POLICY = 'SIGNAL_TYPE_NO_PASSWORD_POLICY';
  /**
   * Performance impact of connections settings
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_CONNECTIONS_PERFORMANCE_IMPACT = 'SIGNAL_TYPE_CONNECTIONS_PERFORMANCE_IMPACT';
  /**
   * Performance impact of temporary tables settings
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_TMP_TABLES_PERFORMANCE_IMPACT = 'SIGNAL_TYPE_TMP_TABLES_PERFORMANCE_IMPACT';
  /**
   * Performance impact of transaction logs settings
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_TRANS_LOGS_PERFORMANCE_IMPACT = 'SIGNAL_TYPE_TRANS_LOGS_PERFORMANCE_IMPACT';
  /**
   * Performance impact of high joins without indexes
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HIGH_JOINS_WITHOUT_INDEXES = 'SIGNAL_TYPE_HIGH_JOINS_WITHOUT_INDEXES';
  /**
   * Detects events where a Cloud SQL superuser (postgres for PostgreSQL servers
   * or root for MySQL users) writes to non-system tables.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_SUPERUSER_WRITING_TO_USER_TABLES = 'SIGNAL_TYPE_SUPERUSER_WRITING_TO_USER_TABLES';
  /**
   * Detects events where a database user or role has been granted all
   * privileges to a database, or to all tables, procedures, or functions in a
   * schema.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_USER_GRANTED_ALL_PERMISSIONS = 'SIGNAL_TYPE_USER_GRANTED_ALL_PERMISSIONS';
  /**
   * Detects if database instance data exported to a Cloud Storage bucket
   * outside of the organization.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DATA_EXPORT_TO_EXTERNAL_CLOUD_STORAGE_BUCKET = 'SIGNAL_TYPE_DATA_EXPORT_TO_EXTERNAL_CLOUD_STORAGE_BUCKET';
  /**
   * Detects if database instance data exported to a Cloud Storage bucket that
   * is owned by the organization and is publicly accessible.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DATA_EXPORT_TO_PUBLIC_CLOUD_STORAGE_BUCKET = 'SIGNAL_TYPE_DATA_EXPORT_TO_PUBLIC_CLOUD_STORAGE_BUCKET';
  /**
   * Detects if a database instance is using a weak password hash algorithm.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_WEAK_PASSWORD_HASH_ALGORITHM = 'SIGNAL_TYPE_WEAK_PASSWORD_HASH_ALGORITHM';
  /**
   * Detects if a database instance has no user password policy set.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_USER_PASSWORD_POLICY = 'SIGNAL_TYPE_NO_USER_PASSWORD_POLICY';
  /**
   * Detects if a database instance/cluster has a hot node.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HOT_NODE = 'SIGNAL_TYPE_HOT_NODE';
  /**
   * Detects if a database instance has no point in time recovery enabled.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_POINT_IN_TIME_RECOVERY = 'SIGNAL_TYPE_NO_POINT_IN_TIME_RECOVERY';
  /**
   * Detects if a database instance/cluster is suspended.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_RESOURCE_SUSPENDED = 'SIGNAL_TYPE_RESOURCE_SUSPENDED';
  /**
   * Detects that expensive commands are being run on a database instance
   * impacting overall performance.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXPENSIVE_COMMANDS = 'SIGNAL_TYPE_EXPENSIVE_COMMANDS';
  /**
   * Indicates that the instance does not have a maintenance policy configured.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_MAINTENANCE_POLICY_CONFIGURED = 'SIGNAL_TYPE_NO_MAINTENANCE_POLICY_CONFIGURED';
  /**
   * Deletion Protection Disabled for the resource
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_NO_DELETION_PROTECTION = 'SIGNAL_TYPE_NO_DELETION_PROTECTION';
  /**
   * Indicates that the instance has inefficient queries detected.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_INEFFICIENT_QUERY = 'SIGNAL_TYPE_INEFFICIENT_QUERY';
  /**
   * Indicates that the instance has read intensive workload.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_READ_INTENSIVE_WORKLOAD = 'SIGNAL_TYPE_READ_INTENSIVE_WORKLOAD';
  /**
   * Indicates that the instance is nearing memory limit.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_MEMORY_LIMIT = 'SIGNAL_TYPE_MEMORY_LIMIT';
  /**
   * Indicates that the instance's max server memory is configured higher than
   * the recommended value.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_MAX_SERVER_MEMORY = 'SIGNAL_TYPE_MAX_SERVER_MEMORY';
  /**
   * Indicates that the database has large rows beyond the recommended limit.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LARGE_ROWS = 'SIGNAL_TYPE_LARGE_ROWS';
  /**
   * Heavy write pressure on the database rows.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HIGH_WRITE_PRESSURE = 'SIGNAL_TYPE_HIGH_WRITE_PRESSURE';
  /**
   * Heavy read pressure on the database rows.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_HIGH_READ_PRESSURE = 'SIGNAL_TYPE_HIGH_READ_PRESSURE';
  /**
   * Encryption org policy not satisfied.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_ENCRYPTION_ORG_POLICY_NOT_SATISFIED = 'SIGNAL_TYPE_ENCRYPTION_ORG_POLICY_NOT_SATISFIED';
  /**
   * Location org policy not satisfied.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_LOCATION_ORG_POLICY_NOT_SATISFIED = 'SIGNAL_TYPE_LOCATION_ORG_POLICY_NOT_SATISFIED';
  /**
   * Outdated DB minor version.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_OUTDATED_MINOR_VERSION = 'SIGNAL_TYPE_OUTDATED_MINOR_VERSION';
  /**
   * Schema not optimized.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_SCHEMA_NOT_OPTIMIZED = 'SIGNAL_TYPE_SCHEMA_NOT_OPTIMIZED';
  /**
   * High number of idle connections.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_MANY_IDLE_CONNECTIONS = 'SIGNAL_TYPE_MANY_IDLE_CONNECTIONS';
  /**
   * Replication delay.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_REPLICATION_LAG = 'SIGNAL_TYPE_REPLICATION_LAG';
  /**
   * Outdated version.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_OUTDATED_VERSION = 'SIGNAL_TYPE_OUTDATED_VERSION';
  /**
   * Outdated client.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_OUTDATED_CLIENT = 'SIGNAL_TYPE_OUTDATED_CLIENT';
  /**
   * Databoost is disabled.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_DATABOOST_DISABLED = 'SIGNAL_TYPE_DATABOOST_DISABLED';
  /**
   * Recommended maintenance policy.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_RECOMMENDED_MAINTENANCE_POLICIES = 'SIGNAL_TYPE_RECOMMENDED_MAINTENANCE_POLICIES';
  /**
   * Resource version is in extended support.
   */
  public const SIGNAL_TYPE_SIGNAL_TYPE_EXTENDED_SUPPORT = 'SIGNAL_TYPE_EXTENDED_SUPPORT';
  /**
   * Optional. Any other additional metadata specific to recommendation
   *
   * @var array[]
   */
  public $additionalMetadata;
  /**
   * Required. last time recommendationw as refreshed
   *
   * @var string
   */
  public $lastRefreshTime;
  /**
   * Required. Recommendation state
   *
   * @var string
   */
  public $recommendationState;
  /**
   * Required. Name of recommendation. Examples:
   * organizations/1234/locations/us-central1/recommenders/google.cloudsql.insta
   * nce.PerformanceRecommender/recommendations/9876
   *
   * @var string
   */
  public $recommender;
  /**
   * Required. ID of recommender. Examples:
   * "google.cloudsql.instance.PerformanceRecommender"
   *
   * @var string
   */
  public $recommenderId;
  /**
   * Required. Contains an identifier for a subtype of recommendations produced
   * for the same recommender. Subtype is a function of content and impact,
   * meaning a new subtype might be added when significant changes to `content`
   * or `primary_impact.category` are introduced. See the Recommenders section
   * to see a list of subtypes for a given Recommender. Examples: For
   * recommender = "google.cloudsql.instance.PerformanceRecommender",
   * recommender_subtype can be "MYSQL_HIGH_NUMBER_OF_OPEN_TABLES_BEST_PRACTICE"
   * /"POSTGRES_HIGH_TRANSACTION_ID_UTILIZATION_BEST_PRACTICE"
   *
   * @var string
   */
  public $recommenderSubtype;
  /**
   * Required. Database resource name associated with the signal. Resource name
   * to follow CAIS resource_name format as noted here go/condor-common-
   * datamodel
   *
   * @var string
   */
  public $resourceName;
  /**
   * Required. Type of signal, for example, `SIGNAL_TYPE_IDLE`,
   * `SIGNAL_TYPE_HIGH_NUMBER_OF_TABLES`, etc.
   *
   * @var string
   */
  public $signalType;

  /**
   * Optional. Any other additional metadata specific to recommendation
   *
   * @param array[] $additionalMetadata
   */
  public function setAdditionalMetadata($additionalMetadata)
  {
    $this->additionalMetadata = $additionalMetadata;
  }
  /**
   * @return array[]
   */
  public function getAdditionalMetadata()
  {
    return $this->additionalMetadata;
  }
  /**
   * Required. last time recommendationw as refreshed
   *
   * @param string $lastRefreshTime
   */
  public function setLastRefreshTime($lastRefreshTime)
  {
    $this->lastRefreshTime = $lastRefreshTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshTime()
  {
    return $this->lastRefreshTime;
  }
  /**
   * Required. Recommendation state
   *
   * Accepted values: UNSPECIFIED, ACTIVE, CLAIMED, SUCCEEDED, FAILED, DISMISSED
   *
   * @param self::RECOMMENDATION_STATE_* $recommendationState
   */
  public function setRecommendationState($recommendationState)
  {
    $this->recommendationState = $recommendationState;
  }
  /**
   * @return self::RECOMMENDATION_STATE_*
   */
  public function getRecommendationState()
  {
    return $this->recommendationState;
  }
  /**
   * Required. Name of recommendation. Examples:
   * organizations/1234/locations/us-central1/recommenders/google.cloudsql.insta
   * nce.PerformanceRecommender/recommendations/9876
   *
   * @param string $recommender
   */
  public function setRecommender($recommender)
  {
    $this->recommender = $recommender;
  }
  /**
   * @return string
   */
  public function getRecommender()
  {
    return $this->recommender;
  }
  /**
   * Required. ID of recommender. Examples:
   * "google.cloudsql.instance.PerformanceRecommender"
   *
   * @param string $recommenderId
   */
  public function setRecommenderId($recommenderId)
  {
    $this->recommenderId = $recommenderId;
  }
  /**
   * @return string
   */
  public function getRecommenderId()
  {
    return $this->recommenderId;
  }
  /**
   * Required. Contains an identifier for a subtype of recommendations produced
   * for the same recommender. Subtype is a function of content and impact,
   * meaning a new subtype might be added when significant changes to `content`
   * or `primary_impact.category` are introduced. See the Recommenders section
   * to see a list of subtypes for a given Recommender. Examples: For
   * recommender = "google.cloudsql.instance.PerformanceRecommender",
   * recommender_subtype can be "MYSQL_HIGH_NUMBER_OF_OPEN_TABLES_BEST_PRACTICE"
   * /"POSTGRES_HIGH_TRANSACTION_ID_UTILIZATION_BEST_PRACTICE"
   *
   * @param string $recommenderSubtype
   */
  public function setRecommenderSubtype($recommenderSubtype)
  {
    $this->recommenderSubtype = $recommenderSubtype;
  }
  /**
   * @return string
   */
  public function getRecommenderSubtype()
  {
    return $this->recommenderSubtype;
  }
  /**
   * Required. Database resource name associated with the signal. Resource name
   * to follow CAIS resource_name format as noted here go/condor-common-
   * datamodel
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Required. Type of signal, for example, `SIGNAL_TYPE_IDLE`,
   * `SIGNAL_TYPE_HIGH_NUMBER_OF_TABLES`, etc.
   *
   * Accepted values: SIGNAL_TYPE_UNSPECIFIED,
   * SIGNAL_TYPE_NOT_PROTECTED_BY_AUTOMATIC_FAILOVER,
   * SIGNAL_TYPE_GROUP_NOT_REPLICATING_ACROSS_REGIONS,
   * SIGNAL_TYPE_NOT_AVAILABLE_IN_MULTIPLE_ZONES,
   * SIGNAL_TYPE_NOT_AVAILABLE_IN_MULTIPLE_REGIONS,
   * SIGNAL_TYPE_NO_PROMOTABLE_REPLICA, SIGNAL_TYPE_NO_AUTOMATED_BACKUP_POLICY,
   * SIGNAL_TYPE_SHORT_BACKUP_RETENTION, SIGNAL_TYPE_LAST_BACKUP_FAILED,
   * SIGNAL_TYPE_LAST_BACKUP_OLD, SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_2_0,
   * SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_3,
   * SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_2,
   * SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_1,
   * SIGNAL_TYPE_VIOLATES_CIS_GCP_FOUNDATION_1_0,
   * SIGNAL_TYPE_VIOLATES_CIS_CONTROLS_V8_0, SIGNAL_TYPE_VIOLATES_NIST_800_53,
   * SIGNAL_TYPE_VIOLATES_NIST_800_53_R5,
   * SIGNAL_TYPE_VIOLATES_NIST_CYBERSECURITY_FRAMEWORK_V1_0,
   * SIGNAL_TYPE_VIOLATES_ISO_27001, SIGNAL_TYPE_VIOLATES_ISO_27001_V2022,
   * SIGNAL_TYPE_VIOLATES_PCI_DSS_V3_2_1, SIGNAL_TYPE_VIOLATES_PCI_DSS_V4_0,
   * SIGNAL_TYPE_VIOLATES_CLOUD_CONTROLS_MATRIX_V4, SIGNAL_TYPE_VIOLATES_HIPAA,
   * SIGNAL_TYPE_VIOLATES_SOC2_V2017,
   * SIGNAL_TYPE_LOGS_NOT_OPTIMIZED_FOR_TROUBLESHOOTING,
   * SIGNAL_TYPE_QUERY_DURATIONS_NOT_LOGGED, SIGNAL_TYPE_VERBOSE_ERROR_LOGGING,
   * SIGNAL_TYPE_QUERY_LOCK_WAITS_NOT_LOGGED, SIGNAL_TYPE_LOGGING_MOST_ERRORS,
   * SIGNAL_TYPE_LOGGING_ONLY_CRITICAL_ERRORS,
   * SIGNAL_TYPE_MINIMAL_ERROR_LOGGING, SIGNAL_TYPE_QUERY_STATISTICS_LOGGED,
   * SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_CLIENT_HOSTNAME,
   * SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_PARSER_STATISTICS,
   * SIGNAL_TYPE_EXCESSIVE_LOGGING_OF_PLANNER_STATISTICS,
   * SIGNAL_TYPE_NOT_LOGGING_ONLY_DDL_STATEMENTS,
   * SIGNAL_TYPE_LOGGING_QUERY_STATISTICS,
   * SIGNAL_TYPE_NOT_LOGGING_TEMPORARY_FILES,
   * SIGNAL_TYPE_CONNECTION_MAX_NOT_CONFIGURED,
   * SIGNAL_TYPE_USER_OPTIONS_CONFIGURED, SIGNAL_TYPE_EXPOSED_TO_PUBLIC_ACCESS,
   * SIGNAL_TYPE_UNENCRYPTED_CONNECTIONS, SIGNAL_TYPE_NO_ROOT_PASSWORD,
   * SIGNAL_TYPE_WEAK_ROOT_PASSWORD,
   * SIGNAL_TYPE_ENCRYPTION_KEY_NOT_CUSTOMER_MANAGED,
   * SIGNAL_TYPE_SERVER_AUTHENTICATION_NOT_REQUIRED,
   * SIGNAL_TYPE_EXPOSED_BY_OWNERSHIP_CHAINING,
   * SIGNAL_TYPE_EXPOSED_TO_EXTERNAL_SCRIPTS,
   * SIGNAL_TYPE_EXPOSED_TO_LOCAL_DATA_LOADS,
   * SIGNAL_TYPE_CONNECTION_ATTEMPTS_NOT_LOGGED,
   * SIGNAL_TYPE_DISCONNECTIONS_NOT_LOGGED,
   * SIGNAL_TYPE_LOGGING_EXCESSIVE_STATEMENT_INFO,
   * SIGNAL_TYPE_EXPOSED_TO_REMOTE_ACCESS, SIGNAL_TYPE_DATABASE_NAMES_EXPOSED,
   * SIGNAL_TYPE_SENSITIVE_TRACE_INFO_NOT_MASKED, SIGNAL_TYPE_PUBLIC_IP_ENABLED,
   * SIGNAL_TYPE_IDLE, SIGNAL_TYPE_OVERPROVISIONED,
   * SIGNAL_TYPE_HIGH_NUMBER_OF_OPEN_TABLES, SIGNAL_TYPE_HIGH_NUMBER_OF_TABLES,
   * SIGNAL_TYPE_HIGH_TRANSACTION_ID_UTILIZATION, SIGNAL_TYPE_UNDERPROVISIONED,
   * SIGNAL_TYPE_OUT_OF_DISK, SIGNAL_TYPE_SERVER_CERTIFICATE_NEAR_EXPIRY,
   * SIGNAL_TYPE_DATABASE_AUDITING_DISABLED,
   * SIGNAL_TYPE_RESTRICT_AUTHORIZED_NETWORKS,
   * SIGNAL_TYPE_VIOLATE_POLICY_RESTRICT_PUBLIC_IP, SIGNAL_TYPE_QUOTA_LIMIT,
   * SIGNAL_TYPE_NO_PASSWORD_POLICY, SIGNAL_TYPE_CONNECTIONS_PERFORMANCE_IMPACT,
   * SIGNAL_TYPE_TMP_TABLES_PERFORMANCE_IMPACT,
   * SIGNAL_TYPE_TRANS_LOGS_PERFORMANCE_IMPACT,
   * SIGNAL_TYPE_HIGH_JOINS_WITHOUT_INDEXES,
   * SIGNAL_TYPE_SUPERUSER_WRITING_TO_USER_TABLES,
   * SIGNAL_TYPE_USER_GRANTED_ALL_PERMISSIONS,
   * SIGNAL_TYPE_DATA_EXPORT_TO_EXTERNAL_CLOUD_STORAGE_BUCKET,
   * SIGNAL_TYPE_DATA_EXPORT_TO_PUBLIC_CLOUD_STORAGE_BUCKET,
   * SIGNAL_TYPE_WEAK_PASSWORD_HASH_ALGORITHM,
   * SIGNAL_TYPE_NO_USER_PASSWORD_POLICY, SIGNAL_TYPE_HOT_NODE,
   * SIGNAL_TYPE_NO_POINT_IN_TIME_RECOVERY, SIGNAL_TYPE_RESOURCE_SUSPENDED,
   * SIGNAL_TYPE_EXPENSIVE_COMMANDS,
   * SIGNAL_TYPE_NO_MAINTENANCE_POLICY_CONFIGURED,
   * SIGNAL_TYPE_NO_DELETION_PROTECTION, SIGNAL_TYPE_INEFFICIENT_QUERY,
   * SIGNAL_TYPE_READ_INTENSIVE_WORKLOAD, SIGNAL_TYPE_MEMORY_LIMIT,
   * SIGNAL_TYPE_MAX_SERVER_MEMORY, SIGNAL_TYPE_LARGE_ROWS,
   * SIGNAL_TYPE_HIGH_WRITE_PRESSURE, SIGNAL_TYPE_HIGH_READ_PRESSURE,
   * SIGNAL_TYPE_ENCRYPTION_ORG_POLICY_NOT_SATISFIED,
   * SIGNAL_TYPE_LOCATION_ORG_POLICY_NOT_SATISFIED,
   * SIGNAL_TYPE_OUTDATED_MINOR_VERSION, SIGNAL_TYPE_SCHEMA_NOT_OPTIMIZED,
   * SIGNAL_TYPE_MANY_IDLE_CONNECTIONS, SIGNAL_TYPE_REPLICATION_LAG,
   * SIGNAL_TYPE_OUTDATED_VERSION, SIGNAL_TYPE_OUTDATED_CLIENT,
   * SIGNAL_TYPE_DATABOOST_DISABLED,
   * SIGNAL_TYPE_RECOMMENDED_MAINTENANCE_POLICIES, SIGNAL_TYPE_EXTENDED_SUPPORT
   *
   * @param self::SIGNAL_TYPE_* $signalType
   */
  public function setSignalType($signalType)
  {
    $this->signalType = $signalType;
  }
  /**
   * @return self::SIGNAL_TYPE_*
   */
  public function getSignalType()
  {
    return $this->signalType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseResourceRecommendationSignalData::class, 'Google_Service_CloudRedis_DatabaseResourceRecommendationSignalData');
