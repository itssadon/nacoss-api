<?php
namespace NACOSS\Controllers;

use NACOSS\Controllers\Controller;
use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Chapter;
use NACOSS\Models\ChapterDue;
use NACOSS\Models\ChapterRegistration;
use NACOSS\Models\Transaction;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ChapterController extends Controller {
  protected $requiredParams = [];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function addChapter(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    $this->requiredParams = [
      'transaction_ref',
      'chapter_email',
      'chapter_name'
    ];
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    $chapterExists = Chapter::where('chapter_name', $params['chapter_name'])->exists();
    if ($chapterExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be registered already exists!'], 200);
    }

    try {
      $transaction = new Transaction();
      $transaction->transaction_ref = $params['transaction_ref'];
      $transaction->email = $params['chapter_email'];
      $transaction->amount = 20000;
      $transaction->save();

      $chapterRegistration = new ChapterRegistration($params);
      $chapterRegistration->save();

      return $response->withJson(["status"=> true, 'message'=> 'Your chapter registration has been logged. Proceed to payment.'], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function updateChapterRegistration(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
    $this->requiredParams = [
      'school_alias',
      'school_name',
      'chapter_name',
      'zone_id',
      'chapter_email',
      'address',
      'transaction_ref'
    ];
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    $chapterExists = Chapter::where('chapter_name', $params['chapter_name'])->exists();
    if ($chapterExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter already exists!'], 200);
    }

    $chapterRegExists = ChapterRegistration::where(['chapter_name'=> $params['chapter_name'], 'transaction_ref'=> $params['transaction_ref']])->exists();
    if (!$chapterRegExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter registration has not been logged.'], 200);
    }

    $chapterPaymentExists = Transaction::where(['transaction_ref'=> $params['transaction_ref'], 'response_code'=> '00', 'purpose_id'=> 'chapter_reg'])->exists();
    if (!$chapterPaymentExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be registered has not paid required registration fee.'], 200);
    }

    try {
      $chapter = new Chapter();
      $chapter->school_name = ucwords($params['school_name']);
      $chapter->school_alias = $params['school_alias'];
      $chapter->chapter_name = strtoupper($params['chapter_name']);
      $chapter->zone_id = $params['zone_id'];
      $chapter->chapter_reg_num = UniqueIdHelper::generateChapterRegNum($params['school_alias']);
      $chapter->chapter_email = strtolower($params['chapter_email']);
      $chapter->address = $params['address'];
      $chapter->slogan = ($params['chapter_slogan']) ? $params['chapter_slogan'] : null;
      $chapter->logo = ($params['chapter_logo_url']) ? $params['chapter_logo_url'] : null;
      $chapter->save();

      $chapterPayload = $chapter->fresh()->getPayload();

      return $response->withJson(['status'=> true, 'message'=> 'Your chapter registration has completed. Proceed to activate your chapter.', "chapter"=> $chapterPayload], 200);
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

  public function searchChapter(Request $request, Response $response, $args) {
    $endpoint = $this->getPath($request);
    $searchTerm = $args['search-term'];

    try {
      $chapters = Chapter::where('chapters.chapter_name', 'LIKE', "%{$searchTerm}%")
        ->orWhere('chapters.school_alias', 'LIKE', "%{$searchTerm}%")
        ->orWhere('chapters.school_name', 'LIKE', "%{$searchTerm}%")
        ->leftJoin('chapter_dues', function($join) {
          $join->on('chapters.chapter_name', '=', 'chapter_dues.chapter_name');
        })
        ->whereNotNull('chapter_dues.transaction_ref')
        ->orderBy('chapter_dues.created_at', DESC)
        ->get();

      $chaptersPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chaptersPayload, $chapter->getPayload());
      }

      return $response->withJson(["chapters"=> $chaptersPayload], 200);
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

    $chapterExists = Chapter::where('chapter_name', $params['chapter_name'])->exists();
    if (!$chapterExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be activated was not found!'], 200);
    }

    $chapterPaymentExists = Transaction::where(['transaction_ref'=> $params['transaction_ref'], 'response_code'=> '00', 'purpose_id'=> 'chapter_dues'])->exists();
    if (!$chapterPaymentExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be activated has not paid required annual due!'], 200);
    }

    try {
      $chapterDues = ChapterDue::updteOrCreate($params);

      return $response->withJson(["status"=> true, 'message'=> 'Your chapter activation was successful'], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}