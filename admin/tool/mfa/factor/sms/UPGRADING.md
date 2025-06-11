# factor_sms Upgrade notes

## 4.5

### Removed

- The following classes are removed as the SMS feature now takes advantage of `core_sms` API:
  - `\factor_sms\event\sms_sent`
  - `\factor_sms\local\smsgateway\aws_sns`
  - `\factor_sms\local\smsgateway\gateway_interface`

  For more information see [MDL-80962](https://tracker.moodle.org/browse/MDL-80962)
