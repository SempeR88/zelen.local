<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property string $user_name
 * @property string $user_phone
 * @property string $warehouse_id
 * @property int $status
 * @property int $items_count
 * @property string $created_at
 * @property string|null $updated_at
 */
class Order extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_order';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'status', 'items_count'], 'integer'],
            [['user_name', 'user_phone'], 'string', 'max' => 255],
            [['warehouse_id'], 'string', 'max' => 10],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'user_phone' => 'User Phone',
            'warehouse_id' => 'Warehouse ID',
            'status' => 'Status',
            'items_count' => 'Items Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param string $url
     * @return void
     */
    public static function getOrdersFromUrl(string $url)
    {
        $orders = self::takeOrdersFromUrl($url);
        $orderIds = self::getIds();

        self::saveOrders($orders, $orderIds);
    }

    /**
     * @param string $filePath
     * @return void
     */
    public static function getOrdersFromFile(string $filePath)
    {
        $orders = self::takeOrdersFromFile($filePath);
        $orderIds = self::getIds();

        self::saveOrders($orders, $orderIds);
    }

    /**
     * @param array $orders
     * @param array $orderIds
     * @return void
     */
    private static function saveOrders(array $orders, array $orderIds)
    {
        self::getDb()->transaction(function($db) use ($orders, $orderIds) {
            foreach ($orders as $order) {
                if (in_array($order['id'], $orderIds)) {
                    $model = self::getById($order['id']);
                } else {
                    $model = new self();
                }
                $model->attributes = $order;
                $model->items_count = count($order['items']);
                $model->save();
            }
        });
    }

    /**
     * @param string $url
     * @return array
     */
    private static function takeOrdersFromUrl(string $url): array
    {
        $json = file_get_contents($url);

        return self::takeOrders($json);
    }

    /**
     * @param string $filePath
     * @return array
     */
    private static function takeOrdersFromFile(string $filePath): array
    {
        $json = file_get_contents(Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $filePath);

        return self::takeOrders($json);
    }

    private static function takeOrders(string $json): array
    {
        $check = Json::decode($json);
        return $check['orders'];
    }

    /**
     * @param integer $id
     * @return self
     */
    private static function getById(int $id): self
    {
        return self::find()->where(['id' => $id])->one();
    }

    /**
     * @return array
     */
    private static function getIds(): array
    {
        return self::find()->select('id')->asArray()->column();
    }

    /**
     * @param string $id
     * @return string
     */
    public static function getInJson(string $id): string
    {
        $orderId = (int)$id;
        $order = self::find()->where(['id' => $orderId])->asArray()->one();
        $json = ($order !== null) ? json_encode($order, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : 'Заказа не существует!';
        return $json;
    }
}