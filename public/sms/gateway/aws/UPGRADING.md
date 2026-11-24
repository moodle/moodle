# smsgateway_aws Upgrade notes

## 5.2.5+

### Changed

- The AWS SMS gateway now uses its configured default country code for phone numbers in local format. Before, these numbers were always sent with the country code 1, whatever was configured. Sites that relied on this, for example in the United States or Canada, must set the gateway's default country code to 1, otherwise local numbers are sent without a country code and fail.

  For more information see [MDL-84943](https://tracker.moodle.org/browse/MDL-84943)

