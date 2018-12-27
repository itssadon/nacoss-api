<?php
namespace NACOSS\Controllers;

use NACOSS\Controllers\Controller;
use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Chapter;
use NACOSS\Models\ChapterDue;
use NACOSS\Models\ChapterRegistration;
use NACOSS\Models\Member;
use NACOSS\Models\Transaction;
use NACOSS\Models\TransactionPurpose;
use Illuminate\Database\QueryException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class TransactionController extends Controller {
  protected $requiredParams = [
    'email',
    'transaction_ref',
    'response_code',
    'response_message',
    'purpose_id'
  ];

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function logTransaction(Request $request, Response $response) {
    $endpoint = $this->getPath($request);
    $params = $request->getParsedBody();

    if ($this->hasMissingRequiredParams($params)) {
      $parametersErrorPayload = $this->getParametersErrorPayload($endpoint);
      return $response->withJson($parametersErrorPayload, 401);
    }

    try {
      $transaction = Transaction::firstOrNew(['transaction_ref'=> $params['transaction_ref']]);
      $transaction->email = strtolower($params['email']);
      $transaction->phone = ($params['phone']) ? $params['phone'] : '';
      $transaction->response_code = $params['response_code'];
      $transaction->response_message = $params['response_message'];
      $transaction->purpose_id = $params['purpose_id'];
      $txPurposeAmount = TransactionPurpose::select('amount')->where('purpose_id', $transaction->purpose_id)->first();
      $transaction->amount = $txPurposeAmount['amount'];
      $transaction->save();

      $transactionPayload = $transaction->fresh()->getPayload();

      return $response->withJson(['status'=> true, 'message'=> 'Transaction logged successfully!', "transaction"=> $transactionPayload])
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);

    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

  public function getAllTransactions(Request $request, Response $response) {
    $endpoint = $this->getPath($request);

    try {
      $transactions = Transaction::orderBy('created_at', DESC)->get();

      $trasactionPayload = [];
      foreach ($transactions as $chapter) {
        array_push($trasactionPayload, $chapter->getPayload());
      }

      return $response->withJson(["transactions"=> $trasactionPayload])
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
        
    } catch (QueryException $dbException) {
      $databaseErrorPayload = $this->getDatabaseErrorPayload($endpoint, $dbException);
      return $response->withJson($databaseErrorPayload, 500);
    }
  }

}