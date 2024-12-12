<?php
require_once('/var/www/html/moodle/custom/app/Controllers/FrontController.php');
$frontController = new FrontController();
$responce = $frontController->index();
?>

<?php include('/var/www/html/moodle/custom/app/Views/common/header.php'); ?>
<div class="main_img">
    <img src="/custom/public/images/main.png" />
</div>
<div class="content_title_area">
    <div>
        <P class="content_sub_title">NEW ALLIVAL</P>
        <P class="content_title">新着講座</P>
    </div>
    <button class="list_button">全ての講座を見る</button>
</div>
<div class="slider">
    <?php foreach ($responce['eventList'] as $event) { ?>
        <div class="slider-img">
            <a href="event_detail.php?id=<?php echo $event['id'] ?>">
                <img src="/custom/upload/img/<?php echo htmlspecialchars($event['main_img_name']) ?>" />
                <p class="status">受付中</p>
                <p class="event_name"><?php echo htmlspecialchars($event['name']); ?></p>
                <?php foreach ($event['details'] as $key => $detail) { ?>
                    <?php
                    $startDate = (new DateTime($detail['start_date']))->format('Y年m月d日');
                    $endDate = (new DateTime($detail['end_date']))->format('Y年m月d日');
                    ?>
                    <p class="event_period">
                        <span>開催日</span> <?php echo $key + 1 ?>回目 :
                        <?php echo htmlspecialchars($startDate) ?> ~ <?php echo htmlspecialchars($endDate) ?>
                    </p>
                    <?php
                    if ($key >= 1) {
                        break;
                    }
                    $key += 1;
                    ?>
                <?php } ?>
            </a>
        </div>
    <?php } ?>
</div>
</body>
<?php include('/var/www/html/moodle/custom/app/Views/common/footer.php'); ?>

</html>
<script>
    $(function() {
        $(".slider").slick({
            arrows: true,
            autoplay: true,
            adaptiveHeight: true,
            centerMode: true,
            centerPadding: "6%",
            dots: false,
            slidesToShow: 3,
        });
    });
</script>