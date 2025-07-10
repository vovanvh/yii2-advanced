<?php
namespace console\controllers;

use yii\console\Controller;
use common\models\User;

class UserController extends Controller
{
    public function actionCreate($username, $email, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        if ($user->save()) {
            echo "User '{$username}' created.\n";
        } else {
            print_r($user->getErrors());
        }
    }
}
