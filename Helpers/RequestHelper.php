<?php
namespace TelNowEdge\FreePBX\Base\Helpers;

class RequestHelper extends AbstractHelper
{
    private $request;

    public function __construct()
    {
        parent::__construct();
    }

    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }

    public function get($name, $default = null)
    {
        if (false === isset($this->request[$name])
        || true === empty($this->request[$name])
        ) {
            return $default;
        }

        return $this->request[$name];
    }
}
