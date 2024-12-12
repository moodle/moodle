<?php
$eventId = $_GET['id'];
require_once('/var/www/html/moodle/custom/app/Controllers/SurveyCustomFieldController.php');
$surveyCustomFieldController = new SurveyCustomFieldController();
$responceList = $surveyCustomFieldController->getSurveyCustomFieldBackend($eventId);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>フィールド追加フォーム</title>
  <link rel="stylesheet" href="/front/style.css" type="text/css">
  <!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
  <style>
    .field-container {
      margin-bottom: 3rem;
    }

    .field-container label {
      display: inline-block;
      margin-right: 10px;
      width: 100%;
    }

    .field-container input,
    .field-container select {
      width: 50%;
      margin-bottom: 10px;
    }

    h2 {
      margin-top: 80px;
      color: #08153A;
    }

    body {
      padding: 3rem;
    }

    .delete_btn {
      width: 100px;
      background-color: #b71111db;
      border: 1px solid #b71111db;
    }

    .delete_btn:hover {
      background-color: #b71111f2;
      border: 1px solid #b71111f2;
    }

    input,
    select {
      padding: 8px;
      box-sizing: border-box;
    }
  </style>
</head>

<body>
  <header>
    <p>大阪大学 動画プラットフォーム</p>
    <?php if ($_SESSION['USER']->id == 0) {  ?>
      <button class="login-button" onclick="window.location.href='/login/index.php'">ログイン</button>
    <?php } else { ?>
      <P class="user_header_p"><?php echo $_SESSION['USER']->lastname . ' ' . $_SESSION['USER']->firstname ?></P>
      <P class="user_header_p"><?php echo $_SESSION['USER']->email ?></P>
      <button class="login-button" onclick="window.location.href='/login/logout.php'">ログアウト</button>
    <?php } ?>
  </header>
  <h2>動的フィールド追加 ( イベント )</h2>
  <form action="survey_customfield_upsert.php" method="post">
    <?php if (empty($responceList['fieldList'])) { ?>
      <div id="fieldsContainer">
        <!-- 動的にフィールドが追加される場所 -->
        <div class="field-container">
          <input type="hidden" name="eventId" value=<?php echo $eventId ?>>
          <label for="fieldName">項目名:</label>
          <input type="text" name="fieldName[]" placeholder="フィールド名を入力" required>
          <label for="name">フィールド名:</label>
          <input type="text" name="name[]" placeholder="nameを入力" required>
          <label for="sort">表示順:</label>
          <input type="number" name="sort[]" placeholder="表示順を入力" required>
          <label for="fieldType">フィールドタイプ:</label>
          <select name="fieldType[]" required>
            <option value="text">テキスト</option>
            <option value="textarea">テキストエリア</option>
            <option value="checkbox">チェックボックス</option>
            <option value="radio">ラジオ</option>
            <option value="date">日付</option>
            <option value="file">ファイル</option>
            <option value="video">動画</option>
          </select>
          <div class="options-container" style="display: none;">
            <label for="fieldOptions">選択肢 (カンマ区切り):</label>
            <input type="text" name="fieldOptions[]" placeholder="例: 選択肢1, 選択肢2">
          </div>
        </div>
      </div>
      <?php } else {
      foreach ($responceList['fieldList'] as $fields) { ?>
        <div id="fieldsContainer">
          <!-- 動的にフィールドが追加される場所 -->
          <div class="field-container">
            <input type="hidden" name="id[]" value=<?php echo !empty($fields['id']) ? $fields['id'] : 0; ?>>
            <input type="hidden" name="eventId" value=<?php echo $eventId ?>>
            <label for="fieldName">項目名:</label>
            <input type="text" name="fieldName[]" placeholder="フィールド名を入力" value="<?php echo $fields['field_name'] ?>" required>
            <label for="name">フィールド名:</label>
            <input type="text" name="name[]" placeholder="nameを入力" value="<?php echo $fields['name'] ?>" required>
            <label for="sort">表示順:</label>
            <input type="number" name="sort[]" placeholder="表示順を入力" value="<?php echo $fields['sort'] ?>" required>
            <label for="fieldType">フィールドタイプ:</label>
            <select name="fieldType[]" required>
              <option value="text" <?php echo ($fields['field_type'] == 'text') ? 'selected' : ''; ?>>テキスト</option>
              <option value="textarea" <?php echo ($fields['field_type'] == 'textarea') ? 'selected' : ''; ?>>テキストエリア</option>
              <option value="checkbox" <?php echo ($fields['field_type'] == 'checkbox') ? 'selected' : ''; ?>>チェックボックス</option>
              <option value="radio" <?php echo ($fields['field_type'] == 'radio') ? 'selected' : ''; ?>>ラジオ</option>
              <option value="date" <?php echo ($fields['field_type'] == 'date') ? 'selected' : ''; ?>>日付</option>
              <option value="file" <?php echo ($fields['field_type'] == 'file') ? 'selected' : ''; ?>>ファイル</option>
              <option value="video" <?php echo ($fields['field_type'] == 'video') ? 'selected' : ''; ?>>動画</option>
            </select>

            <div class="options-container" style="display: none;">
              <label for="fieldOptions">選択肢 (カンマ区切り):</label>
              <input type="text" name="fieldOptions[]" placeholder="例: 選択肢1, 選択肢2" value="<?php echo $fields['field_options'] ?>">
            </div>
            <div><button class="delete_btn">削除</button></div>
          </div>
        </div>
    <?php }
    } ?>

    <button id="add_btn" type="button" onclick="addField()">フィールドを追加</button>
    <button type="submit">送信</button>
  </form>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    const Values = $('select[name="fieldType[]"]').map(function() {
      if ($(this).val() == 'radio' || $(this).val() == 'checkbox') {
        $(this).next().css('display', 'block');
      }
    }).get();
  });
  $(document).on('change', 'select[name="fieldType[]"]', function() {
    if ($(this).val() === 'checkbox' || $(this).val() === 'radio') {
      $(this).next().css('display', 'block');
    } else {
      $(this).next().css('display', 'none');
    }
  });
  $(document).on('click', '.delete_btn', function() {
    event.preventDefault();
    $(this).parent().find('input[name="id[]"]').prop("disabled", true);;
    $(this).parents('.field-container').css('display', 'none');
  });

  // フィールド追加
  function addField() {
    const container = document.getElementById('add_btn');
    const newField = document.createElement('div');
    newField.classList.add('field-container');
    newField.innerHTML = `
      <input type="hidden" name="id[]" value=0>
      <input type="hidden" name="eventId" value="<?php echo $eventId ?>">
      <label for="fieldName">項目名:</label>
      <input type="text" name="fieldName[]" placeholder="フィールド名を入力" required>
      <label for="name">フィールド名:</label>
      <input type="text" name="name[]" placeholder="nameを入力" required>
      <label for="name">表示順:</label>
      <input type="number" name="sort[]" placeholder="表示順を入力" required>
      <label for="fieldType">フィールドタイプ:</label>
      <select name="fieldType[]" required>
        <option value="text">テキスト</option>
        <option value="textarea">テキストエリア</option>
        <option value="checkbox">チェックボックス</option>
        <option value="radio">ラジオ</option>
        <option value="date">日付</option>
        <option value="file">ファイル</option>
        <option value="video">動画</option>
      </select>

      <div class="options-container" style="display: none;">
        <label for="fieldOptions">選択肢 (カンマ区切り):</label>
        <input type="text" name="fieldOptions[]" placeholder="例: 選択肢1, 選択肢2">
      </div>
      <button class="delete_btn">削除</button>
    `;

    // 新しいフィールドを追加
    container.before(newField);
  }
</script>

</html>