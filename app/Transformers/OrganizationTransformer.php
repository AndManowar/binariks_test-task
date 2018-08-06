<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 16:19
 */

namespace App\Transformers;

use App\Models\Db\Organization;
use League\Fractal\TransformerAbstract;

/**
 * Class OrganizationTransformer
 * @package App\Transformers
 */
class OrganizationTransformer extends TransformerAbstract
{
    /**
     * @param Organization $organization
     * @return array
     */
    public function transform(Organization $organization): array
    {
        return [
            'id'                => $organization->id,
            'name'              => $organization->organization_name,
            'registration_date' => $organization->registration_date,
            'owner'             => (new UserTransformer())->transform($organization->owner),
        ];
    }
}