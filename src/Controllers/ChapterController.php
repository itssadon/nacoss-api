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
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be registered already exists!'])
        ->withStatus(402);;
    }

    try {
      $chapterRegistration = new ChapterRegistration($params);
      $chapterRegistration->save();

      return $response->withJson(["status"=> true, 'message'=> 'Your chapter registration has been logged.'])->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function completeChapterRegistration(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
    $this->requiredParams = [
      'school_alias',
      'school_name',
      'chapter_name',
      'zone_id',
      'chapter_email',
      'address',
      'hod_name',
      'hod_phone',
      'transaction_ref'
    ];
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    $chapterExists = Chapter::where('chapter_name', $params['chapter_name'])->exists();
    if ($chapterExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter already exists!'])
        ->withStatus(402);
    }

    $chapterRegExists = ChapterRegistration::where(['chapter_name'=> $params['chapter_name'], 'transaction_ref'=> $params['transaction_ref']])->exists();
    if (!$chapterRegExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter registration has not been logged.'])
        ->withStatus(402);
    }

    $chapterPaymentExists = Transaction::where(['transaction_ref'=> $params['transaction_ref'], 'response_code'=> '00', 'purpose_id'=> 'chapter_reg'])->exists();
    if (!$chapterPaymentExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be registered has not paid required registration fee.'])
        ->withStatus(402);
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
      $chapter->hod_name = $params['hod_name'];
      $chapter->hod_phone = $params['hod_phone'];
      $chapter->slogan = ($params['chapter_slogan']) ? $params['chapter_slogan'] : null;
      $chapter->logo = ($params['chapter_logo_url']) ? $params['chapter_logo_url'] : null;
      $chapter->save();

      $chapterPayload = $chapter->fresh()->getPayload();

      return $response->withJson(['status'=> true, 'message'=> 'Your chapter registration has completed. Proceed to activate your chapter.', "chapter"=> $chapterPayload])->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getAllChapters(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $chapters = Chapter::leftJoin('zones', function($join) {
          $join->on('chapters.zone_id', '=', 'zones.zone_id');
        })
        ->orderBy('chapters.school_name', ASC)
        ->get();

      $chapterPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chapterPayload, $chapter->getPayload($chapter));
      }

      return $response->withJson(["chapters"=> $chapterPayload])->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function searchChapter(Request $request, Response $response, $args) {
    $endpoint = $this->getPath($request);
    $searchTerm = $args['search-term'];
    $chapters = null;

    try {
      if (is_null($searchTerm) || $searchTerm === '') {
        $chapters = Chapter::leftJoin('zones', function($join) {
            $join->on('chapters.zone_id', '=', 'zones.zone_id');
          })
          ->orderBy('chapters.school_name', ASC)
          ->get();
      } else {
        $chapters = Chapter::where('chapters.chapter_name', 'LIKE', "%{$searchTerm}%")
          ->orWhere('chapters.school_alias', 'LIKE', "%{$searchTerm}%")
          ->orWhere('chapters.school_name', 'LIKE', "%{$searchTerm}%")
          ->leftJoin('zones', function($join) {
            $join->on('chapters.zone_id', '=', 'zones.zone_id');
          })
          ->orderBy('chapters.school_name', ASC)
          ->get();
      }

      $chaptersPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chaptersPayload, $chapter->getPayload($chapter));
      }

      return $response->withJson(["chapters"=> $chaptersPayload])->withStatus(200);

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
        ->leftJoin('zones', function($join) {
          $join->on('chapters.zone_id', '=', 'zones.zone_id');
        })
        ->whereNotNull('chapter_dues.transaction_ref')
        ->orderBy('chapter_dues.created_at', DESC)
        ->get();

      $chaptersPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chaptersPayload, $chapter->getPayload($chapter));
      }

      return $response->withJson(["activeChapters"=> $chaptersPayload])->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getInActiveChapters(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $chapters = Chapter::select('chapters.school_alias', 'chapters.school_name', 'chapters.chapter_name', 'chapters.chapter_reg_num', 'zones.zone_name', 'chapters.chapter_email', 'chapters.address', 'chapters.hod_name', 'chapters.hod_phone')
        ->leftJoin('chapter_dues', function($join) {
          $join->on('chapters.chapter_name', '=', 'chapter_dues.chapter_name');
        })
        ->leftJoin('zones', function($join) {
          $join->on('chapters.zone_id', '=', 'zones.zone_id');
        })
        ->whereNull('chapter_dues.transaction_ref')
        ->orderBy('chapter_dues.created_at', DESC)
        ->get();

      $chaptersPayload = [];
      foreach ($chapters as $chapter) {
        array_push($chaptersPayload, $chapter->getPayload($chapter));
      }

      return $response->withJson(["inActiveChapters"=> $chaptersPayload])->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function updateChapterDue(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
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
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be activated was not found!'])
        ->withStatus(402);
    }

    $chapterPaymentExists = Transaction::where(['transaction_ref'=> $params['transaction_ref'], 'response_code'=> '00', 'purpose_id'=> 'chapter_dues'])->exists();
    if (!$chapterPaymentExists) {
      return $response->withJson(['status'=> false, 'message'=> 'Chapter to be activated has not paid required annual due!'])
        ->withStatus(402);
    }

    try {
      $chapterDues = ChapterDue::updateOrCreate(array('chapter_name'=> $params['chapter_name']), $params);

      return $response->withJson(["status"=> true, 'message'=> 'Your chapter activation was successful'])->withStatus(200);
        
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}