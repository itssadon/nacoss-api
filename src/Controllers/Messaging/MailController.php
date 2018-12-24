<?php
namespace NACOSS\Controllers\Messaging;

use NACOSS\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Slim\App;

date_default_timezone_set('Etc/UTC');

/**
 * Use PHPMailer as a base class and extend it
 */
class MailController extends PHPMailer
{
  private $requiredConfig = ['SMTPDebug', 'isSMTP', 'host', 'SMTPAuth', 'username', 'password', 'SMTPSecure', 'port'];
  private $missingRequiredConfig = [];

  /**
   * MailController constructor.
   *
   * @param bool|null $exceptions
   * @param string    $body A default HTML message body
   */
  public function __construct($exceptions, $body = '')
  {
    parent::__construct($exceptions);

    chdir(dirname(__DIR__));
    $settings = require '../../settings.php';
    $app = new App($settings);
    $container = $app->getContainer();
    $config = $container->settings['PHPMailer'];

    if (empty($config)) {
      throw new Exception('No configuration has been set for PHPMailer');
    } else {
      if ($this->hasMissingRequiredConfig($config)) {
        $missingRequiredConfigStr = implode(', ', $this->missingRequiredConfig);
        throw new Exception("Required config not set: " . $missingRequiredConfigStr);               
      }

      //dd($config['port']);

      $this->SMTPDebug = $config['SMTPDebug'];
      //$this->isSMTP();
      $this->host = $config['host'];
      $this->SMTPAuth = $config['SMTPAuth'];
      $this->SMTPSecure = $config['SMTPSecure'];
      $this->username = $config['username'];
      $this->password = $config['password'];
      $this->port = $config['port'];
      $this->setFrom('no-reply@playnetworkafica.com', 'PLAY Network Africa');
      $this->msgHTML($body, __DIR__);
      // Inject a new debug output handler
      $this->Debugoutput = function ($str, $level) {
        // echo "Debug level $level; message: $str\n";
      };
    }
      
  }

  private function hasMissingRequiredConfig(array $config)
  {
    $value = 0;
    $requiredConfig = $this->requiredConfig;

    foreach ($requiredConfig as $required) {
      if (!array_key_exists($required, $config)) {
        array_push($this->missingRequiredConfig, $required);
        $value++;
      }
    }

    return $value > 0;
  }

  // Extend the send function
  public function send()
  {
    $this->Subject = $this->Subject;
    $r = parent::send();
    return $r;
  }
}