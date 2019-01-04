<?php
namespace NACOSS\Controllers\Lookups;

use NACOSS\Controllers\Controller;
use NACOSS\Models\Zone;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ZoneController extends Controller {
  protected $requiredParams = [];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function getAllZones(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $allZones = Zone::orderBy('zone_id', ASC)->get();

      $zonePayload = [];
      foreach ($allZones as $zone) {
        array_push($zonePayload, $zone->getPayload());
      }

      return $response->withJson(["zones"=> $zonePayload])->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}