<?php

use TMCms\DB\TableTree;
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\Element\CmsButton;
use TMCms\HTML\Cms\Element\CmsSelect;

$from_branch_id = $_GET['from_id'];
if (!$from_branch_id) return;

//$from_branch = q_assoc_row('');

$tree_from = TableTree::getInstance(module_catalogue::$tables['categories'])
    ->addTranslationColumn('title')
    ->setTitleColumn('title')
    ->setOrderColumn('order')
    ->getAsArray4Options($from_branch_id)
;

$tree_full = TableTree::getInstance(module_catalogue::$tables['categories'])
    ->addTranslationColumn('title')
    ->setTitleColumn('title')
    ->setOrderColumn('order')
    ->getAsArray4Options()
;

echo CmsForm::getInstance()
    ->setAction('?p='. P .'&do=_copy_branch&from_id='. $from_branch_id)
    ->setSubmitButton(new CmsButton('Copy branch'))
    ->addField('Selected tree', CmsSelect::getInstance('')->setOptions($tree_from)->html(false))
    ->addField('Copy to', CmsSelect::getInstance('to_id')->setOptions($tree_full)->html(false))
;