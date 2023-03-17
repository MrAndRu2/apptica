<?php

namespace app\components;

use Yii;

/**класс для записи логов*/
class FileLog
{
    /**название файла лога */
    private $_logName;
    /**файл */
    private $_file;
    /**формат */
    private $_fileFormat = '.log';

    /**
     * @param string $logName
     */
    public function __construct(string $logName = 'index')
    {
        $this->_logName = str_replace('-', '_', $logName);

        $this->openFile();
    }

    /**
     * открываем файл для записи
     * @return void
     */
    private function openFile()
    {
        $path = $this->filePath();

        $needChmod = !file_exists($path);
        $this->_file = fopen($path, 'a');
        if ($needChmod) {
            chmod($path, 0777);
        }
    }

    /**
     * получаем путь до файла
     * @return string
     */
    private function filePath(): string
    {
        /**путь состоит в основном из пришедшего запроса */
        $base = Yii::$app->basePath . '/fileLog/' . Yii::$app->request->getPathInfo() . '/';

        if (!is_dir($base)) {
            @mkdir($base, 0777, true);
        }

        return $base . $this->_logName . $this->_fileFormat;
    }

    /**
     * записываем в файл сообщение
     * @param string $message
     * @param string $context
     * @return void
     */
    public function info(string $message, string $context = '')
    {
        fwrite(
            $this->_file,
            '[' .  date('Y-m-d H:i:s') . '][ ip: ' . Yii::$app->getRequest()->getUserIP() . '] ' . $message . ' ' . print_r($context, true) . PHP_EOL
        );
    }
}
