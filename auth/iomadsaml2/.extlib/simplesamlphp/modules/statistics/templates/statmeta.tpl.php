<?php
$this->data['header'] = 'SimpleSAMLphp Statistics Metadata';
$this->data['head'] = '<link rel="stylesheet" type="text/css" href="'.
    SimpleSAML\Module::getModuleURL("statistics/assets/css/statistics.css").'" />';
$this->includeAtTemplateBase('includes/header.php');

echo '<table id="statmeta">' ;

if (isset($this->data['metadata'])) {
    $metadata = $this->data['metadata'];

    if (isset($metadata['lastrun'])) {
        echo '<tr><td>Aggregator last run at</td><td>'.$metadata['lastrun'].'</td></tr>';
    }

    if (isset($metadata['notBefore'])) {
        echo '<tr><td>Aggregated data until</td><td>'.$metadata['notBefore'].'</td></tr>';
    }

    if (isset($metadata['memory'])) {
        echo '<tr><td>Memory usage</td><td>'.$metadata['memory'].' MB'.'</td></tr>';
    }

    if (isset($metadata['time'])) {
        echo '<tr><td>Execution time</td><td>'.$metadata['time'].' seconds'.'</td></tr>';
    }

    if (isset($metadata['lastlinehash'])) {
        echo '<tr><td>SHA1 of last processed logline</td><td>'.$metadata['lastlinehash'].'</td></tr>';
    }

    if (isset($metadata['lastline'])) {
        echo '<tr><td>Last processed logline</td><td>'.$metadata['lastline'].'</td></tr>';
    }
} else {
    echo '<tr><td>No metadata found</td></tr>';
}

echo '</table>';
echo '<p>[ <a href="'.SimpleSAML\Module::getModuleURL("statistics/showstats.php").'">Show statistics</a> ] </p>';

$this->includeAtTemplateBase('includes/footer.php');
