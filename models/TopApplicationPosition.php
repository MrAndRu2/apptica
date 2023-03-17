<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "top_application_position".
 *
 * @property int $id
 * @property int $category_id
 * @property int $position
 * @property string $date
 */
class TopApplicationPosition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'top_application_position';
    }

    public function __construct($category_id, $position, $date)
    {
        $this->category_id = $category_id;
        $this->position = $position;
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'position', 'date'], 'required'],
            [['category_id', 'position'], 'integer'],
            [['date'], 'safe'],
            [['category_id', 'date'], 'unique', 'targetAttribute' => ['category_id', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'position' => 'Position',
            'date' => 'Date',
        ];
    }

    /**
     * возвращает форматированные данные топа приложений за определенную дату
     * @param string $date - дата в формате (Y-m-d)
     * @return array $answer - ассоциативный массив [category_id] => position
     */
    public static function getTopAppByDate(string $date) : array
    {
        $rows = (new Query())->
        select(['category_id', 'position'])
        ->from(self::tableName())
        ->where('date = :date', [':date' => $date])
        ->all(Yii::$app->db);

        $answer = [];

        foreach($rows as $row){
            $answer[$row['category_id']] = $row['position'];
        }

        return $answer;
    } 

    
}
