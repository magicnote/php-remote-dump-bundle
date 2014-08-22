<?php
namespace Wusuopu\RemoteDumpBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Wusuopu\RemoteDumpBundle\Common\EnvTrait;

/**
 * A remote debug listener for kernel event.
 */
class DebugListener
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
        $this->checkIgnore();
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->checkListener("request")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $request = $event->getRequest();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName(),
            "POST:", $request->request,
            "GET:", $request->query,
            "FILES:", $request->files,
            "Cookies:", $request->cookies,
            "Headers:", $request->headers
        );
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->checkListener("controller")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName(),
            "Controller:", $event->getController()
        );
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$this->checkListener("view")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName(),
            "ControllerResult:", $event->getControllerResult()
        );
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelRespone(FilterResponseEvent $event)
    {
        if (!$this->checkListener("response")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $response = $event->getResponse();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName(),
            "StatusCode", $response->getStatusCode(),
            "Charset", $response->getCharset(),
            "Headers", $response->headers
        );
    }

    /**
     * @param FinishRequestEvent $event
     */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        if (!$this->checkListener("finish_request")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName(),
            "RequestType:", $event->getRequestType(),
            "isMasterRequest:", $event->isMasterRequest()
        );
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (!$this->checkListener("terminate")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName()
        );
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->checkListener("exception")) {
            return;
        }
        $container = $this->kernel->getContainer();
        $container->get('wusuopu.remote_dump')->dump(
            $event->getName(),
            "Exception:", $event->getException()
        );
    }

    private function checkIgnore()
    {
        if (!$this->isEnv) {
            return;
        }

        $container = $this->kernel->getContainer();
        // check if this listener is enabled.
        $isEnable = ($container->hasParameter('wusuopu_remote_dump_listener_enable')
            && $container->getParameter('wusuopu_remote_dump_listener_enable'));

        if (!$isEnable) {
            $this->isEnv = false;

            return;
        }

        if (!$container->hasParameter('wusuopu_remote_dump_listener_ignore_pattern')) {
            return;
        }

        $pattern = $container->getParameter('wusuopu_remote_dump_listener_ignore_pattern');
        try {
            $request = $container->get('request');
        } catch (\Exception $e) {
            $this->isEnv = false;

            return;
        }
        $url = $request->getRequestUri();
        try {
            if (preg_match($pattern, $url)) {
                $this->isEnv = false;

                return;
            }
        } catch (\Exception $e) {
        }
    }

    private function checkListener($listener)
    {
        if (!$this->isEnv) {
            return false;
        }

        $container = $this->kernel->getContainer();
        $parameter = 'wusuopu_remote_dump_listener.'. $listener;

        return ($container->hasParameter($parameter) && $container->getParameter($parameter));
    }
}
