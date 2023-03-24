<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule('highloadblock');
Loc::loadMessages(__FILE__);

$hBlocks = [];
$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList();
while($hBlock = $rsData->fetch())
{
	$hBlocks[$hBlock["ID"]] = $hBlock["NAME"] . "[" . $hBlock["ID"] . "]";
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"HBLOCK_ID" => array(
			"NAME" => Loc::getMessage("L151_FEEDBACK_HIGHLOADBLOCK_ID"),
			"PARENT" => "BASE",
			"NAME" => "HighloadBlock ID",
			"TYPE" => "LIST",
			"VALUES" => $hBlocks,
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
	),
);