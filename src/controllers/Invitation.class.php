<?php
	/**
	 * @author Alexander A. Klestov <alan@klestoff.ru>
	 * @copyright Copyright (c) 2011, Alexander A. Klestov
	 */
	final class Invitation extends MethodMappedController
	{
		/**
		 * @var Guest
		 */
		private $guest = null;

		public function __construct() 
		{
			$this->
				setMethodMapping('show', 'showInvitation')->
				setMethodMapping('accept', 'acceptInvitation')->
				setMethodMapping('decline', 'declineInvitation')->
				setMethodMapping('comment', 'commentInvitation')->
				setMethodMapping('optional', 'optionalInvitation')->
				setDefaultAction('show');
		}

		public function handleRequest(HttpRequest $request)
		{
			$this->guest =
				(
					$request->hasAttachedVar('key')
					&& $request->getAttachedVar('key')
				)
					? Guest::dao()->getByKey($request->getAttachedVar('key'))
					: null;

			$mav = parent::handleRequest($request);

			$mav->getModel()->set(
				'guest', 
				$this->guest ?: Guest::no()
			);

			return $mav;
		}

		public function showInvitation(HttpRequest $request)
		{
			return ModelAndView::create();
		}

		/**
		 * @return ModelAndView
		 */
		public function acceptInvitation(HttpRequest $request)
		{
			$mav = $this->createJsonMav();
			
			if (
				$this->guest
				&& $this->guest->isSecretKey()
			) {
				Guest::dao()->
					save(
						$this->guest->setResult(
							Result::agree()
						)
					);
				
				$mav->getModel()->set('result', array('success' => true));
			}
			
			return $mav;
		}
		
		/**
		 * @return ModelAndView
		 */
		public function declineInvitation(HttpRequest $request)
		{
			$mav = $this->createJsonMav();
			
			if (
				$this->guest
				&& $this->guest->isSecretKey()
			) {
				Guest::dao()->
					save(
						$this->guest->setResult(
							Result::disagree()
						)
					);
				
				$mav->getModel()->set('result', array('success' => true));
			}
			
			return $mav;
		}
		
		/**
		 * @return ModelAndView
		 */
		public function commentInvitation(HttpRequest $request)
		{
			$mav = $this->createJsonMav();
			
			if (
				$this->guest
				&& $this->guest->isSecretKey()
			) {
				$form = 
					Form::create()->
						add(
							Primitive::string('text')->
								setMin(1)->
								required()
						)->
						import($request->getPost());
						
				if (!$form->getErrors()) {
					Guest::dao()->save(
						$this->guest->setComment(
							$this->guest->getComment()
							.'==='.PHP_EOL
							.$form->getValue('text')
						)
					);
					
					$mav->getModel()->set('result', array('success' => true));
				}
			}
			
			return $mav;
		}
		
		/**
		 * @return ModelAndView
		 */
		public function optionalInvitation(HttpRequest $request)
		{
			$mav = $this->createJsonMav();
			
			if (
				$this->guest
				&& $this->guest->isSecretKey()
			) {
				$form = 
					Form::create()->
						add(
							Primitive::ternary('value')->
								required()
						)->
						import($request->getPost());

				if (!$form->getErrors()) {
					Guest::dao()->save(
						$this->guest->setOptionalResult(
							($form->getValue('value'))
								? Result::agree()
								: Result::disagree()
						)
					);
					
					$mav->getModel()->set('result', array('success' => true));
				}
			}
			
			return $mav;
		}
		
		/**
		 * @return ModelAndView
		 */
		private function createJsonMav()
		{
			return
				ModelAndView::create()->
					setView('json');
		}
	}
?>
