<?php

namespace app\components;

use DateTime;
use Yii;

/**
 * класс для лимитов по запросам
 * @todo все параметры можно вынести в базу и переделать в модель. Вероятно понадобятся еще параметры, едва ли запросы будут нормированы
 */
class RequestsLimitParams
{
    /**
     * получить все параметры по лимитам запросов
     * @return array
     */
    public static function getParamsCurrentRequest()
    {
        $action_id = Yii::$app->controller->action->id;

        $model = new self;
        $keys = $model->getKeys();
        $limits = $model->getCountLimits();
        $time_outs = $model->getTimeOuts();

        return [
            'key' => $keys[$action_id],
            'limit' => $limits[$action_id],
            'time_out' => $time_outs[$action_id],
        ];
    }

    /**
     * массив лимитов по кол-во запросов
     * @return array
     */
    private function getCountLimits() : array
    {
        return [
            'app-top-category' => 5,
        ];
    }

    /**
     * массив периода времени для лимита
     * @return array
     */
    private function getTimeOuts() : array
    {
        return [
            'app-top-category' => 60,
        ];
    }

    /**
     * массив ключей
     * @return array
     */
    private function getKeys() : array
    {
        return [
            'app-top-category' => Yii::$app->getRequest()->getUserIP() . 'AppTopCategory',
        ];
    }
}
