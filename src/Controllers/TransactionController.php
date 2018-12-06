<?php
namespace NACOSS\Controllers;

use NACOSS\Controllers\Controller;
use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Chapter;
use NACOSS\Models\ChapterDue;
use NACOSS\Models\ChapterRegistration;
use NACOSS\Models\Member;
use NACOSS\Models\Transaction;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class TransactionController extends Controller {
  protected $requiredParams = [
    'email',
    'phone',
    'transaction_ref',
    'amount',
    'response_code',
    'response_message'
  ];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function addTransaction(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    try {
      $transaction = new Transaction();
      $transaction->email = strtolower($params['email']);
      $transaction->phone = $params['phone'];
      $transaction->transaction_ref = $params['transaction_ref'];
      $transaction->amount = $params['amount'];
      $transaction->response_code = $params['response_code'];
      $transaction->response_message = $params['response_message'];
      $transaction->save();

      $transactionPayload = $transaction->fresh()->getPayload();

      return $response->withJson(['status'=> true, 'message'=> 'Transaction logged successfully!', "treaction"=> $transactionPayload], 200);
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}