<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\TopApplicationPosition;

/**
 * класс команд для статистики по приложениям
 * умеет выгружать и сохранять статистику по приложениям со стороннего ресурса
 */
class  TopApplicationController extends Controller
{
    /**
     * команда запроса для выгрузки статистики с стороннего ресурса (api.apptica.com) в таблицу модели TopApplicationPosition
     * @param string $date_from
     * @param string $date_to
     */
    public function actionAppAddTopData(string $date_from = '', string $date_to = '')
    {
        $data = self::getAppTopData($date_from, $date_to);
        self::saveTopData($data);
        exit;
    }

    /**
     * запрос для выгрузки данных с (api.apptica.com)
     * @param string $date_from - дата от
     * @param string $date_to - дата до 
     * @return array|void
     */
    private static function getAppTopData(string $date_from, string $date_to)
    {
        /** 
         * @todo конечно по хорошему ссылка делится на состовные части и выносится в параметры, но для теста оставил так
         */
        $url = "https://api.apptica.com/package/top_history/1421444/1?date_from=$date_from&date_to=$date_to&B4NKGg=fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l";
        $headers = [
            'Content-Type: application/json',
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 45);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $json_answer = curl_exec($curl);
        curl_close($curl);

        $answer = json_decode($json_answer, true);

        if ($answer['status_code'] == 200) {
            return $answer['data'];
        } else {
            echo 'Ошибка запроса, status_code = ' . $answer['status_code'];
            exit;
        }
    }

    /**
     * обработка и сохранение данных для таблицы топа приложений
     * @param array $data - данные для топа
     */
    private static function saveTopData($data)
    {
        $formatData = [];
        /**идем по каждой категории */
        foreach ($data as $cat_id => $category) {
            /**нужен только первый ключ */
            $current_category_data = reset($category);
            
            /**идем в первой подкатегории по каждой дате */
            foreach ($current_category_data as $date => $position) {
                $formatData[] = [
                     $cat_id,
                     $position,
                     $date,
                ] ;
            }
        }
        
        Yii::$app->db->createCommand()->batchInsertIgnore(
            TopApplicationPosition::tableName(),
            ['category_id', 'position', 'date'],
            $formatData,
            
        )->execute();

        echo 'Новые данные сохранены';
    }
}
