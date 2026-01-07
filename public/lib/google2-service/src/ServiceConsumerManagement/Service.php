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

namespace Google\Service\ServiceConsumerManagement;

class Service extends \Google\Collection
{
  protected $collection_key = 'types';
  protected $apisType = Api::class;
  protected $apisDataType = 'array';
  protected $aspectsType = Aspect::class;
  protected $aspectsDataType = 'array';
  protected $authenticationType = Authentication::class;
  protected $authenticationDataType = '';
  protected $backendType = Backend::class;
  protected $backendDataType = '';
  protected $billingType = Billing::class;
  protected $billingDataType = '';
  /**
   * Obsolete. Do not use. This field has no semantic meaning. The service
   * config compiler always sets this field to `3`.
   *
   * @var string
   */
  public $configVersion;
  protected $contextType = Context::class;
  protected $contextDataType = '';
  protected $controlType = Control::class;
  protected $controlDataType = '';
  protected $customErrorType = CustomError::class;
  protected $customErrorDataType = '';
  protected $documentationType = Documentation::class;
  protected $documentationDataType = '';
  protected $endpointsType = Endpoint::class;
  protected $endpointsDataType = 'array';
  protected $enumsType = Enum::class;
  protected $enumsDataType = 'array';
  protected $httpType = Http::class;
  protected $httpDataType = '';
  /**
   * A unique ID for a specific instance of this message, typically assigned by
   * the client for tracking purpose. Must be no longer than 63 characters and
   * only lower case letters, digits, '.', '_' and '-' are allowed. If empty,
   * the server may choose to generate one instead.
   *
   * @var string
   */
  public $id;
  protected $loggingType = Logging::class;
  protected $loggingDataType = '';
  protected $logsType = LogDescriptor::class;
  protected $logsDataType = 'array';
  protected $metricsType = MetricDescriptor::class;
  protected $metricsDataType = 'array';
  protected $monitoredResourcesType = MonitoredResourceDescriptor::class;
  protected $monitoredResourcesDataType = 'array';
  protected $monitoringType = Monitoring::class;
  protected $monitoringDataType = '';
  /**
   * The service name, which is a DNS-like logical identifier for the service,
   * such as `calendar.googleapis.com`. The service name typically goes through
   * DNS verification to make sure the owner of the service also owns the DNS
   * name.
   *
   * @var string
   */
  public $name;
  /**
   * The Google project that owns this service.
   *
   * @var string
   */
  public $producerProjectId;
  protected $publishingType = Publishing::class;
  protected $publishingDataType = '';
  protected $quotaType = Quota::class;
  protected $quotaDataType = '';
  protected $sourceInfoType = SourceInfo::class;
  protected $sourceInfoDataType = '';
  protected $systemParametersType = SystemParameters::class;
  protected $systemParametersDataType = '';
  protected $systemTypesType = Type::class;
  protected $systemTypesDataType = 'array';
  /**
   * The product title for this service, it is the name displayed in Google
   * Cloud Console.
   *
   * @var string
   */
  public $title;
  protected $typesType = Type::class;
  protected $typesDataType = 'array';
  protected $usageType = Usage::class;
  protected $usageDataType = '';

