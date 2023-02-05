<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use app\models\AirportName;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Страница вывода командировок
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function actionTrips()
    {
        $sf = new \app\models\SearchForm();
        $sf->setAttributes($this->request->get());
        if ($this->request->isPost && ($params = $sf->getParamsForRedirect($this->request->post())) !== false) {
            array_unshift($params, '');
            return $this->redirect($params);
        }

        return $this->render('trips', [
            'form' => $sf,
        ]);
    }


    /**
     * выполнение автоподстановки для поля Аэропорта
     *
     * @param      string  $q      Поисковый запрос введённный в поле
     *
     */
    public function actionAirPortList($q = null)
    {
        $this->response->format = \yii\web\Response::FORMAT_JSON;
        return ['results' => AirportName::getSuggestByName($q)];
    }


}
