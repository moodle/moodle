<?php
require_once('/var/www/html/moodle/custom/app/Controllers/EventController.php');
$eventId = $_GET['id'];
$eventController = new EventController();
$event = $eventController->getEventDetails($eventId);
$details = $event['details'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin イベント登録</title>
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
      padding-left: 3rem;
      margin-top: 80px;
      color: #2D287F;
    }

    form {
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

    label {
      color: #2D287F;
      font-weight: bold;
      margin-top: 1.5vh;
    }

    img {
      display: block;
    }
  </style>
</head>
<?php include('/var/www/html/moodle/custom/app/Views/common/header.php'); ?>
<h2>イベント登録</h2>
<form action="submit.php" method="post" enctype="multipart/form-data">
  <div id="fieldsContainer">
    <!-- 動的にフィールドが追加される場所 -->
    <div class="field-container">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <input type="hidden" name="eventId" value=<?php echo htmlspecialchars($eventId) ?>>
      <label for="fieldName">イベントタイトル</label>
      <input type="text" name="name" required value=<?php echo htmlspecialchars($event['name']) ?>>
      <label for="category_name">カテゴリ名</label>
      <input type="text" name="category_name" placeholder="nameを入力">

      <label for="main_img">イベント画像</label>
      <input type="file" id="main_img" name="main_img" accept="image/*">
      <img id="preview_main" src="/custom/upload/img/<?php echo htmlspecialchars($event['main_img_name']) ?>" onerror="this.style.display='none';">
      <label for="detail_img">イベント詳細画像</label>
      <input type="file" id="sub_img" name="detail_img" accept="image/*">
      <img id="preview_sub" src="/custom/upload/img/<?php echo htmlspecialchars($event['sub_img_name']) ?>" onerror="this.style.display='none';">
      <label for="venue">会場</label>
      <select name="venue">
        <option value="1">オンライン</option>
        <option value="2">オンデマンド</option>
        <option value="3">現地</option>
      </select>
      <label for="target">受講対象</label>
      <input type="text" name="target" value=<?php echo htmlspecialchars($event['target']) ?>>
      <label for="note">備考</label>
      <textarea name="note" rows="5" cols="107"><?php echo htmlspecialchars($event['note']) ?></textarea>
      <label for="abstruct">イベント概要</label>
      <textarea name="abstruct" rows="5" cols="107"><?php echo htmlspecialchars($event['description']) ?></textarea>

      <?php if ($details) { ?>
        <?php foreach ($details as $detail) { ?>
          <div class="individual-container">
            <input type="hidden" name="each_id[]" value=<?php echo htmlspecialchars($detail['id']) ?>>
            <label for="start_date">開始日</label>
            <input type="datetime-local" name="start_date[]" required value=<?php echo date('Y-m-d\TH:i', strtotime($detail['start_date'])) ?>>
            <label for="end_date">終了日</label>
            <input type="datetime-local" name="end_date[]" required value=<?php echo date('Y-m-d\TH:i', strtotime($detail['end_date'])) ?>>
            <label for="capacity">定員</label>
            <input type="number" name="capacity[]" required value=<?php echo htmlspecialchars($detail['capacity']) ?>>
            <label for="capacity">教員</label>
            <input type="text" name="teacher_name[]" required value="<?php echo htmlspecialchars($detail['teacher_name']) ?>">
            <label for="files">講義資料</label>
            <input type="file" name="files[]" accept="application/pdf">
            <label for="movie">講義動画</label>
            <input type="file" name="movie[]" accept="video/*">
          </div>
        <?php } ?>
      <? } ?>
    </div>
  </div>
  <button id="add_btn" type="button" onclick="addField()">フィールドを追加</button>
  <button type="submit">送信</button>
</form>
</body>
<?php include('/var/www/html/moodle/custom/app/Views/common/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    const Values = $('select[name="fieldType[]"]').map(function() {
      if ($(this).val() == 'radio' || $(this).val() == 'checkbox') {
        $(this).next().css('display', 'block');
      }
    }).get();
    $('#main_img').on('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#preview_main').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      }
    });
    // ファイル選択時の処理
    $('#main_img').on('change', function(event) {
      const file = event.target.files[0];
      previewImage(file, '#preview_main');
    });
    $('#sub_img').on('change', function(event) {
      const file = event.target.files[0];
      previewImage(file, '#preview_sub');
    });
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
    $(this).parent().find('input[name="id[]"]').prop("disabled", true);
    $(this).parents('.field-container').css('display', 'none');
  });

  // フィールド追加
  function addField() {
    const container = document.getElementById('add_btn');
    const newField = document.createElement('div');
    newField.classList.add('field-container');
    newField.innerHTML = `
    <div class="individual-container">
      <input type="hidden" name="each_id[]" value="">
      <label for="start_date">開始日</label>
      <input type="datetime-local" name="start_date[]" required>
      <label for="end_date">終了日</label>
      <input type="datetime-local" name="end_date[]" required>
      <label for="capacity">定員</label>
      <input type="number" name="capacity[]" required> 
      <label for="capacity">教員</label>
      <input type="text" name="teacher_name[]" required> 
      <label for="files">講義資料</label>
      <input type="file" name="files[]" accept="application/pdf">
      <label for="movie">講義動画</label>
      <input type="file" name="movie[]" accept="video/*">
    </div>
    <button class="delete_btn">削除</button>
    `;

    // 新しいフィールドを追加
    container.before(newField);
  }
  // 画像描画
  function previewImage(file, previewSelector) {
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const imgElement = $(previewSelector);
        imgElement.on('load', function() {
          $(this).css('display', '');
        });
        imgElement.on('error', function() {
          $(this).css('display', 'none');
        });
        imgElement.attr('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  }
</script>

</html>