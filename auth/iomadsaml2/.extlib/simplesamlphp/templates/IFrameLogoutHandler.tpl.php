<!DOCTYPE html>
<html>
  <head>
    <title>Logout response from <?= htmlspecialchars(var_export($this->data['assocId'])); ?></title>
    <script>
<?php
    if (array_key_exists('errorMsg', $this->data)) {
        echo 'window.parent.logoutFailed("'.$this->data['spId'].'", "'.addslashes($this->data['errorMsg']).'");';
    } else {
        echo 'window.parent.logoutCompleted("'.$this->data['spId'].'");';
    }
?>
    </script>
  </head>
  <body></body>
</html>
