<?php
namespace L151\Components;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require_once('lib/vendor/autoload.php');

use Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Error,
    Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule('highloadblock');
Loc::loadMessages(__FILE__);

class FeedbackService extends \CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable, \Bitrix\Main\Errorable
{
    /** @var ErrorCollection **/
    protected $errorCollection;

    public function configureActions()
    {
        return [
            "sendForm" => [
                "prefilters" => [
                    new ActionFilter\HttpMethod(
                        [
                            ActionFilter\HttpMethod::METHOD_POST
                        ]
                    ),
                    new ActionFilter\Csrf()
                ],
            ],
        ];
    }

    protected function listKeysSignedParameters()
    {
        return [
            "HBLOCK_ID",
        ];
    }

    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new \Bitrix\Main\ErrorCollection();
        return $arParams;
    }


    /**
     * * @throws \Bitrix\Main\ObjectException 
     */
    public function sendFormAction($post)
    {   
        $this->getSignedParameters();
        $response = ["message" => Loc::GetMessage("L151_FEEDBACK_FINAL_MESSAGE")];

        if( !empty($post['detected']) )
        {
            $this->errorCollection[] = new Error("Simple bot detected.");
            return null;
        }

        $validator = new \Rakit\Validation\Validator;
        $validation = $validator->validate($post, [
            "name"                  => "required",
            "email"                 => "required|email",
            "rating"                => "required|numeric|min:1|max:5",
            "comment"               => "required|max:500",
        ]);

        if( $validation->fails() )
        {
            $this->errorCollection[] = new Error( $validation->errors()->all() );
            return null;
        }

        $hblockEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity( $this->arParams["HBLOCK_ID"] );
        $hblockDataClass = $hblockEntity->getDataClass();
        $hblockFields = [
            "UF_NAME"         => $post["name"],
            "UF_EMAIL"        => $post["email"],
            "UF_RATION"       => $post["rating"],
            "UF_COMMENT"      => $post["comment"],
        ];

        $hblockResult = $hblockDataClass::add($hblockFields);

        if( !$hblockResult->isSuccess() )
        {   
            throw new \Bitrix\Main\ObjectException( $hblockResult->getErrors() );
        }
        
        $response["mail_id"] = $this->sendEmail($post);
        return $response;
    }

    private function sendEmail($data)
    {
        //дефолтный шаблон обратной связи
        $arMail = [
            "DEFAULT_EMAIL_FROM"   => $data["email"],
            "AUTHOR_EMAIL"         => $data["email"],
            "AUTHOR"               => $data["name"],
            "EMAIL_TO"             => \Bitrix\Main\Config\Option::get("main", "email_from"),
            "TEXT"                 => $data["comment"] . PHP_EOL.PHP_EOL . Loc::GetMessage('L151_FEEDBACK_MAIL_RATING') . $data["rating"],
        ];

        return \CEvent::Send("FEEDBACK_FORM", SITE_ID, $arMail);
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

    /**
     * Getting array of errors.
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    /**
     * Getting once error with the necessary code.
     * @param string $code Code of error.
     * @return Error
     */
    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

}