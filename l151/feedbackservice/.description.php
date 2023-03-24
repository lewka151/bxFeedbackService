<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("L151_FEEDBACK_DESCR_NAME"),
	"DESCRIPTION" => Loc::getMessage("L151_FEEDBACK_DESCR_TEXT"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "CUSTOM",
		"NAME" => Loc::getMessage("L151_FEEDBACK_DESCR_CHILD_NAME"),
		"SORT" => 10,
	),
);