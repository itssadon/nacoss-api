<?php
namespace NACOSS\Controllers\Messaging;

class MessageController
{
    /**
     * @var string
     */
    private $body;
    
    /**
     * @var string[] varname => string value
     */
    private $vars;

    public function __construct($body, array $vars = array())
    {
        $this->body = (string)$body;
        $this->setVars($vars);
    }

    public function setVars(array $vars)
    {
        $this->vars = $vars;
    }

    public function getTemplateText()
    {
        return $this->body;
    }

    public function __toString()
    {
        return strtr($this->getTemplateText(), $this->getReplacementPairs());
    }

    private function getReplacementPairs()
    {
        $pairs = array();
        foreach ($this->vars as $name => $value)
        {
            $key = sprintf('[{%s}]', strtoupper($name));
            $pairs[$key] = (string)$value;
        }
        return $pairs;
    }
}
