<?php
	/**
	 * @author Alexander A. Klestov <alan@klestoff.ru>
	 * @copyright Copyright (c) 2010, Alexander A. Klestov
	 */
	class Index
	{
		/**
		 * @var HttpRequest 
		 */
		protected $request = null;

		protected $defaultController = 'main';
		protected $defaultView = 'main';

		private $controllerName = null;
		private $decorators = array();

		/**
		 * @return Index
		 */
		public static function create()
		{
			return new self();
		}

		/**
		 * @return Index
		 */
		public function run()
		{
			try {
				$this->
					init()->
					render(
						$this->
							decorate($this->getController())->
							handleRequest($this->request)
					);
			} catch (Exception $e) {
				LogManager::storeException($e);

				if (defined('__LOCAL_DEBUG__') && __LOCAL_DEBUG__)
					LogManager::deliverToOut();
				else {
					LogManager::deliverToLog();
					header('Location: /');
				}
			}

			return $this;
		}

		public function getDefaultController()
		{
			return $this->defaultController;
		}

		/**
		 * @return Index
		 */
		public function setDefaultController($defaultController)
		{
			$this->defaultController = $defaultController;

			return $this;
		}

		public function getDefaultView()
		{
			return $this->defaultView;
		}

		/**
		 * @return Index
		 */
		public function setDefaultView($defaultView)
		{
			$this->defaultView = $defaultView;

			return $this;
		}

		/**
		 * @return Index
		 */
		public function addDecorator($decoratorName)
		{
			$this->decorators[] = $decoratorName;

			return $this;
		}

		/**
		 * @return Index
		 */
		protected function init()
		{
			Session::start();

			$this->request =
				HttpRequest::create()->
					setCookie($_COOKIE)->
					setFiles($_FILES)->
					setGet($_GET)->
					setPost($_POST)->
					setServer($_SERVER)->
					setSession($_SESSION);

			RouterRewrite::me()->route($this->request);

			return $this;
		}

		/**
		 * @return Index
		 */
		protected function render(ModelAndView $mav)
		{
			$view = $this->makeSafeView($mav->getView());
			
			$model = $mav->getModel();

			if (is_string($view)) {
				$model->
					// FIXME XXX: self url must contain get-params
					set('selfUrl', RouterRewrite::me()->assembly())->
					set('baseUrl', PATH_WEB);

				$view = $this->getViewResolver()->resolveViewName($view);
			}

			ob_start();

			$view->render($model);

			echo ob_get_clean();

			return $this;
		}

		/**
		 * @return ViewResolver
		 */
		protected function getViewResolver()
		{
			return
				MultiPrefixPhpViewResolver::create()->
					addPrefix(PATH_TEMPLATES)->
					addPrefix(
						PATH_TEMPLATES
						.$this->getControllerName()
						.DIRECTORY_SEPARATOR
					);
		}

		private function decorate(Controller $controller)
		{
			foreach ($this->decorators as $decoratorName)
				$controller = new $decoratorName($controller);

			return $controller;
		}

		/**
		 * @return Controller
		 */
		private function getController()
		{
			$controllerName = $this->getControllerName();
			
			return new $controllerName;
		}

		private function getControllerName()
		{
			if (!$this->controllerName) {
				$area =
					$this->request->hasAttachedVar('area')
						? $this->request->getAttachedVar('area')
						: $this->getDefaultController();
				
				if (
					$area
					&& ClassUtils::isClassName($area)
					&& $this->isControllerExists($area)
				)
					$this->controllerName = $area;
			}

			if (!$this->controllerName)
				throw new BadRequestException('Appropriate controller not found');
			
			return $this->controllerName;
		}

		private function isControllerExists($controllerName)
		{
			return is_readable(PATH_CONTROLLERS.$controllerName.EXT_CLASS);
		}

		private function makeSafeView($view = null)
		{
			if (!$view)
				return $this->getDefaultView();

			return $view;
		}

	}
?>
