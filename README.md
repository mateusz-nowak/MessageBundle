# MatixMessageBundle
Application that send SMS and VMS messages using SMSApi.

# How to

> cd /your/symfony-project-application/src/

> git clone git://github.com/matixe/MessageBundle.git Matix/MessageBundle

in *app/AppKernel.php* add: 
> new Matix\MessageBundle\MatixMessageBundle()

in *app/config/config.yml* update your *imports* section:
> - { resource: @MatixMessageBundle/Resources/config/services.xml }

In your *config/parameters.yml* add:
```PHP
mailer_password: null
locale: en
secret: 46e8b30a4ec6186a415ccea2adcbc10ada72a855
database_path: null

sms:
  username: matix
  password: testowe
```

# Example of use
If you want to send SMS message simply use in your controller:

> $messageBundleService = $this->get('sms.messager.service');

> // sms sending

> $messageBundleService->sendSMS('660000000', 'Your message');

> // vms sending

> $messageBundleService->sendVMS('660000000', 'Your message');

If you want to spool all the messages in a queue in your console:
> ./app/console message:send