<?php
namespace NACOSS\Controllers\Messaging;

use NACOSS\Controllers\Controller;
use NACOSS\Models\Profile;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class USSDController extends Controller {
  protected $requiredParams = [];

  public function __construct(Container $container) {
    parent::__construct($container);
  }
  
  public function executeUSSDOperation(Request $request, Response $response) {
    // Reads the variables sent via POST from our gateway
    $sessionId   = $request->getParam("sessionId");
    $serviceCode = $request->getParam("serviceCode");
    $phoneNumber = $request->getParam("phoneNumber");
    $text        = $request->getParam("text");

    switch ($text) {
      case '1':
        // Business logic for first level response
        $responsePayload = "CON Choose registration type you want to do \n";
        $responsePayload .= "1. Member Registration \n";
        $responsePayload .= "2. Chapter Registration";
        break;

      case '1*1':
        // This is a second level response where the user selected 1 in the first instance
        $responsePayload = "END This feature is still being tested.";
        break;

      case '1*2':
        // This is a second level response where the user selected 1 in the first instance
        // This is a terminal request. Note how we start the response with END
        $responsePayload = "END This feature is still being tested.";
        break;

      case '2':
        $memberProfile = $this->getMemberMRN($phoneNumber);

        if (is_null($memberProfile)) {
          $responsePayload = "END We could not find any account with phone number: $phoneNumber";
        } else {
          $responsePayload = "END ".$memberProfile['firstname'].", your NACOSS ID (MRN) is: ".$memberProfile['mrn'];
        }

        break;
      
      default:
        // This is the first request. Note how we start the response with CON
        $responsePayload  = "CON Great NACOSSite! What would you want to do? \n";
        $responsePayload .= "1. Registration \n";
        $responsePayload .= "2. My NACOSS MRN \n";
        $responsePayload .= "3. Give Feedback";
        break;
    }

    // Echo the response back to the API
    return $response->withHeader('Content-Type', 'text/plain')->write($responsePayload);
  }

  private function getMemberMRN($phone) {
    try {
      $memberProfile = Profile::where('phone', $phone)->first();
      
      if (is_null($memberProfile)) {
        return null;
      }

      return $memberProfile->getPayload();
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return null;
    }
  }
}