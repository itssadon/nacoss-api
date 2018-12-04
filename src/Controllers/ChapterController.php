<?php
namespace NACOSS\Controllers;

use NACOSS\Controllers\Controller;
use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Chapter;
use NACOSS\Models\ChapterDue;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ChapterController extends Controller {
  protected $requiredParams = [
    'school_alias', 'school_name', 'chapter_name', 'zone_id', 'chapter_reg_num', 'chapter_email', 'address'
  ];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function addChapter(Request $request, Response $response, $args) {
    $endpoint = $this->getPath($request);
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    try {
      $chapter = new Chapter();
      $chapter->school_name = ucwords($params['school_name']);
      $chapter->school_alias = $params['school_alias'];
      $chapter->chapter_name = strtoupper($params['chapter_name']);
      $chapter->zone_id = $params['zone_id'];
      $chapter->chapter_reg_num = UniqueIdHelper::generateChapterRegNum($params['school_alias']);
      $chapter->chapter_email = $params['chapter_email'];
      $chapter->address = $params['address'];
      $chapter->save();

      $chapterPayload = $chapter->fresh()->getPayload();

      return $response->withJson(["chapter"=> $chapterPayload, 'message'=> 'Your chapter registration was successful'], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getAllChapters(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $chapters = Chapter::orderBy('created_at', DESC)->get();

      $chapterPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chapterPayload, $chapter->getPayload());
      }

      return $response->withJson(["chapters"=> $chapterPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getActiveChapters(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $chapters = Chapter::leftJoin('chapter_dues', function($join) {
        $join->on('chapters.chapter_name', '=', 'chapter_dues.chapter_name');
      })
      ->whereNotNull('chapter_dues.transaction_ref')
      ->orderBy('chapter_dues.created_at', DESC)
      ->get();

      $chaptersPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chaptersPayload, $chapter->getPayload());
      }

      return $response->withJson(["activeChapters"=> $chaptersPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function updateChapterDue(Request $request, Response $response) {
    $endpoint = $rhis->getPath($request);
    $this->requiredParams = [
      'chapter_name',
      'transaction_ref'
    ];
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    try {
      $chapterDues = Chapter::updteOrCreate($params);
      
      $chapter = Chapter::leftJoin('chapter_dues', function($join) {
        $join->on('chapters.chapter_name', '=', 'chapter_dues.chapter_name');
      })
      ->where('chapter.chapter_name', $params['chapter_name'])
      ->get();

      $chapterPayload = $chapter->fresh()->getPayload();

      return $response->withJson(["chapter"=> $chapterPayload, 'message'=> 'Your chapter dues payment was successful'], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}