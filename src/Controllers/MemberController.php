<?php
namespace NACOSS\Controllers;

use NACOSS\Controllers\Controller;
use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Chapter;
use NACOSS\Models\ChapterDue;
use NACOSS\Models\Member;
use NACOSS\Models\Profile;
use NACOSS\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Respect\Validation\Validator as Rule;
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

    $signUpRules = $this->getRulesForSignUp();

		$validator = $this->getValidator($request, $signUpRules);
		if (!$validator->isValid()) {
			$customErrorPayload = $this->getCustomErrorPayload($endpoint, 'Invalid parameter(s).', 422, 'Some parameters provided are invalid.');
			return $response->withJson($customErrorPayload, $customErrorPayload['code']);
		}

    $userExists = User::where('email', $params['email'])->exists();
    if ($userExists) {
      $customErrorPayload = $this->getCustomErrorPayload($endpoint, 'Member exists.', 422, 'Member with email address already exists!');
			return $response->withJson($customErrorPayload, $customErrorPayload['code']);
    }

    $phoneExists = Profile::where('phone', $params['phone'])->exists();
    if ($phoneExists) {
      $customErrorPayload = $this->getCustomErrorPayload($endpoint, 'Member exists.', 422, 'Member with phone number already exists!');
			return $response->withJson($customErrorPayload, $customErrorPayload['code']);
    }

    try {
      $member = new Member();
      $member->mrn = UniqueIdHelper::generateNacossId();
      $member->school_alias = $params['school_alias'];
      $member->save();

      $profile = new Profile();
      $profile->mrn = $member->mrn;
      $profile->surname = $params['surname'];
      $profile->firstname = $params['firstname'];
      $profile->othername = ($params['othername']) ? $params['othername'] : '';
      $profile->gender_id = $params['gender_id'];
      $profile->phone = $params['phone'];
      $profile->date_of_birth = $params['date_of_birth'];
      $profile->save();

      $user = new User;
      $user->mrn = $member->mrn;
      $user->email = strtolower($params['email']);
      $user->password  = password_hash($params['password'], PASSWORD_BCRYPT, ['cost'=> 10]);
      $user->save();

      $messageType = "welcome_email";
			$vars = [
				'surname' => $profile->surname,
				'firstname' => $profile->firstname,
				'mrn' => $user->mrn,
        'email' => $user->email,
        'password' => $params['password'],
				'copyright_year' => $this->getCopyrightYear()
			];

			try {
				$messageTemplate = $this->getMessageTemplate($messageType);

				if (empty($messageTemplate)) {
					$templateNotFoundPayLoad = $this->getTemplateNotFoundPayload($endpoint);
					return $response->withJson($templateNotFoundPayLoad, 500);
				}

				$subject = str_replace('[{FNAME}]', $profile->firstname, $messageTemplate->subject);
				$message = new MessageController($messageTemplate->body, $vars);

			} catch (QueryException $dbException) {
				$databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
				return $response->withJson($databaseErrorPayload, 500);
			}

      $member = $member->fresh()->getPayload();
      $profile = $profile->fresh()->getPayload();
      $user = $user->fresh()->getPayload();

      $memberPayload = [
        'member'=> $member
      ];

      return $response->withJson(['status'=> true, 'message'=> 'Your membership registration was successful', "memberDetails"=> $memberPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getAllMembers(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
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

  public function getMemberDetails(Request $request, Response $response, $args) {
    $endpoint = $this->getPath($request);
    $mrn = $args['mrn'];

    try {
      $memberDetails = Member::select('members.mrn', 'members.school_alias', 'members.skills', 'members.issued_cert', 'members.is_genuine', 'profiles.surname', 'profiles.firstname', 'profiles.othername', 'profiles.gender_id', 'profiles.phone', 'profiles.date_of_birth', 'profiles.photo', 'profiles.twitter', 'profiles.facebook', 'profiles.linkedin', 'profiles.website', 'users.email')
        ->where('members.mrn', $mrn)
        ->leftJoin('profiles', function($join) {
          $join->on('members.mrn', '=', 'profiles.mrn');
        })
        ->leftJoin('users', function($join) {
          $join->on('members.mrn', '=', 'users.mrn');
        })
        ->first();

      $memberPayload = Member::getFullPayload($memberDetails);

      return $response->withJson(["memberDetails"=> $memberPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function updateMemberDetails(Request $request, Response $response, $args) {
    $endpoint = $this->getPath($request);
    $mrn = $args['mrn'];
    $params = $request->getParsedBody();
    $this->requiredParams = [
      'surname',
      'firstname',
      'email',
      'phone',
      'gender_id',
      'date_of_birth'
    ];

    if (is_null($mrn) || empty($mrn)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    $userExists = Member::where('mrn', $mrn)->exists();
    if (!$userExists) {
      $customErrorPayload = $this->getCustomErrorPayload($endpoint, 'Member does not exist.', 422, 'Member with MRN does not exist!');
			return $response->withJson($customErrorPayload, $customErrorPayload['code']);
    }

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    $signUpRules = $this->getRulesForSignUp();

		$validator = $this->getValidator($request, $signUpRules);
		if (!$validator->isValid()) {
			$customErrorPayload = $this->getCustomErrorPayload($endpoint, 'Invalid parameter(s).', 422, 'Some parameters provided are invalid.');
			return $response->withJson($customErrorPayload, $customErrorPayload['code']);
    }
    
    try {
      $memberProfile = Profile::find($mrn);
      $memberProfile->surname = ($params['surname']) ? $params['surname'] : $memberProfile->surname;
      $memberProfile->firstname = ($params['firstname']) ? $params['firstname'] : $memberProfile->firstname;
      $memberProfile->othername = ($params['othername']) ? $params['othername'] : $memberProfile->othername;
      $memberProfile->gender_id = ($params['gender_id']) ? $params['gender_id'] : $memberProfile->gender_id;
      $memberProfile->phone = ($params['phone']) ? $params['phone'] : $memberProfile->phone;
      $memberProfile->date_of_birth = ($params['date_of_birth']) ? $params['date_of_birth'] : $memberProfile->date_of_birth;
      $memberProfile->update();

      $memberPayload = $memberProfile->fresh()->getPayload();

      return $response->withJson(['status'=> true, 'message'=> 'Update was successful', "memberDetails"=> $memberPayload], 200);
    } catch(QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  private function getRulesForSignUp() {
		return [
			'school_alias' => Rule::stringType()->length(1, null),
			'surname' => Rule::stringType()->length(1, null),
			'firstname' => Rule::stringType()->length(1, null),
			'email' => Rule::email(),
			'password' => Rule::stringType()->length(6, null),
      'phone' => Rule::stringType()->length(11, 11),
      'gender_id' => Rule::stringType()->lenght(1, 1)
		];
	}

}