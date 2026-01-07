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

namespace Google\Service\CloudComposer;

class SoftwareConfig extends \Google\Model
{
  /**
   * Default mode.
   */
  public const WEB_SERVER_PLUGINS_MODE_WEB_SERVER_PLUGINS_MODE_UNSPECIFIED = 'WEB_SERVER_PLUGINS_MODE_UNSPECIFIED';
  /**
   * Web server plugins are not supported.
   */
  public const WEB_SERVER_PLUGINS_MODE_PLUGINS_DISABLED = 'PLUGINS_DISABLED';
  /**
   * Web server plugins are supported.
   */
  public const WEB_SERVER_PLUGINS_MODE_PLUGINS_ENABLED = 'PLUGINS_ENABLED';
  /**
   * Optional. Apache Airflow configuration properties to override. Property
   * keys contain the section and property names, separated by a hyphen, for
   * example "core-dags_are_paused_at_creation". Section names must not contain
   * hyphens ("-"), opening square brackets ("["), or closing square brackets
   * ("]"). The property name must not be empty and must not contain an equals
   * sign ("=") or semicolon (";"). Section and property names must not contain
   * a period ("."). Apache Airflow configuration property names must be written
   * in [snake_case](https://en.wikipedia.org/wiki/Snake_case). Property values
   * can contain any character, and can be written in any lower/upper case
   * format. Certain Apache Airflow configuration property values are
   * [blocked](/composer/docs/concepts/airflow-configurations), and cannot be
   * overridden.
   *
   * @var string[]
   */
  public $airflowConfigOverrides;
  protected $cloudDataLineageIntegrationType = CloudDataLineageIntegration::class;
  protected $cloudDataLineageIntegrationDataType = '';
  /**
   * Optional. Additional environment variables to provide to the Apache Airflow
   * scheduler, worker, and webserver processes. Environment variable names must
   * match the regular expression `a-zA-Z_*`. They cannot specify Apache Airflow
   * software configuration overrides (they cannot match the regular expression
   * `AIRFLOW__[A-Z0-9_]+__[A-Z0-9_]+`), and they cannot match any of the
   * following reserved names: * `AIRFLOW_HOME` * `C_FORCE_ROOT` *
   * `CONTAINER_NAME` * `DAGS_FOLDER` * `GCP_PROJECT` * `GCS_BUCKET` *
   * `GKE_CLUSTER_NAME` * `SQL_DATABASE` * `SQL_INSTANCE` * `SQL_PASSWORD` *
   * `SQL_PROJECT` * `SQL_REGION` * `SQL_USER`
   *
   * @var string[]
   */
  public $envVariables;
  /**
   * Optional. The version of the software running in the environment. This
   * encapsulates both the version of Cloud Composer functionality and the
   * version of Apache Airflow. It must match the regular expression `composer-
   * ([0-9]+(\.[0-9]+\.[0-9]+(-preview\.[0-9]+)?)?|latest)-airflow-([0-9]+(\.[0-
   * 9]+(\.[0-9]+)?)?)`. When used as input, the server also checks if the
   * provided version is supported and denies the request for an unsupported
   * version. The Cloud Composer portion of the image version is a full
   * [semantic version](https://semver.org), or an alias in the form of major
   * version number or `latest`. When an alias is provided, the server replaces
   * it with the current Cloud Composer version that satisfies the alias. The
   * Apache Airflow portion of the image version is a full semantic version that
   * points to one of the supported Apache Airflow versions, or an alias in the
   * form of only major or major.minor versions specified. When an alias is
   * provided, the server replaces it with the latest Apache Airflow version
   * that satisfies the alias and is supported in the given Cloud Composer
   * version. In all cases, the resolved image version is stored in the same
   * field. See also [version list](/composer/docs/concepts/versioning/composer-
   * versions) and [versioning
   * overview](/composer/docs/concepts/versioning/composer-versioning-overview).
   *
   * @var string
   */
  public $imageVersion;
  /**
   * Optional. Custom Python Package Index (PyPI) packages to be installed in
   * the environment. Keys refer to the lowercase package name such as "numpy"
   * and values are the lowercase extras and version specifier such as
   * "==1.12.0", "[devel,gcp_api]", or "[devel]>=1.8.2, <1.9.2". To specify a
   * package without pinning it to a version specifier, use the empty string as
   * the value.
   *
   * @var string[]
   */
  public $pypiPackages;
  /**
   * Optional. The major version of Python used to run the Apache Airflow
   * scheduler, worker, and webserver processes. Can be set to '2' or '3'. If
   * not specified, the default is '3'. Cannot be updated. This field is only
   * supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*. Environments in newer versions always use
   * Python major version 3.
   *
   * @var string
   */
  public $pythonVersion;
  /**
   * Optional. The number of schedulers for Airflow. This field is supported for
   * Cloud Composer environments in versions composer-1.*.*-airflow-2.*.*.
   *
   * @var int
   */
  public $schedulerCount;
  /**
   * Optional. Whether or not the web server uses custom plugins. If
   * unspecified, the field defaults to `PLUGINS_ENABLED`. This field is
   * supported for Cloud Composer environments in versions
   * composer-3-airflow-*.*.*-build.* and newer.
   *
   * @var string
   */
  public $webServerPluginsMode;

