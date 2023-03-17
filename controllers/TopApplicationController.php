<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\TopApplicationPosition;
use app\components\Toolkit;
use app\components\FileLog;
/**
 * контроллер для топа приложений
 */
class TopApplicationController extends Controller
{
    /** атрибуты ответа */
    private $answer;

    public function beforeAction($action)
    {
        /**
         * проверка на лимит запросов
         */
        if (!Toolkit::checkLimitRequest()) {
            $this->sendAnswer(500, 'Too many request');
        }

        return parent::beforeAction($action);
    }

    /**
     * возвращает статистику приложений по дате
     * принимает дату в url строке
     */
    public function actionAppTopCategory()
    {
        $date = $_GET['date'];

        if (!$date || !Toolkit::validateDate($date)) {
            $this->sendAnswer(500, 'Set date to format Y-m-d');
        }

        $data = TopApplicationPosition::getTopAppByDate($date);

        if (!$data) {
            $this->sendAnswer(404, 'Not Found');
        }
        
        $this->answer['data'] = $data;
        $this->sendAnswer(200, 'ok');
    }

    /**
     * общая функция для ответа по запросу
     * @param int $status_code - код ответа
     * @param string $message - сообщение ответа
     * @param int $jsonFlags - доп флаги ответа
     */
    private function sendAnswer(int $status_code, string $message = '', int $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK)
    {
        (new FileLog)->info("status_code = $status_code", "message = $message");
        $this->answer['status_code'] = $status_code;
        $this->answer['message'] = $message;

        echo json_encode($this->answer, $jsonFlags);
        exit;
    }
}
