<?php

namespace Wusuopu\RemoteDumpBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Wusuopu\RemoteDumpBundle\Util\DumpUtil;

/**
 * var_dump variable service.
 */
class DumpService
{
    
    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param object $data
     */
    public function dump($data)
    {
        $container = $this->kernel->getContainer();
        if (!$container->hasParameter("wusuopu_remote_dump_env") || !$container->hasParameter("wusuopu_remote_dump_url")) {
            return;
        }

        $environment = $container->getParameter("wusuopu_remote_dump_env");
        $url = $container->getParameter("wusuopu_remote_dump_url");

        if (!$environment) {
            return;
        }

        $currentEnv = $container->getParameter("kernel.environment");

        if (is_array($environment) && !in_array($currentEnv, $environment)) {
            return;
        } else if ($currentEnv === $environment) {
            return;
        }


        try {
            $request = $container->get('request');
        } catch (\Exception $e) {
            $request = null;
        }

        ob_start();

        if ($request) {
            echo $request->getClientIp() . " ". $request->getRealMethod() . " " . $request->getHttpHost() . " " . $request->getRequestUri() . ($request->isXmlHttpRequest() ? "   (Ajax) </ br>" : " </ br>");
        }

        var_dump($data);

        $str = ob_get_contents();
        ob_end_clean();

        DumpUtil::postData($str, $url);
    }
}
