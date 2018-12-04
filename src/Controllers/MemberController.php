<?php
namespace NACOSS\Controllers;

use NACOSS\Controllers\Controller;
use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Chapter;
use NACOSS\Models\ChapterDue;
use NACOSS\Models\Member;
use NACOSS\Models\Profile;
use NACOSS\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class MemberController extends Controller {
  protected $requiredParams = [
    'school_alias',
    'surname',
    'firstname',
    'email',
    'phone',
    'gender_id',
    'date_of_birth',
    'password'
  ];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function addMember(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    $userExists = User::where('email', $params['email'])->exists();
    if ($userExists) {
      return $response->withJson(['status'=> false, 'message'=> 'User with email already exists!'], 200);
    }

    $phoneExists = Profile::where('phone', $params['phone'])->exists();
    if ($phoneExists) {
      return $response->withJson(['status'=> false, 'message'=> 'User with phone number already exists!'], 200);
    }

    try {
      $member = new Member();
      $member->mrn = UniqueIdHelper::generateNacossId();
      $member->school_alias = $params['school_alias'];
      $member->save();

      $profile = new Profile();
      $profile->mrn = $member->mrn;
      $profile->firstname = $params['firstname'];
      $profile->othername = $params['othername'] || '';
      $profile->gender_id = $params['gender_id'];
      $profile->phone = $params['phone'];
      $profile->date_of_birth = $params['date_of_birth'];
      $profile->save();

      $user = new User;
      $user->mrn = $member->mrn;
      $user->email = $params['email'];
      $user->password  = Hash::make($params['password']);
      $user->save();

      $member = $member->fresh()->getPayload();
      $profile = $profile->fresh()->getPayload();
      $user = $user->fresh()->getPayload();

      $memberPayload = [
        $member,
        $profile,
        $user
      ];

      return $response->withJson(['status'=> true, 'message'=> 'Your membership registration was successful', "member"=> $memberPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getAllMembers(Request $request, Response $response, $args) {
    $endpoint = $this->getPath($request);
    $mrn = $args['mrn'];
    $skip = 0;
    $page_size = 25;

    if (!is_null($request->getQueryParam('start')) && !is_null($request->getQueryParam('page-size'))) {
			$skip = $request->getQueryParam('start');
			$page_size = $request->getQueryParam('page-size');
		}

    try {
      $members = Member::leftJoin('profiles', function($join) {
          $join->on('members.mrn', '=', 'profiles.mrn');
        })
        ->leftJoin('users', function($join) {
          $join->on('members.mrn', '=', 'users.mrn');
        })
        ->skip($start)
        ->take($page_size)
        ->get();

      $membersPayload = [];
      foreach ($members as $member) {
        array_push($membersPayload, $member->getFullPayload($member));
      }

      return $response->withJson(["members"=> $membersPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}