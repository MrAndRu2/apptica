<?php

namespace app\components;

use DateTime;
use Yii;

/**
 * вспомогательный класс для инструментов|форматирования|валидаций
 */
class Toolkit
{

    /**
     * проверяет дату на нужный формат
     * @param string $date - дата
     * @param string $format - формат даты
     * @return bool
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $DateTime = DateTime::createFromFormat($format, $date);
        return $DateTime && $DateTime->format($format) == $date;
    }

    /**
     * проверяет лимит запросов
     * хранит значения в кеше и делает проверку на кол-во запросов
     * @return bool
     */
    public static function checkLimitRequest(): bool
    {
        /**берем нужные параметры по запросу*/
        $params = RequestsLimitParams::getParamsCurrentRequest();

        /**вернем true если параметров нет - валидация не нужна */
        if (!$params) {
            return true;
        }

        $current_time = time();

        $data = Yii::$app->cache->get($params['key']);
        /**сразу считаем кол-во запросов */
        $count_request = $data['count_request'] + 1;

        /**если запросов еще не было */
        if (!$data) {
            Yii::$app->cache->set($params['key'], ['first_time' => $current_time, 'count_request' => 1], $params['time_out']);
            return true;
        }

        /**если лимит превышен */
        if ($count_request > $params['limit']) {
            return false;
        }
        /**
         * считаем время хранения ключа через внутренний параметр. 
         * По итогу ключ должен храниться $params['time_out'] с первой записи, и не изменять параметр duration после повторных запросов
         */
        $duration = $params['time_out'] - ($current_time - $data['first_time']);
        Yii::$app->cache->set($params['key'], ['first_time' => $data['first_time'], 'count_request' => $count_request], $duration);

        return true;
    }
}
