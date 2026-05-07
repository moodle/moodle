<!doctype html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>" class="h5p-iframe">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8'); ?></title>
  <?php for ($i = 0, $s = count($scripts); $i < $s; $i++): ?>
    <script src="<?php echo htmlspecialchars($scripts[$i], ENT_QUOTES, 'UTF-8'); ?>"></script>
  <?php endfor; ?>
  <?php for ($i = 0, $s = count($styles); $i < $s; $i++): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($styles[$i], ENT_QUOTES, 'UTF-8'); ?>">
  <?php endfor; ?>
  <?php if (!empty($additional_embed_head_tags)): echo implode("\n", $additional_embed_head_tags); endif; ?>
</head>
<body>
  <div class="h5p-content" data-content-id="<?php echo intval($content['id']); ?>"></div>
  <script>
    H5PIntegration = <?php echo json_encode($integration); ?>;
  </script>
</body>
</html>