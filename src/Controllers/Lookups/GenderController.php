<?php
namespace NACOSS\Controllers\Lookups;

use NACOSS\Controllers\Controller;
use NACOSS\Models\Gender;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class GenderController extends Controller {
  protected $requiredParams = [];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function getAllGender(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $allGender = Gender::orderBy('gender_id', ASC)->get();

      $genderPayload = [];
      foreach ($allGender as $gender) {
        array_push($genderPayload, $gender->getPayload());
      }

      return $response->withJson(["gender"=> $genderPayload])
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}