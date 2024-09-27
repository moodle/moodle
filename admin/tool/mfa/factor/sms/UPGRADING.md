# factor_sms Upgrade notes

## 4.5beta

### Removed

- The following classes are removed as the SMS feature now takes advantage of core_sms API: - sms_sent (admin/tool/mfa/factor/sms/classes/event/sms_sent.php) - aws_sns (admin/tool/mfa/factor/sms/classes/local/smsgateway/aws_sns.php) - gateway_interface (admin/tool/mfa/factor/sms/classes/local/smsgateway/gateway_interface.php)

  For more information see [MDL-80962](https://tracker.moodle.org/browse/MDL-80962)
