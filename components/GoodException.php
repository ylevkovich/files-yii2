<?php

namespace app\components;

use yii;
use yii\base\ExitException;

/**
 * Исключение, которое будет автоматически обрабатываться на уровне yii\base\Application
 */
class GoodException extends ExitException
{
    /**
     * Конструктор
     * @param string $name Название (выведем в качестве названия страницы)
     * @param string $message Подробное сообщение об ошибке
     * @param int $code Код ошибки
     * @param int $status Статус ответа
     * @param \Exception $previous Предыдущее исключение
     */
    public function __construct($name, $message = null, $code = 0, $status = 500, \Exception $previous = null)
    {
        # Генерируем ответ
        $view = yii::$app->getView();
        $response = yii::$app->getResponse();
        $response->data = $view->renderFile('@app/views/exception.php', [
            'name' => $name,
            'message' => $message,
        ]);

        # Возвратим нужный статус (по-умолчанию отдадим 500-й)
        $response->setStatusCode($status);

        parent::__construct($status, $message, $code, $previous);
    }
}