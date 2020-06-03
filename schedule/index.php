<?php
require_once 'secure.php';
$header = 'Главная: Ваше расписание занятий.';
require_once './template/header.php';
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <section class="box-body">
                <h3><?=$header;?></h3>
            </section>
        </div>
    </div>
</div>
<?php
require_once './template/footer.php';
?>