  /**
   * Optional. Apache Airflow configuration properties to override. Property
   * keys contain the section and property names, separated by a hyphen, for
   * example "core-dags_are_paused_at_creation". Section names must not contain
   * hyphens ("-"), opening square brackets ("["), or closing square brackets
   * ("]"). The property name must not be empty and must not contain an equals
   * sign ("=") or semicolon (";"). Section and property names must not contain
   * a period ("."). Apache Airflow configuration property names must be written
   * in [snake_case](https://en.wikipedia.org/wiki/Snake_case). Property values
   * can contain any character, and can be written in any lower/upper case
   * format. Certain Apache Airflow configuration property values are
   * [blocked](/composer/docs/concepts/airflow-configurations), and cannot be
   * overridden.
   *
   * @param string[] $airflowConfigOverrides
   */
  public function setAirflowConfigOverrides($airflowConfigOverrides)
  {
    $this->airflowConfigOverrides = $airflowConfigOverrides;
  }
  /**
   * @return string[]
   */
  public function getAirflowConfigOverrides()
  {
    return $this->airflowConfigOverrides;
  }
  /**
   * Optional. The configuration for Cloud Data Lineage integration.
   *
   * @param CloudDataLineageIntegration $cloudDataLineageIntegration
   */
  public function setCloudDataLineageIntegration(CloudDataLineageIntegration $cloudDataLineageIntegration)
  {
    $this->cloudDataLineageIntegration = $cloudDataLineageIntegration;
  }
  /**
   * @return CloudDataLineageIntegration
   */
  public function getCloudDataLineageIntegration()
  {
    return $this->cloudDataLineageIntegration;
  }
  /**
   * Optional. Additional environment variables to provide to the Apache Airflow
   * scheduler, worker, and webserver processes. Environment variable names must
   * match the regular expression `a-zA-Z_*`. They cannot specify Apache Airflow
   * software configuration overrides (they cannot match the regular expression
   * `AIRFLOW__[A-Z0-9_]+__[A-Z0-9_]+`), and they cannot match any of the
   * following reserved names: * `AIRFLOW_HOME` * `C_FORCE_ROOT` *
   * `CONTAINER_NAME` * `DAGS_FOLDER` * `GCP_PROJECT` * `GCS_BUCKET` *
   * `GKE_CLUSTER_NAME` * `SQL_DATABASE` * `SQL_INSTANCE` * `SQL_PASSWORD` *
   * `SQL_PROJECT` * `SQL_REGION` * `SQL_USER`
   *
   * @param string[] $envVariables
   */
  public function setEnvVariables($envVariables)
  {
    $this->envVariables = $envVariables;
  }
  /**
   * @return string[]
   */
  public function getEnvVariables()
  {
    return $this->envVariables;
  }
  /**
   * Optional. The version of the software running in the environment. This
   * encapsulates both the version of Cloud Composer functionality and the
   * version of Apache Airflow. It must match the regular expression `composer-
   * ([0-9]+(\.[0-9]+\.[0-9]+(-preview\.[0-9]+)?)?|latest)-airflow-([0-9]+(\.[0-
   * 9]+(\.[0-9]+)?)?)`. When used as input, the server also checks if the
   * provided version is supported and denies the request for an unsupported
   * version. The Cloud Composer portion of the image version is a full
   * [semantic version](https://semver.org), or an alias in the form of major
   * version number or `latest`. When an alias is provided, the server replaces
   * it with the current Cloud Composer version that satisfies the alias. The
   * Apache Airflow portion of the image version is a full semantic version that
   * points to one of the supported Apache Airflow versions, or an alias in the
   * form of only major or major.minor versions specified. When an alias is
   * provided, the server replaces it with the latest Apache Airflow version
   * that satisfies the alias and is supported in the given Cloud Composer
   * version. In all cases, the resolved image version is stored in the same
   * field. See also [version list](/composer/docs/concepts/versioning/composer-
   * versions) and [versioning
   * overview](/composer/docs/concepts/versioning/composer-versioning-overview).
   *
   * @param string $imageVersion
   */
  public function setImageVersion($imageVersion)
  {
    $this->imageVersion = $imageVersion;
  }
  /**
   * @return string
   */
  public function getImageVersion()
  {
    return $this->imageVersion;
  }
  /**
   * Optional. Custom Python Package Index (PyPI) packages to be installed in
   * the environment. Keys refer to the lowercase package name such as "numpy"
   * and values are the lowercase extras and version specifier such as
   * "==1.12.0", "[devel,gcp_api]", or "[devel]>=1.8.2, <1.9.2". To specify a
   * package without pinning it to a version specifier, use the empty string as
   * the value.
   *
   * @param string[] $pypiPackages
   */
  public function setPypiPackages($pypiPackages)
  {
    $this->pypiPackages = $pypiPackages;
  }
  /**
   * @return string[]
   */
  public function getPypiPackages()
  {
    return $this->pypiPackages;
  }
  /**
   * Optional. The major version of Python used to run the Apache Airflow
   * scheduler, worker, and webserver processes. Can be set to '2' or '3'. If
   * not specified, the default is '3'. Cannot be updated. This field is only
   * supported for Cloud Composer environments in versions
   * composer-1.*.*-airflow-*.*.*. Environments in newer versions always use
   * Python major version 3.
   *
   * @param string $pythonVersion
   */
  public function setPythonVersion($pythonVersion)
  {
    $this->pythonVersion = $pythonVersion;
  }
  /**
   * @return string
   */
  public function getPythonVersion()
  {
    return $this->pythonVersion;
  }
  /**
   * Optional. The number of schedulers for Airflow. This field is supported for
   * Cloud Composer environments in versions composer-1.*.*-airflow-2.*.*.
   *
   * @param int $schedulerCount
   */
  public function setSchedulerCount($schedulerCount)
  {
    $this->schedulerCount = $schedulerCount;
  }
  /**
   * @return int
   */
  public function getSchedulerCount()
  {
    return $this->schedulerCount;
  }
  /**
   * Optional. Whether or not the web server uses custom plugins. If
   * unspecified, the field defaults to `PLUGINS_ENABLED`. This field is
   * supported for Cloud Composer environments in versions
   * composer-3-airflow-*.*.*-build.* and newer.
   *
   * Accepted values: WEB_SERVER_PLUGINS_MODE_UNSPECIFIED, PLUGINS_DISABLED,
   * PLUGINS_ENABLED
   *
   * @param self::WEB_SERVER_PLUGINS_MODE_* $webServerPluginsMode
   */
  public function setWebServerPluginsMode($webServerPluginsMode)
  {
    $this->webServerPluginsMode = $webServerPluginsMode;
  }
  /**
   * @return self::WEB_SERVER_PLUGINS_MODE_*
   */
  public function getWebServerPluginsMode()
  {
    return $this->webServerPluginsMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SoftwareConfig::class, 'Google_Service_CloudComposer_SoftwareConfig');