  /**
   * A list of API interfaces exported by this service. Only the `name` field of
   * the google.protobuf.Api needs to be provided by the configuration author,
   * as the remaining fields will be derived from the IDL during the
   * normalization process. It is an error to specify an API interface here
   * which cannot be resolved against the associated IDL files.
   *
   * @param Api[] $apis
   */
  public function setApis($apis)
  {
    $this->apis = $apis;
  }
  /**
   * @return Api[]
   */
  public function getApis()
  {
    return $this->apis;
  }
  /**
   * Configuration aspects. This is a repeated field to allow multiple aspects
   * to be configured. The kind field in each ConfigAspect specifies the type of
   * aspect. The spec field contains the configuration for that aspect. The
   * schema for the spec field is defined by the backend service owners.
   *
   * @param Aspect[] $aspects
   */
  public function setAspects($aspects)
  {
    $this->aspects = $aspects;
  }
  /**
   * @return Aspect[]
   */
  public function getAspects()
  {
    return $this->aspects;
  }
  /**
   * Auth configuration.
   *
   * @param Authentication $authentication
   */
  public function setAuthentication(Authentication $authentication)
  {
    $this->authentication = $authentication;
  }
  /**
   * @return Authentication
   */
  public function getAuthentication()
  {
    return $this->authentication;
  }
  /**
   * API backend configuration.
   *
   * @param Backend $backend
   */
  public function setBackend(Backend $backend)
  {
    $this->backend = $backend;
  }
  /**
   * @return Backend
   */
  public function getBackend()
  {
    return $this->backend;
  }
  /**
   * Billing configuration.
   *
   * @param Billing $billing
   */
  public function setBilling(Billing $billing)
  {
    $this->billing = $billing;
  }
  /**
   * @return Billing
   */
  public function getBilling()
  {
    return $this->billing;
  }
  /**
   * Obsolete. Do not use. This field has no semantic meaning. The service
   * config compiler always sets this field to `3`.
   *
   * @param string $configVersion
   */
  public function setConfigVersion($configVersion)
  {
    $this->configVersion = $configVersion;
  }
  /**
   * @return string
   */
  public function getConfigVersion()
  {
    return $this->configVersion;
  }
  /**
   * Context configuration.
   *
   * @param Context $context
   */
  public function setContext(Context $context)
  {
    $this->context = $context;
  }
  /**
   * @return Context
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Configuration for the service control plane.
   *
   * @param Control $control
   */
  public function setControl(Control $control)
  {
    $this->control = $control;
  }
  /**
   * @return Control
   */
  public function getControl()
  {
    return $this->control;
  }
  /**
   * Custom error configuration.
   *
   * @param CustomError $customError
   */
  public function setCustomError(CustomError $customError)
  {
    $this->customError = $customError;
  }
  /**
   * @return CustomError
   */
  public function getCustomError()
  {
    return $this->customError;
  }
  /**
   * Additional API documentation.
   *
   * @param Documentation $documentation
   */
  public function setDocumentation(Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Configuration for network endpoints. If this is empty, then an endpoint
   * with the same name as the service is automatically generated to service all
   * defined APIs.
   *
   * @param Endpoint[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return Endpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * A list of all enum types included in this API service. Enums referenced
   * directly or indirectly by the `apis` are automatically included. Enums
   * which are not referenced but shall be included should be listed here by
   * name by the configuration author. Example: enums: - name:
   * google.someapi.v1.SomeEnum
   *
   * @param Enum[] $enums
   */
  public function setEnums($enums)
  {
    $this->enums = $enums;
  }
  /**
   * @return Enum[]
   */
  public function getEnums()
  {
    return $this->enums;
  }
  /**
   * HTTP configuration.
   *
   * @param Http $http
   */
  public function setHttp(Http $http)
  {
    $this->http = $http;
  }
  /**
   * @return Http
   */
  public function getHttp()
  {
    return $this->http;
  }
  /**
   * A unique ID for a specific instance of this message, typically assigned by
   * the client for tracking purpose. Must be no longer than 63 characters and
   * only lower case letters, digits, '.', '_' and '-' are allowed. If empty,
   * the server may choose to generate one instead.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Logging configuration.
   *
   * @param Logging $logging
   */
  public function setLogging(Logging $logging)
  {
    $this->logging = $logging;
  }
  /**
   * @return Logging
   */
  public function getLogging()
  {
    return $this->logging;
  }
  /**
   * Defines the logs used by this service.
   *
   * @param LogDescriptor[] $logs
   */
  public function setLogs($logs)
  {
    $this->logs = $logs;
  }
  /**
   * @return LogDescriptor[]
   */
  public function getLogs()
  {
    return $this->logs;
  }
  /**
   * Defines the metrics used by this service.
   *
   * @param MetricDescriptor[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return MetricDescriptor[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Defines the monitored resources used by this service. This is required by
   * the Service.monitoring and Service.logging configurations.
   *
   * @param MonitoredResourceDescriptor[] $monitoredResources
   */
  public function setMonitoredResources($monitoredResources)
  {
    $this->monitoredResources = $monitoredResources;
  }
  /**
   * @return MonitoredResourceDescriptor[]
   */
  public function getMonitoredResources()
  {
    return $this->monitoredResources;
  }
  /**
   * Monitoring configuration.
   *
   * @param Monitoring $monitoring
   */
  public function setMonitoring(Monitoring $monitoring)
  {
    $this->monitoring = $monitoring;
  }
  /**
   * @return Monitoring
   */
  public function getMonitoring()
  {
    return $this->monitoring;
  }
  /**
   * The service name, which is a DNS-like logical identifier for the service,
   * such as `calendar.googleapis.com`. The service name typically goes through
   * DNS verification to make sure the owner of the service also owns the DNS
   * name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The Google project that owns this service.
   *
   * @param string $producerProjectId
   */
  public function setProducerProjectId($producerProjectId)
  {
    $this->producerProjectId = $producerProjectId;
  }
  /**
   * @return string
   */
  public function getProducerProjectId()
  {
    return $this->producerProjectId;
  }
  /**
   * Settings for [Google Cloud Client
   * libraries](https://cloud.google.com/apis/docs/cloud-client-libraries)
   * generated from APIs defined as protocol buffers.
   *
   * @param Publishing $publishing
   */
  public function setPublishing(Publishing $publishing)
  {
    $this->publishing = $publishing;
  }
  /**
   * @return Publishing
   */
  public function getPublishing()
  {
    return $this->publishing;
  }
  /**
   * Quota configuration.
   *
   * @param Quota $quota
   */
  public function setQuota(Quota $quota)
  {
    $this->quota = $quota;
  }
  /**
   * @return Quota
   */
  public function getQuota()
  {
    return $this->quota;
  }
  /**
   * Output only. The source information for this configuration if available.
   *
   * @param SourceInfo $sourceInfo
   */
  public function setSourceInfo(SourceInfo $sourceInfo)
  {
    $this->sourceInfo = $sourceInfo;
  }
  /**
   * @return SourceInfo
   */
  public function getSourceInfo()
  {
    return $this->sourceInfo;
  }
  /**
   * System parameter configuration.
   *
   * @param SystemParameters $systemParameters
   */
  public function setSystemParameters(SystemParameters $systemParameters)
  {
    $this->systemParameters = $systemParameters;
  }
  /**
   * @return SystemParameters
   */
  public function getSystemParameters()
  {
    return $this->systemParameters;
  }
  /**
   * A list of all proto message types included in this API service. It serves
   * similar purpose as [google.api.Service.types], except that these types are
   * not needed by user-defined APIs. Therefore, they will not show up in the
   * generated discovery doc. This field should only be used to define system
   * APIs in ESF.
   *
   * @param Type[] $systemTypes
   */
  public function setSystemTypes($systemTypes)
  {
    $this->systemTypes = $systemTypes;
  }
  /**
   * @return Type[]
   */
  public function getSystemTypes()
  {
    return $this->systemTypes;
  }
  /**
   * The product title for this service, it is the name displayed in Google
   * Cloud Console.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * A list of all proto message types included in this API service. Types
   * referenced directly or indirectly by the `apis` are automatically included.
   * Messages which are not referenced but shall be included, such as types used
   * by the `google.protobuf.Any` type, should be listed here by name by the
   * configuration author. Example: types: - name: google.protobuf.Int32
   *
   * @param Type[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return Type[]
   */
  public function getTypes()
  {
    return $this->types;
  }
  /**
   * Configuration controlling usage of this service.
   *
   * @param Usage $usage
   */
  public function setUsage(Usage $usage)
  {
    $this->usage = $usage;
  }
  /**
   * @return Usage
   */
  public function getUsage()
  {
    return $this->usage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Service::class, 'Google_Service_ServiceConsumerManagement_Service');
