<?php

$from_id = $_GET['from_id'];
if (!$from_id) return;

$to_id = $_POST['to_id'];
if (!$to_id) return;

function copy_category_branch ($id, $pid) {
    $data = q_assoc_row('SELECT * FROM `m_catalogue_categories` WHERE `id` = "'. $id .'"');
    unset($data['id']);
    $data['pid'] = $pid;
    $title = Translations::get($data['title']);
    $data['title'] = Translations::save($title);
    $data['specification_id'] = 0;
    $new_id = q('INSERT INTO `m_catalogue_categories` (`'. implode('`,`', array_keys($data)) .'`) VALUES ("'. implode('","', $data) .'")', 1, 1);
    foreach (q_assoc('SELECT `id` FROM `m_catalogue_categories` WHERE `pid` = "'. $id .'"') as $v) {
        copy_category_branch($v['id'], $new_id);
    }
}

copy_category_branch($from_id, $to_id);

go('?p='. P .'&do=categories');