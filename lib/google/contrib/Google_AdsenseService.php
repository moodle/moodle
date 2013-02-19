<?php
/*
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


  /**
   * The "urlchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $urlchannels = $adsenseService->urlchannels;
   *  </code>
   */
  class Google_UrlchannelsServiceResource extends Google_ServiceResource {


    /**
     * List all URL channels in the specified ad client for this AdSense account. (urlchannels.list)
     *
     * @param string $adClientId Ad client for which to list URL channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through URL channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of URL channels to include in the response, used for paging.
     * @return Google_UrlChannels
     */
    public function listUrlchannels($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannels($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adunits" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $adunits = $adsenseService->adunits;
   *  </code>
   */
  class Google_AdunitsServiceResource extends Google_ServiceResource {


    /**
     * List all ad units in the specified ad client for this AdSense account. (adunits.list)
     *
     * @param string $adClientId Ad client for which to list ad units.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeInactive Whether to include inactive ad units. Default: true.
     * @opt_param string pageToken A continuation token, used to page through ad units. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of ad units to include in the response, used for paging.
     * @return Google_AdUnits
     */
    public function listAdunits($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnits($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets the specified ad unit in the specified ad client. (adunits.get)
     *
     * @param string $adClientId Ad client for which to get the ad unit.
     * @param string $adUnitId Ad unit to retrieve.
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function get($adClientId, $adUnitId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "customchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $customchannels = $adsenseService->customchannels;
   *  </code>
   */
  class Google_AdunitsCustomchannelsServiceResource extends Google_ServiceResource {


    /**
     * List all custom channels which the specified ad unit belongs to. (customchannels.list)
     *
     * @param string $adClientId Ad client which contains the ad unit.
     * @param string $adUnitId Ad unit for which to list custom channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through custom channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of custom channels to include in the response, used for paging.
     * @return Google_CustomChannels
     */
    public function listAdunitsCustomchannels($adClientId, $adUnitId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannels($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adclients" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $adclients = $adsenseService->adclients;
   *  </code>
   */
  class Google_AdclientsServiceResource extends Google_ServiceResource {


    /**
     * List all ad clients in this AdSense account. (adclients.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through ad clients. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of ad clients to include in the response, used for paging.
     * @return Google_AdClients
     */
    public function listAdclients($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdClients($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $reports = $adsenseService->reports;
   *  </code>
   */
  class Google_ReportsServiceResource extends Google_ServiceResource {


    /**
     * Generate an AdSense report based on the report request sent in the query parameters. Returns the
     * result as JSON; to retrieve output in CSV format specify "alt=csv" as a query parameter.
     * (reports.generate)
     *
     * @param string $startDate Start of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param string $endDate End of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string sort The name of a dimension or metric to sort the resulting report on, optionally prefixed with "+" to sort ascending or "-" to sort descending. If no prefix is specified, the column is sorted ascending.
     * @opt_param string locale Optional locale to use for translating report output to a local language. Defaults to "en_US" if not specified.
     * @opt_param string metric Numeric columns to include in the report.
     * @opt_param int maxResults The maximum number of rows of report data to return.
     * @opt_param string filter Filters to be run on the report.
     * @opt_param string currency Optional currency to use when reporting on monetary metrics. Defaults to the account's currency if not set.
     * @opt_param int startIndex Index of the first row of report data to return.
     * @opt_param string dimension Dimensions to base the report on.
     * @opt_param string accountId Accounts upon which to report.
     * @return Google_AdsenseReportsGenerateResponse
     */
    public function generate($startDate, $endDate, $optParams = array()) {
      $params = array('startDate' => $startDate, 'endDate' => $endDate);
      $params = array_merge($params, $optParams);
      $data = $this->__call('generate', array($params));
      if ($this->useObjects()) {
        return new Google_AdsenseReportsGenerateResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "accounts" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $accounts = $adsenseService->accounts;
   *  </code>
   */
  class Google_AccountsServiceResource extends Google_ServiceResource {


    /**
     * List all accounts available to this AdSense account. (accounts.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through accounts. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of accounts to include in the response, used for paging.
     * @return Google_Accounts
     */
    public function listAccounts($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Accounts($data);
      } else {
        return $data;
      }
    }
    /**
     * Get information about the selected AdSense account. (accounts.get)
     *
     * @param string $accountId Account to get information about.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool tree Whether the tree of sub accounts should be returned.
     * @return Google_Account
     */
    public function get($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Account($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "urlchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $urlchannels = $adsenseService->urlchannels;
   *  </code>
   */
  class Google_AccountsUrlchannelsServiceResource extends Google_ServiceResource {


    /**
     * List all URL channels in the specified ad client for the specified account. (urlchannels.list)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client for which to list URL channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through URL channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of URL channels to include in the response, used for paging.
     * @return Google_UrlChannels
     */
    public function listAccountsUrlchannels($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_UrlChannels($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "adunits" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $adunits = $adsenseService->adunits;
   *  </code>
   */
  class Google_AccountsAdunitsServiceResource extends Google_ServiceResource {


    /**
     * List all ad units in the specified ad client for the specified account. (adunits.list)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client for which to list ad units.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeInactive Whether to include inactive ad units. Default: true.
     * @opt_param string pageToken A continuation token, used to page through ad units. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of ad units to include in the response, used for paging.
     * @return Google_AdUnits
     */
    public function listAccountsAdunits($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnits($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets the specified ad unit in the specified ad client for the specified account. (adunits.get)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client for which to get the ad unit.
     * @param string $adUnitId Ad unit to retrieve.
     * @param array $optParams Optional parameters.
     * @return Google_AdUnit
     */
    public function get($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnit($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "customchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $customchannels = $adsenseService->customchannels;
   *  </code>
   */
  class Google_AccountsAdunitsCustomchannelsServiceResource extends Google_ServiceResource {


    /**
     * List all custom channels which the specified ad unit belongs to. (customchannels.list)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client which contains the ad unit.
     * @param string $adUnitId Ad unit for which to list custom channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through custom channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of custom channels to include in the response, used for paging.
     * @return Google_CustomChannels
     */
    public function listAccountsAdunitsCustomchannels($accountId, $adClientId, $adUnitId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'adUnitId' => $adUnitId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannels($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "adclients" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $adclients = $adsenseService->adclients;
   *  </code>
   */
  class Google_AccountsAdclientsServiceResource extends Google_ServiceResource {


    /**
     * List all ad clients in the specified account. (adclients.list)
     *
     * @param string $accountId Account for which to list ad clients.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through ad clients. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of ad clients to include in the response, used for paging.
     * @return Google_AdClients
     */
    public function listAccountsAdclients($accountId, $optParams = array()) {
      $params = array('accountId' => $accountId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdClients($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "reports" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $reports = $adsenseService->reports;
   *  </code>
   */
  class Google_AccountsReportsServiceResource extends Google_ServiceResource {


    /**
     * Generate an AdSense report based on the report request sent in the query parameters. Returns the
     * result as JSON; to retrieve output in CSV format specify "alt=csv" as a query parameter.
     * (reports.generate)
     *
     * @param string $accountId Account upon which to report.
     * @param string $startDate Start of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param string $endDate End of the date range to report on in "YYYY-MM-DD" format, inclusive.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string sort The name of a dimension or metric to sort the resulting report on, optionally prefixed with "+" to sort ascending or "-" to sort descending. If no prefix is specified, the column is sorted ascending.
     * @opt_param string locale Optional locale to use for translating report output to a local language. Defaults to "en_US" if not specified.
     * @opt_param string metric Numeric columns to include in the report.
     * @opt_param int maxResults The maximum number of rows of report data to return.
     * @opt_param string filter Filters to be run on the report.
     * @opt_param string currency Optional currency to use when reporting on monetary metrics. Defaults to the account's currency if not set.
     * @opt_param int startIndex Index of the first row of report data to return.
     * @opt_param string dimension Dimensions to base the report on.
     * @return Google_AdsenseReportsGenerateResponse
     */
    public function generate($accountId, $startDate, $endDate, $optParams = array()) {
      $params = array('accountId' => $accountId, 'startDate' => $startDate, 'endDate' => $endDate);
      $params = array_merge($params, $optParams);
      $data = $this->__call('generate', array($params));
      if ($this->useObjects()) {
        return new Google_AdsenseReportsGenerateResponse($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "customchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $customchannels = $adsenseService->customchannels;
   *  </code>
   */
  class Google_AccountsCustomchannelsServiceResource extends Google_ServiceResource {


    /**
     * List all custom channels in the specified ad client for the specified account.
     * (customchannels.list)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client for which to list custom channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through custom channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of custom channels to include in the response, used for paging.
     * @return Google_CustomChannels
     */
    public function listAccountsCustomchannels($accountId, $adClientId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannels($data);
      } else {
        return $data;
      }
    }
    /**
     * Get the specified custom channel from the specified ad client for the specified account.
     * (customchannels.get)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client which contains the custom channel.
     * @param string $customChannelId Custom channel to retrieve.
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function get($accountId, $adClientId, $customChannelId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adunits" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $adunits = $adsenseService->adunits;
   *  </code>
   */
  class Google_AccountsCustomchannelsAdunitsServiceResource extends Google_ServiceResource {


    /**
     * List all ad units in the specified custom channel. (adunits.list)
     *
     * @param string $accountId Account to which the ad client belongs.
     * @param string $adClientId Ad client which contains the custom channel.
     * @param string $customChannelId Custom channel for which to list ad units.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeInactive Whether to include inactive ad units. Default: true.
     * @opt_param int maxResults The maximum number of ad units to include in the response, used for paging.
     * @opt_param string pageToken A continuation token, used to page through ad units. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @return Google_AdUnits
     */
    public function listAccountsCustomchannelsAdunits($accountId, $adClientId, $customChannelId, $optParams = array()) {
      $params = array('accountId' => $accountId, 'adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnits($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "customchannels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $customchannels = $adsenseService->customchannels;
   *  </code>
   */
  class Google_CustomchannelsServiceResource extends Google_ServiceResource {


    /**
     * List all custom channels in the specified ad client for this AdSense account.
     * (customchannels.list)
     *
     * @param string $adClientId Ad client for which to list custom channels.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken A continuation token, used to page through custom channels. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of custom channels to include in the response, used for paging.
     * @return Google_CustomChannels
     */
    public function listCustomchannels($adClientId, $optParams = array()) {
      $params = array('adClientId' => $adClientId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannels($data);
      } else {
        return $data;
      }
    }
    /**
     * Get the specified custom channel from the specified ad client. (customchannels.get)
     *
     * @param string $adClientId Ad client which contains the custom channel.
     * @param string $customChannelId Custom channel to retrieve.
     * @param array $optParams Optional parameters.
     * @return Google_CustomChannel
     */
    public function get($adClientId, $customChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_CustomChannel($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "adunits" collection of methods.
   * Typical usage is:
   *  <code>
   *   $adsenseService = new Google_AdsenseService(...);
   *   $adunits = $adsenseService->adunits;
   *  </code>
   */
  class Google_CustomchannelsAdunitsServiceResource extends Google_ServiceResource {


    /**
     * List all ad units in the specified custom channel. (adunits.list)
     *
     * @param string $adClientId Ad client which contains the custom channel.
     * @param string $customChannelId Custom channel for which to list ad units.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeInactive Whether to include inactive ad units. Default: true.
     * @opt_param string pageToken A continuation token, used to page through ad units. To retrieve the next page, set this parameter to the value of "nextPageToken" from the previous response.
     * @opt_param int maxResults The maximum number of ad units to include in the response, used for paging.
     * @return Google_AdUnits
     */
    public function listCustomchannelsAdunits($adClientId, $customChannelId, $optParams = array()) {
      $params = array('adClientId' => $adClientId, 'customChannelId' => $customChannelId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AdUnits($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Adsense (v1.1).
 *
 * <p>
 * Gives AdSense publishers access to their inventory and the ability to generate reports
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/adsense/management/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_AdsenseService extends Google_Service {
  public $urlchannels;
  public $adunits;
  public $adunits_customchannels;
  public $adclients;
  public $reports;
  public $accounts;
  public $accounts_urlchannels;
  public $accounts_adunits;
  public $accounts_adunits_customchannels;
  public $accounts_adclients;
  public $accounts_reports;
  public $accounts_customchannels;
  public $accounts_customchannels_adunits;
  public $customchannels;
  public $customchannels_adunits;
  /**
   * Constructs the internal representation of the Adsense service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'adsense/v1.1/';
    $this->version = 'v1.1';
    $this->serviceName = 'adsense';

    $client->addService($this->serviceName, $this->version);
    $this->urlchannels = new Google_UrlchannelsServiceResource($this, $this->serviceName, 'urlchannels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}}, "id": "adsense.urlchannels.list", "httpMethod": "GET", "path": "adclients/{adClientId}/urlchannels", "response": {"$ref": "UrlChannels"}}}}', true));
    $this->adunits = new Google_AdunitsServiceResource($this, $this->serviceName, 'adunits', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"includeInactive": {"type": "boolean", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}}, "id": "adsense.adunits.list", "httpMethod": "GET", "path": "adclients/{adClientId}/adunits", "response": {"$ref": "AdUnits"}}, "get": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "adUnitId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.adunits.get", "httpMethod": "GET", "path": "adclients/{adClientId}/adunits/{adUnitId}", "response": {"$ref": "AdUnit"}}}}', true));
    $this->adunits_customchannels = new Google_AdunitsCustomchannelsServiceResource($this, $this->serviceName, 'customchannels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "adUnitId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.adunits.customchannels.list", "httpMethod": "GET", "path": "adclients/{adClientId}/adunits/{adUnitId}/customchannels", "response": {"$ref": "CustomChannels"}}}}', true));
    $this->adclients = new Google_AdclientsServiceResource($this, $this->serviceName, 'adclients', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}}, "response": {"$ref": "AdClients"}, "httpMethod": "GET", "path": "adclients", "id": "adsense.adclients.list"}}}', true));
    $this->reports = new Google_ReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"generate": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"sort": {"repeated": true, "type": "string", "location": "query"}, "startDate": {"required": true, "type": "string", "location": "query"}, "endDate": {"required": true, "type": "string", "location": "query"}, "locale": {"type": "string", "location": "query"}, "metric": {"repeated": true, "type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "50000", "format": "int32"}, "filter": {"repeated": true, "type": "string", "location": "query"}, "currency": {"type": "string", "location": "query"}, "startIndex": {"location": "query", "minimum": "0", "type": "integer", "maximum": "5000", "format": "int32"}, "dimension": {"repeated": true, "type": "string", "location": "query"}, "accountId": {"repeated": true, "type": "string", "location": "query"}}, "id": "adsense.reports.generate", "httpMethod": "GET", "supportsMediaDownload": true, "path": "reports", "response": {"$ref": "AdsenseReportsGenerateResponse"}}}}', true));
    $this->accounts = new Google_AccountsServiceResource($this, $this->serviceName, 'accounts', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}}, "response": {"$ref": "Accounts"}, "httpMethod": "GET", "path": "accounts", "id": "adsense.accounts.list"}, "get": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"tree": {"type": "boolean", "location": "query"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.get", "httpMethod": "GET", "path": "accounts/{accountId}", "response": {"$ref": "Account"}}}}', true));
    $this->accounts_urlchannels = new Google_AccountsUrlchannelsServiceResource($this, $this->serviceName, 'urlchannels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.urlchannels.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/urlchannels", "response": {"$ref": "UrlChannels"}}}}', true));
    $this->accounts_adunits = new Google_AccountsAdunitsServiceResource($this, $this->serviceName, 'adunits', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"includeInactive": {"type": "boolean", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.adunits.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/adunits", "response": {"$ref": "AdUnits"}}, "get": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"adClientId": {"required": true, "type": "string", "location": "path"}, "adUnitId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.adunits.get", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}", "response": {"$ref": "AdUnit"}}}}', true));
    $this->accounts_adunits_customchannels = new Google_AccountsAdunitsCustomchannelsServiceResource($this, $this->serviceName, 'customchannels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "adUnitId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.adunits.customchannels.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/adunits/{adUnitId}/customchannels", "response": {"$ref": "CustomChannels"}}}}', true));
    $this->accounts_adclients = new Google_AccountsAdclientsServiceResource($this, $this->serviceName, 'adclients', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.adclients.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients", "response": {"$ref": "AdClients"}}}}', true));
    $this->accounts_reports = new Google_AccountsReportsServiceResource($this, $this->serviceName, 'reports', json_decode('{"methods": {"generate": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"sort": {"repeated": true, "type": "string", "location": "query"}, "startDate": {"required": true, "type": "string", "location": "query"}, "endDate": {"required": true, "type": "string", "location": "query"}, "locale": {"type": "string", "location": "query"}, "metric": {"repeated": true, "type": "string", "location": "query"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "50000", "format": "int32"}, "filter": {"repeated": true, "type": "string", "location": "query"}, "currency": {"type": "string", "location": "query"}, "startIndex": {"location": "query", "minimum": "0", "type": "integer", "maximum": "5000", "format": "int32"}, "dimension": {"repeated": true, "type": "string", "location": "query"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.reports.generate", "httpMethod": "GET", "supportsMediaDownload": true, "path": "accounts/{accountId}/reports", "response": {"$ref": "AdsenseReportsGenerateResponse"}}}}', true));
    $this->accounts_customchannels = new Google_AccountsCustomchannelsServiceResource($this, $this->serviceName, 'customchannels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.customchannels.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/customchannels", "response": {"$ref": "CustomChannels"}}, "get": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"customChannelId": {"required": true, "type": "string", "location": "path"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.customchannels.get", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/customchannels/{customChannelId}", "response": {"$ref": "CustomChannel"}}}}', true));
    $this->accounts_customchannels_adunits = new Google_AccountsCustomchannelsAdunitsServiceResource($this, $this->serviceName, 'adunits', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"includeInactive": {"type": "boolean", "location": "query"}, "customChannelId": {"required": true, "type": "string", "location": "path"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}, "pageToken": {"type": "string", "location": "query"}, "accountId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.accounts.customchannels.adunits.list", "httpMethod": "GET", "path": "accounts/{accountId}/adclients/{adClientId}/customchannels/{customChannelId}/adunits", "response": {"$ref": "AdUnits"}}}}', true));
    $this->customchannels = new Google_CustomchannelsServiceResource($this, $this->serviceName, 'customchannels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}}, "id": "adsense.customchannels.list", "httpMethod": "GET", "path": "adclients/{adClientId}/customchannels", "response": {"$ref": "CustomChannels"}}, "get": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"customChannelId": {"required": true, "type": "string", "location": "path"}, "adClientId": {"required": true, "type": "string", "location": "path"}}, "id": "adsense.customchannels.get", "httpMethod": "GET", "path": "adclients/{adClientId}/customchannels/{customChannelId}", "response": {"$ref": "CustomChannel"}}}}', true));
    $this->customchannels_adunits = new Google_CustomchannelsAdunitsServiceResource($this, $this->serviceName, 'adunits', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/adsense", "https://www.googleapis.com/auth/adsense.readonly"], "parameters": {"includeInactive": {"type": "boolean", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "customChannelId": {"required": true, "type": "string", "location": "path"}, "adClientId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"location": "query", "minimum": "0", "type": "integer", "maximum": "10000", "format": "int32"}}, "id": "adsense.customchannels.adunits.list", "httpMethod": "GET", "path": "adclients/{adClientId}/customchannels/{customChannelId}/adunits", "response": {"$ref": "AdUnits"}}}}', true));

  }
}

class Google_Account extends Google_Model {
  public $kind;
  public $id;
  protected $__subAccountsType = 'Google_Account';
  protected $__subAccountsDataType = 'array';
  public $subAccounts;
  public $name;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSubAccounts($subAccounts) {
    $this->assertIsArray($subAccounts, 'Google_Account', __METHOD__);
    $this->subAccounts = $subAccounts;
  }
  public function getSubAccounts() {
    return $this->subAccounts;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_Accounts extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_Account';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_Account', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AdClient extends Google_Model {
  public $productCode;
  public $kind;
  public $id;
  public $supportsReporting;
  public function setProductCode($productCode) {
    $this->productCode = $productCode;
  }
  public function getProductCode() {
    return $this->productCode;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSupportsReporting($supportsReporting) {
    $this->supportsReporting = $supportsReporting;
  }
  public function getSupportsReporting() {
    return $this->supportsReporting;
  }
}

class Google_AdClients extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_AdClient';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_AdClient', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AdUnit extends Google_Model {
  public $status;
  public $kind;
  public $code;
  public $id;
  public $name;
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_AdUnits extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_AdUnit';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_AdUnit', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AdsenseReportsGenerateResponse extends Google_Model {
  public $kind;
  public $rows;
  public $warnings;
  public $totals;
  protected $__headersType = 'Google_AdsenseReportsGenerateResponseHeaders';
  protected $__headersDataType = 'array';
  public $headers;
  public $totalMatchedRows;
  public $averages;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setRows($rows) {
    $this->rows = $rows;
  }
  public function getRows() {
    return $this->rows;
  }
  public function setWarnings($warnings) {
    $this->warnings = $warnings;
  }
  public function getWarnings() {
    return $this->warnings;
  }
  public function setTotals($totals) {
    $this->totals = $totals;
  }
  public function getTotals() {
    return $this->totals;
  }
  public function setHeaders($headers) {
    $this->assertIsArray($headers, 'Google_AdsenseReportsGenerateResponseHeaders', __METHOD__);
    $this->headers = $headers;
  }
  public function getHeaders() {
    return $this->headers;
  }
  public function setTotalMatchedRows($totalMatchedRows) {
    $this->totalMatchedRows = $totalMatchedRows;
  }
  public function getTotalMatchedRows() {
    return $this->totalMatchedRows;
  }
  public function setAverages($averages) {
    $this->averages = $averages;
  }
  public function getAverages() {
    return $this->averages;
  }
}

class Google_AdsenseReportsGenerateResponseHeaders extends Google_Model {
  public $currency;
  public $type;
  public $name;
  public function setCurrency($currency) {
    $this->currency = $currency;
  }
  public function getCurrency() {
    return $this->currency;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_CustomChannel extends Google_Model {
  public $kind;
  public $code;
  protected $__targetingInfoType = 'Google_CustomChannelTargetingInfo';
  protected $__targetingInfoDataType = '';
  public $targetingInfo;
  public $id;
  public $name;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCode($code) {
    $this->code = $code;
  }
  public function getCode() {
    return $this->code;
  }
  public function setTargetingInfo(Google_CustomChannelTargetingInfo $targetingInfo) {
    $this->targetingInfo = $targetingInfo;
  }
  public function getTargetingInfo() {
    return $this->targetingInfo;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_CustomChannelTargetingInfo extends Google_Model {
  public $location;
  public $adsAppearOn;
  public $siteLanguage;
  public $description;
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setAdsAppearOn($adsAppearOn) {
    $this->adsAppearOn = $adsAppearOn;
  }
  public function getAdsAppearOn() {
    return $this->adsAppearOn;
  }
  public function setSiteLanguage($siteLanguage) {
    $this->siteLanguage = $siteLanguage;
  }
  public function getSiteLanguage() {
    return $this->siteLanguage;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}

class Google_CustomChannels extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_CustomChannel';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_CustomChannel', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_UrlChannel extends Google_Model {
  public $kind;
  public $id;
  public $urlPattern;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setUrlPattern($urlPattern) {
    $this->urlPattern = $urlPattern;
  }
  public function getUrlPattern() {
    return $this->urlPattern;
  }
}

class Google_UrlChannels extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_UrlChannel';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems($items) {
    $this->assertIsArray($items, 'Google_UrlChannel', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}
