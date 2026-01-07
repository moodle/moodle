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

namespace Google\Service\AnalyticsData\Resource;

use Google\Service\AnalyticsData\AudienceExport;
use Google\Service\AnalyticsData\ListAudienceExportsResponse;
use Google\Service\AnalyticsData\Operation;
use Google\Service\AnalyticsData\QueryAudienceExportRequest;
use Google\Service\AnalyticsData\QueryAudienceExportResponse;

/**
 * The "audienceExports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $analyticsdataService = new Google\Service\AnalyticsData(...);
 *   $audienceExports = $analyticsdataService->properties_audienceExports;
 *  </code>
 */
class PropertiesAudienceExports extends \Google\Service\Resource
{
  /**
   * Creates an audience export for later retrieval. This method quickly returns
   * the audience export's resource name and initiates a long running asynchronous
   * request to form an audience export. To export the users in an audience
   * export, first create the audience export through this method and then send
   * the audience resource name to the `QueryAudienceExport` method. See [Creating
   * an Audience Export](https://developers.google.com/analytics/devguides/reporti
   * ng/data/v1/audience-list-basics) for an introduction to Audience Exports with
   * examples. An audience export is a snapshot of the users currently in the
   * audience at the time of audience export creation. Creating audience exports
   * for one audience on different days will return different results as users
   * enter and exit the audience. Audiences in Google Analytics 4 allow you to
   * segment your users in the ways that are important to your business. To learn
   * more, see https://support.google.com/analytics/answer/9267572. Audience
   * exports contain the users in each audience. Audience Export APIs have some
   * methods at alpha and other methods at beta stability. The intention is to
   * advance methods to beta stability after some feedback and adoption. To give
   * your feedback on this API, complete the [Google Analytics Audience Export API
   * Feedback](https://forms.gle/EeA5u5LW6PEggtCEA) form. (audienceExports.create)
   *
   * @param string $parent Required. The parent resource where this audience
   * export will be created. Format: `properties/{property}`
   * @param AudienceExport $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, AudienceExport $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Gets configuration metadata about a specific audience export. This method can
   * be used to understand an audience export after it has been created. See
   * [Creating an Audience Export](https://developers.google.com/analytics/devguid
   * es/reporting/data/v1/audience-list-basics) for an introduction to Audience
   * Exports with examples. Audience Export APIs have some methods at alpha and
   * other methods at beta stability. The intention is to advance methods to beta
   * stability after some feedback and adoption. To give your feedback on this
   * API, complete the [Google Analytics Audience Export API
   * Feedback](https://forms.gle/EeA5u5LW6PEggtCEA) form. (audienceExports.get)
   *
   * @param string $name Required. The audience export resource name. Format:
   * `properties/{property}/audienceExports/{audience_export}`
   * @param array $optParams Optional parameters.
   * @return AudienceExport
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AudienceExport::class);
  }
  /**
   * Lists all audience exports for a property. This method can be used for you to
   * find and reuse existing audience exports rather than creating unnecessary new
   * audience exports. The same audience can have multiple audience exports that
   * represent the export of users that were in an audience on different days. See
   * [Creating an Audience Export](https://developers.google.com/analytics/devguid
   * es/reporting/data/v1/audience-list-basics) for an introduction to Audience
   * Exports with examples. Audience Export APIs have some methods at alpha and
   * other methods at beta stability. The intention is to advance methods to beta
   * stability after some feedback and adoption. To give your feedback on this
   * API, complete the [Google Analytics Audience Export API
   * Feedback](https://forms.gle/EeA5u5LW6PEggtCEA) form.
   * (audienceExports.listPropertiesAudienceExports)
   *
   * @param string $parent Required. All audience exports for this property will
   * be listed in the response. Format: `properties/{property}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of audience exports to
   * return. The service may return fewer than this value. If unspecified, at most
   * 200 audience exports will be returned. The maximum value is 1000 (higher
   * values will be coerced to the maximum).
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAudienceExports` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListAudienceExports` must
   * match the call that provided the page token.
   * @return ListAudienceExportsResponse
   * @throws \Google\Service\Exception
   */
  public function listPropertiesAudienceExports($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAudienceExportsResponse::class);
  }
  /**
   * Retrieves an audience export of users. After creating an audience, the users
   * are not immediately available for exporting. First, a request to
   * `CreateAudienceExport` is necessary to create an audience export of users,
   * and then second, this method is used to retrieve the users in the audience
   * export. See [Creating an Audience Export](https://developers.google.com/analy
   * tics/devguides/reporting/data/v1/audience-list-basics) for an introduction to
   * Audience Exports with examples. Audiences in Google Analytics 4 allow you to
   * segment your users in the ways that are important to your business. To learn
   * more, see https://support.google.com/analytics/answer/9267572. Audience
   * Export APIs have some methods at alpha and other methods at beta stability.
   * The intention is to advance methods to beta stability after some feedback and
   * adoption. To give your feedback on this API, complete the [Google Analytics
   * Audience Export API Feedback](https://forms.gle/EeA5u5LW6PEggtCEA) form.
   * (audienceExports.query)
   *
   * @param string $name Required. The name of the audience export to retrieve
   * users from. Format: `properties/{property}/audienceExports/{audience_export}`
   * @param QueryAudienceExportRequest $postBody
   * @param array $optParams Optional parameters.
   * @return QueryAudienceExportResponse
   * @throws \Google\Service\Exception
   */
  public function query($name, QueryAudienceExportRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('query', [$params], QueryAudienceExportResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertiesAudienceExports::class, 'Google_Service_AnalyticsData_Resource_PropertiesAudienceExports');
