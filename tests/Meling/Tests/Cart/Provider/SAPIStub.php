<?php
namespace Meling\Tests\Cart\Provider;
/**
 * @coversDefaultClass PHPixie\HTTP\Context\Session\SAPI
 */
class SAPIStub extends \PHPixie\HTTP\Context\Session\SAPI
{
    protected $sessionArray;

    protected $sessionStarted = false;

    public function __construct(&$sessionArray)
    {
        $this->sessionArray = &$sessionArray;
    }

    public function isSessionStarted()
    {
        return $this->sessionStarted;
    }

    protected function &session()
    {
        return $this->sessionArray;
    }

    protected function sessionStart()
    {
        $this->sessionStarted = true;
    }

}
