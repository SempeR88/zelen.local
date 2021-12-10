<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Order;

class OrderController extends Controller
{
    /**
     * @param string $url
     * @return void
     */
    public function actionUpdateNet(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo 'URL некорректен!\n';
            return 1;
        } else {
            Order::getOrdersFromUrl($url);
            return 0;
        }
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function actionUpdateLocal(string $filePath)
    {
        if (pathinfo($filePath, PATHINFO_EXTENSION) != 'json') {
            echo 'Файл не формата json!\n';
            return 1;
        } else {
            Order::getOrdersFromFile($filePath);
            return 0;
        }
    }

    /**
     * @param string $orderId
     * @return void
     */
    public function actionInfo(string $orderId)
    {
        if (!is_numeric($orderId)) {
            echo 'Введите число!\n';
            return 1;
        } else {
            echo Order::getInJson($orderId);
            return 0;
        }
    }
}