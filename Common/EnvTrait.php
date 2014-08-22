<?php

namespace Wusuopu\RemoteDumpBundle\Common;

trait EnvTrait
{
    /**
     * Check if run services in current environment.
     *
     * @return boolean
     */
    private function checkEnv()
    {
        $container = $this->kernel->getContainer();
        if (!$container->hasParameter("wusuopu_remote_dump_env") || !$container->hasParameter("wusuopu_remote_dump_url")) {
            return false;
        }

        $environment = $container->getParameter("wusuopu_remote_dump_env");
        $url = $container->getParameter("wusuopu_remote_dump_url");

        if (!$environment) {
            return false;
        }

        $currentEnv = $container->getParameter("kernel.environment");

        if (is_array($environment) && !in_array($currentEnv, $environment)) {
            return false;
        } else if ($currentEnv === $environment) {
            return false;
        }

        return true;
    }
}
