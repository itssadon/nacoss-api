<?php
namespace NACOSS\Controllers;

use NACOSS\Helpers\ResponsePayload;
use NACOSS\Models\MessageTemplate;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller {
	protected $container;
	protected $requiredParams = [];

	public function __construct(Container $container) {
		$this->container = $container;
	}

	protected function hasMissingRequiredParams(array $parameters) {
		$requiredParams = $this->requiredParams;

		$value = 0;
		if (count($requiredParams) > 0) {
			foreach ($requiredParams as $requiredParam) {
				if (!array_key_exists($requiredParam, $parameters)) {
					$value++;
				}
			}
		}

		return $value > 0;
	}

	protected function getSettingsAttribute($attr) {
		return $this->container->settings[$attr];
	}

	protected function getDatabaseErrorPayload($link, $dbException) {
		$code = 500;
		$link = $link;
		$message = "An error occured";
		$developerMessage = $dbException->getMessage();

		return ResponsePayload::getPayload($code, $message, $link, $developerMessage);
	}

	protected function getParametersErrorPayload($endpoint) {
		$code = 401;
		$developerMessage = 'Some required parameters are missing';
		$message = 'Invalid parameters';
            
		return ResponsePayload::getPayload($code, $message, $endpoint, $developerMessage);
	}

	protected function getNoRecordPayload($endpoint) {
		$code = 200;
		$developerMessage = 'There are not record found in the database';
		$message = 'No record found';
            
		return ResponsePayload::getPayload($code, $message, $endpoint, $developerMessage);
	}

	protected function getValidator(Request $request, $rules) {
		return $this->container->validator->validate($request, $rules,
			[
				'length' => 'This field must have a length between {{minValue}} and {{maxValue}} characters',
				'positive' => 'This field must be positive',
			]
		);
	}

	protected function getPath(Request $request) {
		return str_replace('/v1', '', $request->getUri()->getPath());
	}

	protected function getMessageTemplate($id) {
		$messageTemplate = MessageTemplate::select("subject", "body")->where(["id" => $id])->first();

		return $messageTemplate;
	}

	protected function getTemplateNotFoundPayload($link) {
		$code = 500;
		$link = $link;
		$message = 'Template not found';
		$developerMessage = 'We could not find any template with the id provided';

		return ResponsePayload::getPayload($code, $message, $link, $developerMessage);
	}

	protected function getCopyrightYear() {
		return $this->container->copyrightYear;
	}

}
