<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 12:30
 */

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Трейт для логирования евентов
 *
 * Class LoggerTrait
 * @package App\Traits
 */
trait EventLoggerTrait
{
    /**
     * Канал с логами
     *
     * @const
     */
    private $channelName = 'eventLog';

    /**
     * Запись сообщения в лог
     *
     * @param string $message
     */
    public function writeToLog(string $message): void
    {
        Log::channel($this->channelName)->debug($message);
    }
}