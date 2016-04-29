<?php
namespace Craft;

class MailgunFormPlugin extends BasePlugin
{
	function getName()
	{
		return Craft::t('MailgunForm');
	}

	function getVersion()
	{
		return '0.1';
	}

	function getDeveloper()
	{
		return 'Eric McNiece';
	}

	function getDeveloperUrl()
	{
		return 'http://emc2innovation.com';
	}

	protected function defineSettings()
	{
		return array(
			'emailRecipient' => array(AttributeType::Email, 'required' => true),
			'apiKey' => array(AttributeType::String, 'required' => true),
			'domainName' => array(AttributeType::String, 'require' => true),
			'emailSubject' => array(AttributeType::String),
		);
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('mailgunform/_settings', array(
			'settings' => $this->getSettings()
		));
	}
}
