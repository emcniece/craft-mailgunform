# MailgunForm for Craft CMS

MailgunForm for Craft is a port of https://github.com/jmisavage/craft-mandrillform which is an adaptation of Pixel &amp; Tonic's ContactForm plugin. It uses Mailgun to send emails instead of PHP's mailer functions.

## How to install and use:

1. Place the mailgunform folder in your craft/plugins folder.
1. Go to Settings > Plugins from your Craft control panel and enable the MailgunForm plugin.
1. Click on "MailgunForm" to go to the plugin's settings page.
1. Enter the email address you would like the contact requests to be sent to.
1. Enter your Mailgun API key and domain name.
1. Enter the subject to appear in the emails you receive.

## Sample Form

```html
{% macro errorList(errors) %}
  {% if errors %}
      <ul class="errors">
          {% for error in errors %}
              <li>{{ error }}</li>
          {% endfor %}
      </ul>
  {% endif %}
{% endmacro %}

{% from _self import errorList %}

<form method="post" action="" accept-charset="UTF-8">
    <input type="hidden" name="action" value="mailgunForm/sendMessage">
    <input type="hidden" name="successRedirectUrl" value="success">

    <input id="fromEmail" type="text" name="fromEmail" value="{% if message is defined %}{{ message.fromEmail }}{% endif %}" placeholder="Enter Email">

    {{ message is defined and message ? errorList(message.getErrors('fromEmail')) }}

    <input id="fromName" type="text" name="fromName" value="{% if message is defined %}{{ message.fromName }}{% endif %}" placeholder="Enter Name">

    {{ message is defined and message ? errorList(message.getErrors('fromName')) }}

    <input id="subject" type="text" name="subject" value="{% if message is defined %}{{ message.subject }}{% endif %}" placeholder="Enter Subject">

    {{ message is defined and message ? errorList(message.getErrors('subject')) }}

    <textarea rows="10" cols="40" id="message" name="message" placeholder="Enter Message">{% if message is defined %}{{ message.message }}{% endif %}</textarea>

    {{ message is defined and message ? errorList(message.getErrors('message')) }}

    <button class="submit" type="submit" value="Submit">Submit</button>
</form>
```

## Roadmap

This plugin was an incredibly fast port to move clients away from Mandrill since they [closed their free tier](http://blog.mailchimp.com/important-changes-to-mandrill/). I don't personally plan on developing this further unless urgent need arises, but I'll do my best to find time to respond to issues or feature requests.

## Special Notes

This plugin currently includes v1.7 of the [official Mailgun API Client for PHP](https://github.com/mailgun/mailgun-php).
