<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 16:07
 */

namespace App\Transformers;

use App\Models\Db\User;
use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer
 * @package App\Transformers
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id'                => $user->id,
            'surname'           => $user->surname,
            'name'              => $user->name,
            'email'             => $user->email,
            'registration_date' => $user->created_at->format('d/m/Y'),
            'role'              => $user->role,
        ];
    }
}