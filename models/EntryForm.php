<?php

namespace app\models;

use Yii;
use app\components\BaseModel;

class EntryForm extends BaseModel
{
    public $name;
    public $email;

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            ['email', 'email'],
        ];
    }
}