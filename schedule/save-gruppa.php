<?php
require_once 'secure.php';
if (!Helper::can('admin') && !Helper::can('manager')) {
    header('Location: 404.php');
    exit();
    }
if (isset($_POST['saveGruppa'])) {
    $gruppa = new Gruppa();
    $gruppa->group_id = Helper::clearInt($_POST['gruppa_id']);
    $gruppa->name = Helper::clearString($_POST['name']);
    $gruppa->special_id =Helper::clearInt($_POST['special_id']);
    $gruppa->date_begin =Helper::clearString($_POST['date_begin']);
    $gruppa->date_end =Helper::clearString($_POST['date_end']);
    if ((new GruppaMap())->save($gruppa)) {
        header('Location: view-gruppa.php?id='.$gruppa->group_id);
    } else {
        if ($gruppa->group_id) {
    header('Location: add-gruppa.php?id='.$gruppa->group_id);
        } else {
            header('Location: add-gruppa.php');
        }
    }
}