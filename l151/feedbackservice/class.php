<?php
namespace L151\Components;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require_once('lib/vendor/autoload.php');

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule('highloadblock');
Loc::loadMessages(__FILE__);

class FeedbackService extends \CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable, \Bitrix\Main\Errorable
{
    /** @var ErrorCollection **/
    protected $errorCollection;

    public function configureActions()
    {
        return [
            'sendForm' => [
                'prefilters' => [
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

    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new \Bitrix\Main\ErrorCollection();
        return $arParams;
    }

    public function sendFormAction($post)
    {
        if( empty($post['detected']) )
        {
            $response = ["message" => Loc::GetMessage("L151_FEEDBACK_FINAL_MESSAGE")];

            $post["hblockid"] = $this->decodeParam($post["hblockid"]);

            $validator = new \Rakit\Validation\Validator;
            $validation = $validator->validate($post, [
                "name"                  => "required",
                "email"                 => "required|email",
                "rating"                => "required|numeric|min:1|max:5",
                "comment"               => "required|max:500",
                "hblockid"              => "required|numeric",
            ]);

            if(!$validation->fails())
            {   
                $hblockEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($post["hblockid"]);
                $hblockDataClass = $hblockEntity->getDataClass();
                $hblockFields = [
                    "UF_NAME"         => $post["name"],
                    "UF_EMAIL"        => $post["email"],
                    "UF_RATION"       => $post["rating"],
                    "UF_COMMENT"      => $post["comment"],
                ];

                $hblockResult = $hblockDataClass::add($hblockFields);

                if($hblockResult->isSuccess())
                {
                    $response["mail_id"] = $this->sendEmail($post);
                    return $response;
                }
                else
                {
                    $this->errorCollection[] = new Error( $hblockResult->getErrors() );
                    return null;
                }
            }
            else
            {
                $this->errorCollection[] = new Error( $validation->errors()->all() );
                return null;
            }
        }
        else
        {
            $this->errorCollection[] = new Error("Simple bot detected.");
            return null;
        }
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

    public function encodeParam($param)
    {
        return htmlspecialchars(json_encode(base64_encode($param)));
    }

    public function decodeParam($param)
    {
        return htmlspecialchars_decode(base64_decode(json_decode($param)));
    }

    public function executeComponent()
    {
        if($this->startResultCache())
        {
            $this->arResult["HBLOCK_ID"] = $this->encodeParam($this->arParams["HBLOCK_ID"]);
            $this->includeComponentTemplate();
        }
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