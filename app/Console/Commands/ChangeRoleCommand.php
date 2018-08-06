<?php

namespace App\Console\Commands;

use App\Models\Db\Task;
use App\Models\Db\User;
use DB;
use Illuminate\Console\Command;

/**
 * Комманда для изменения роли пользователя(лей) по [id]
 *
 * Class ChangeRoleCommand
 * @package App\Console\Commands
 */
class ChangeRoleCommand extends Command
{
    /**
     * Причина отмены задачи
     *
     * @const
     */
    const CANCELLING_REASON = "User has changed role in the system and can’t finish the task";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-role:change {--id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo 'Такс, начинаем обработку пользователей'.PHP_EOL;
        foreach ($this->option('id') as $id) {
            echo 'Пользователь с id = '.$id.PHP_EOL;
            /** @var User $user */
            $user = User::findOrFail($id);
            // В задании условие - если роль с исполнителя на руководителя, значит если щас роль руководитель - не меняем
            if ($user->role === User::ROLE_OWNER) {
                echo "А пользователь {$user->email}/{$user->id} то не исполнитель".PHP_EOL;
                continue;
            }
            $this->processRoleChanging($user);
        }
        echo 'Всех обработали'.PHP_EOL;

        return true;
    }

    /**
     * Процедура изменения роли
     *
     * @param User $user
     * @return void
     */
    private function processRoleChanging(User $user): void
    {
        DB::beginTransaction();

        if (!$this->changeRole($user) || !$this->cancelPerformerTasks($user) || !$this->getOutFromOrganizations($user)) {
            DB::rollBack();
        }

        DB::commit();
    }

    /**
     * Отменить задачи исполнителя
     *
     * @param User $performer
     * @return bool
     */
    private function cancelPerformerTasks(User $performer): bool
    {
        foreach ($performer->tasks as $task) {
            $task->status = Task::STATUS_CANCELLED;
            $task->cancellation_reason = self::CANCELLING_REASON;
            if (!$task->save()) {
                return false;
            }
            echo "Отменили задачу {$task->name}".PHP_EOL;
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    private function getOutFromOrganizations(User $user): bool
    {
        foreach ($user->organizations as $userOrganization) {
            if (!$userOrganization->delete()) {
                return false;
            }
            echo "Выходим из организации {$userOrganization->organization->organization_name}".PHP_EOL;
        }

        return true;
    }

    /**
     * Меняем роль на руководителя
     *
     * @param User $user
     * @return bool
     */
    private function changeRole(User $user): bool
    {
        echo 'Меняем роль'.PHP_EOL;

        $user->role = User::ROLE_OWNER;

        return $user->save();
    }
}
