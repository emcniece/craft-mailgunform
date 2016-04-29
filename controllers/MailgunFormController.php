<?php
namespace Craft;

class MailgunFormController extends BaseController
{
	protected $allowAnonymous = true;

	public function actionSendMessage()
	{
		$this->requirePostRequest();

		$plugin = craft()->plugins->getPlugin('mailgunform');

		// We need the plugin before we can do anything
		if (!$plugin)
		{
			throw new Exception('Couldn’t find the MailgunForm plugin!');
		}

		// Check the plugin's settings to see if we have everything
		$settings = $plugin->getSettings();

		if (($toEmail = $settings->emailRecipient) == null)
		{
			craft()->userSession->setError('The "To Email" address is not set on the plugin’s settings page.');
			Craft::log('Tried to send a contact form request, but missing the "To Email" address on the plugin’s settings page.', LogLevel::Error);
			$this->redirectToPostedUrl();
		}
		elseif (($apiKey = $settings->apiKey) == null)
		{
			craft()->userSession->setError('The "API key" is not set on the plugin’s settings page.');
			Craft::log('Tried to send a contact form request, but missing the "API key" on the plugin’s settings page.', LogLevel::Error);
			$this->redirectToPostedUrl();
		}
		elseif (($domainName = $settings->domainName) == null)
		{
			craft()->userSession->setError('The "Domain Name" is not set on the plugin’s settings page.');
			Craft::log('Tried to send a contact form request, but missing the "Domain Name" on the plugin’s settings page.', LogLevel::Error);
      $this->redirectToPostedUrl();
		}
		else
		{
			// Include Mailgun API SDK
			require_once(CRAFT_PLUGINS_PATH.'mailgunform/vendor/autoload.php');
			$client = new \Http\Adapter\Guzzle6\Client();
			$mg = new \Mailgun\Mailgun($settings->apiKey, $client);

			// construct model
			$formData = new MailgunFormModel();
			$formData->fromEmail 	= craft()->request->getPost('fromEmail');
			$formData->fromName 	= craft()->request->getPost('fromName', '');
			$formData->subject 		= $settings->emailSubject;
			$formData->message 		= craft()->request->getPost('message');

			// Mailgun status responses
			$result = false;
			$messageStatus = 'unknown';
			$rejectReason = null;
			$response = 0;

			if ($formData->validate()){
				// Configure Mailgun Message Object
				$from = $formData->fromName ? $formData->fromName.' <'.$formData->fromEmail.'>' : $formData->fromEmail;
				$message = array(
					'text'				=> $formData->message,
					'subject'			=> $formData->subject,
					'from'				=> $from,
					'to' 					=> $settings->emailRecipient
				);

				try{
					// Mailgun API Message Send
					$result = $mg->sendMessage($settings->domainName, $message);
					$messageStatus = $result->http_response_body->message;
					$response = $result->http_response_code;
				} catch(\Exception $e){
					$messageStatus = 'rejected';
					$rejectReason = $e->getMessage();
				}

				craft()->userSession->setNotice('Your message has been ' . $messageStatus);

				if( !$result || ($response !== 200)){

					// Error sending
					Craft::log('A Mailgun error occured: '. $response.'. Status: ' . $messageStatus.'. Reason: '.$rejectReason, LogLevel::Error);
					craft()->userSession->setError(Craft::t('Couldn’t send email. Check your settings.'));
					$this->redirectToPostedUrl();
				} else{

					// Success!
					if (($successRedirectUrl = craft()->request->getPost('successRedirectUrl', null)) != null){
						$this->redirect($successRedirectUrl);
					} else {
						$this->redirectToPostedUrl();
					}
				}

			}

			craft()->urlManager->setRouteVariables(array(
				'message'				=> $formData,
				'status'				=> $messageStatus,
				'rejectReason'	=> $rejectReason
			));
		}
	}

}
