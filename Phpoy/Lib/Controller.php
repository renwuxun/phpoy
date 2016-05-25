<?php
/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/23 0023
 * Time: 17:57
 */

namespace Phpoy\Lib;


abstract class Controller {

    /**
     * @var array
     */
    private $interceptors;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var View
     */
    private $view;

    /**
     * @return array
     */
    protected static function selfInterceptors() {
        return [];
    }

    protected static function viewPath() {
        return PROJECT_PATH . '/View';
    }

    protected function getInterceptors() {
        if (null === $this->interceptors) {
            $this->interceptors = array();

            /**
             * @var $className $this
             */
            $className = get_class($this);
            while ($className) {
                $selfInterceptors = $className::selfInterceptors(); // need php>=5.3.0
                if (!empty($selfInterceptors)) {
                    $this->interceptors = array_merge($selfInterceptors, $this->interceptors);
                }
                $className = get_parent_class($className);
            }
        }

        return $this->interceptors;
    }

    /**
     * @param string $sAction
     * @param array $args
     * @return string
     */
    public function run($sAction, $args = []) {
        $interceptors = array();
        $skipLogic = false;
        foreach ($this->getInterceptors() as $sInterceptor => $applyActs) {
            if (empty($applyActs) || in_array($sAction, $applyActs)) {
                /** @var $interceptor Interceptor */
                $interceptor = new $sInterceptor;
                $interceptors[] = $interceptor;
                if (false === $interceptor->before($sAction)) {
                    $skipLogic = true;
                }
            }
        }
        $content = '';
        if (!$skipLogic) {
            $content = call_user_func_array(array($this, $sAction.'Action'), $args);
            $layout = $this->getLayout();
            if (null !== $layout) {
                $layout->assign('content', $content);
                $content = $layout->run('index');
            }
        }
        foreach ($interceptors as $interceptor) {
            $interceptor->after();
        }
        return $content;
    }

    abstract public function indexAction();

    /**
     * @return Layout
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * 请注意避免形成layout死循环
     * @param Layout $layout
     * @throws Exception
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * @return View
     */
    public function getView() {
        if (null === $this->view) {
            $this->view = new View;
            $this->view->init(self::viewPath());
        }
        return $this->view;
    }

    public function render($sView, $status = 200, $headers = []) {
        $response = App::app()->getResponse();
        $response->setHeader('Content-Type', 'text/html;charset=utf-8');
        foreach ($headers as $k => $v) {
            $response->setHeader($k, $v);
        }
        $response->$response->setStatus($status);
        return $this->getView()->render($sView);
    }

    public function renderJson($data = null, $status = 200, $headers = []) {
        $response = App::app()->getResponse();
        $response->setHeader('Content-Type', 'application/javascript;charset=utf8');
        foreach ($headers as $k => $v) {
            $response->setHeader($k, $v);
        }
        $response->setStatus($status);
        if (null === $data) {
            $data = new \ArrayObject;
        }
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return string
     */
    public function renderJsonCb($data = null, $status = 200, $headers = []) {
        $jsoncb = $this->getRequest()->get('jsoncb', '');
        $ret = $this->renderJson($data, $status, $headers);
        return $jsoncb ? $jsoncb.'('.$ret.');' : $ret;
    }

    public function assign($key, $val) {
        $this->getView()->assign($key, $val);
    }


    /**
     * @return Response
     */
    public function getResponse() {
        return App::app()->getResponse();
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return App::app()->getRequest();
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return string
     */
    protected function redirect($url, $status = 302, $headers = []) {
        $content =
'<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>';

        $content = sprintf($content, htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

        $headers = array_merge($headers, ['Location'=>$url, 'Content-Length'=>strlen($content)]);

        foreach ($headers as $k=>$v) {
            $this->getResponse()->setHeader($k, $v);
        }

        $this->getResponse()->setStatus($status);

        return $content;
    }
}