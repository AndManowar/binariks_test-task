<?php

namespace App\Api\V1\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
/**
 * Class Controller
 *
 * @SWG\Swagger(
 *    basePath="/",
 *    @SWG\Info(
 *       version="1.0",
 *       title="Test Task API",
 *       description="",
 *       termsOfService="",
 *    ),
 *
 *   @SWG\Definition(
 *      definition="ValidationErrors",
 *      type="object",
 *      @SWG\Property(property="message", type="string"),
 *      @SWG\Property(property="errors", type="object",
 *        @SWG\Property(property="field", type="array",
 *           @SWG\Items(type="string")
 *        ),
 *      ),
 *      @SWG\Property(property="status_code", type="integer"),
 *   ),
 *
 *   @SWG\Definition(
 *      definition="Error",
 *      type="object",
 *      @SWG\Property(property="message", type="string"),
 *      @SWG\Property(property="status_code", type="integer"),
 *   ),
 *
 *   @SWG\Definition(
 *      definition="Meta",
 *      type="object",
 *      @SWG\Property(property="pagination", ref="#/definitions/Pagination"),
 *   ),
 *
 *   @SWG\Definition(
 *      definition="Pagination",
 *      type="object",
 *      @SWG\Property(property="total", type="integer"),
 *      @SWG\Property(property="count", type="integer"),
 *      @SWG\Property(property="per_page", type="integer"),
 *      @SWG\Property(property="current_page", type="integer"),
 *      @SWG\Property(property="total_pages", type="integer"),
 *      @SWG\Property(property="links", type="object", @SWG\Property(property="next", type="string")),
 *   ),
 * )
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;
}
