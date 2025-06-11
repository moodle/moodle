# xhprof for PHP7 and PHP8
[![Build Status](https://travis-ci.com/longxinH/xhprof.svg?branch=master)](https://app.travis-ci.com/github/longxinH/xhprof) [![Build status](https://ci.appveyor.com/api/projects/status/dornfeel5yutaxte/branch/master?svg=true)](https://ci.appveyor.com/project/longxinH/xhprof/branch/master)

XHProf is a function-level hierarchical profiler for PHP and has a simple HTML based navigational interface. The raw data collection component is implemented in C (as a PHP extension). The reporting/UI layer is all in PHP. It is capable of reporting function-level inclusive and exclusive wall times, memory usage, CPU times and number of calls for each function. Additionally, it supports ability to compare two runs (hierarchical DIFF reports), or aggregate results from multiple runs.

This version supports PHP7 and PHP8

# PHP Version
- 7.2
- 7.3
- 7.4
- 8.0
- 8.1
- 8.2

# Installation
```
git clone https://github.com/longxinH/xhprof.git ./xhprof
cd xhprof/extension/
/path/to/php7/bin/phpize
./configure --with-php-config=/path/to/php7/bin/php-config
make && sudo make install
```

#### configuration add to your php.ini
```
[xhprof]
extension = xhprof.so
xhprof.output_dir = /tmp/xhprof
```

### php.ini configuration
|      Options        |  Defaults  |  Version  |  Explain  |
| --------------- |:-------------:|:-------------:|:---------|
|xhprof.output_dir  | "" | All |Output directory|
|xhprof.sampling_interval  | 100000 | >= v2.* | Sampling interval to be used by the sampling profiler, in microseconds|
|xhprof.sampling_depth  | INT_MAX | >= v2.* | Depth to trace call-chain by the sampling profiler|
|xhprof.collect_additional_info  | 0 | >= v2.1 | Collect mysql_query, curl_exec internal info. The default is 0. Open value is 1|

# Turn on extra collection
#### php.ini adds xhprof.collect_additional_info
```sh
xhprof.collect_additional_info = 1
````
# Options
```php
xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
```
- `XHPROF_FLAGS_NO_BUILTINS` do not profile builtins
- `XHPROF_FLAGS_CPU` gather CPU times for funcs
- `XHPROF_FLAGS_MEMORY` gather memory usage for funcs

Example
```php
<?php

array(
    "main()" => array(
        "wt" => 237,
        "ct" => 1,
        "cpu" => 100,
    )
)
```

- `wt` The execution time of the function method is time consuming
- `ct` The number of times the function was called
- `cpu` The CPU time consumed by the function method execution
- `mu` Memory used by function methods. The call is zend_memory_usage to get the memory usage
- `pmu` Peak memory used by the function method. The call is zend_memory_peak_usage to get the memory

### PDO::exec
### PDO::query
### mysqli_query
```php
$mysqli = new mysqli("localhost", "my_user", "my_password", "user");
$result = $mysqli->query("SELECT * FROM user LIMIT 10");
```
##### Output data
```
mysqli::query#SELECT * FROM user LIMIT 10
```

### PDO::prepare
Convert preprocessing placeholders for actual parameters, more intuitive analytic performance (does not change the zend execution process)
```php
$_sth = $db->prepare("SELECT * FROM user where userid = :id and username = :name");
$_sth->execute([':id' => '1', ':name' => 'admin']);
$data1 = $_sth->fetch();

$_sth = $db->prepare("SELECT * FROM user where userid = ?");
$_sth->execute([1]);
$data2 = $_sth->fetch();
```
##### Output data
```
PDOStatement::execute#SELECT * FROM user where userid = 1 and username = admin
PDOStatement::execute#SELECT * FROM user where userid = 1
```

### Curl
```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.baidu.com");
$output = curl_exec($ch);
curl_close($ch);
```
##### Output data
```
curl_exec#http://www.baidu.com
```

## PECL Repository
[![pecl](resource/pecl.png)](https://pecl.php.net/package/xhprof)
