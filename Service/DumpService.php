<?php

namespace Wusuopu\RemoteDumpBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Wusuopu\RemoteDumpBundle\Util\DumpUtil;
use Wusuopu\RemoteDumpBundle\Common\EnvTrait;

/**
 * var_dump variable service.
 */
class DumpService
{
    use EnvTrait;
    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * @var boolean
     */
    protected $isEnv;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->isEnv = $this->checkEnv();
    }

    /**
     * dump data.
     * Variable-length argument lists are supported.
     */
    public function dump()
    {
        if (!$this->isEnv) {
            return;
        }

        $container = $this->kernel->getContainer();
        $url = $container->getParameter("wusuopu_remote_dump_url");
        $timeout = $container->getParameter("wusuopu_remote_dump_timeout");

        try {
            $request = $container->get('request');
        } catch (\Exception $e) {
            $request = null;
        }

        $numargs = func_num_args();
        if ($numargs == 0) {
            return;
        }

        $arglist = func_get_args();
        ob_start();

        if ($request) {
            echo $request->getClientIp() . " ". $request->getRealMethod() . " " . $request->getHttpHost() . " " . $request->getRequestUri() . ($request->isXmlHttpRequest() ? "   (Ajax) </ br>" : " </ br>");
        }

        for ($i = 0; $i < $numargs; $i++) {
            var_dump($arglist[$i]);
        }

        $str = ob_get_contents();
        ob_end_clean();

        DumpUtil::postData($str, $url, $timeout);
    }
}
