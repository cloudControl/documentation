# Phrase #

phrase allows you to edit your translations directly on your site without having to manually edit localization files. This makes your whole translation process easy as pie. Use the in-place editor to translate content directly on your site or manage your translations with the powerful phrase Translation Center.


## Provisioning the add-on

phrase can be attached to CloudControl via the command line client:

    $ cctrlapp APP_NAME/DEP_NAME addon.add phrase.PLAN


Once the add-on has been provisioned the `PHRASE_AUTH_TOKEN` will be available within your list of credentials. It contains your authentication token that lets you connect to the phrase service:

    $ cctrlapp APP_NAME/DEP_NAME addon | grep PHRASE_AUTH_TOKEN
    PHRASE_AUTH_TOKEN: d219d3abcgcxBahs72K1


## Installation and Usage

phrase should be integrated in an own staging environment. If you haven't done so already, we recommend you set up a staging environment as explained in the [CloudControl Documentation](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#development-staging-and-production-environments). The staging environment will be the place for your translators to edit your website content.
Of course, you can use your local development environment for integration as well. If you decide to do so, simply replace the staging with your development environment in the following steps.

If you have any questions, contact us at [info@phraseapp.com](info@phraseapp.com) and we will be happy to guide you through the installation process.


### phrase command line tool ###

Developers typically access phrase with the [phrase ruby gem](https://rubygems.org/gems/phrase) via command line:

    gem install phrase

You are now able to use phrase to upload translations to the API and export new translation files form phrase as described in the next sections.


### Using with Symfony2

Simply follow the instructions from our documentation: [phraseapp.com/docs/installation/symfony2](https://phraseapp.com/docs/installation/symfony2)


### Using with Ruby / Rails / Sinatra

*The following integration guide assumes your application uses bundler for gem dependency management.*


#### Add the gem

Add the phrase gem to your staging environment:
    
    group :development, :staging do
      gem 'phrase'
    end

and install the gem with the bundle command:

    $ bundle install
    

#### Initialize phrase

First you need to enable phrase within your application and provide the authentication token. You can do this by adding the following two lines to an initializer (Rails) or in your main application file:

    Phrase.enabled = true
    Phrase.auth_token = ENV['PHRASE_AUTH_TOKEN']
  
If you are using Sinatra, you might have to require phrase in your app file:

    if ENV['RACK_ENV'] == 'staging'
      require "phrase"
    end

This will load phrase when you start your application in staging environment.

Now initialize phrase with the auth token you received after adding the phrase add-on to your application:

    $ bundle exec phrase init --secret=YOUR_AUTH_TOKEN --default-locale=en

This will generate a .phrase config file in your project root that includes your secret key. We recommend adding it to your .gitignore file.

Next, upload the existing locale files from your app:

    $ bundle exec phrase push ./config/locales/
    
phrase now knows about your locales and the keys in your application. To enable the phrase in-place editor, simply add the javascript to your application layout file:

    <script>
    var phrase_auth_token = '<%= ENV['PHRASE_AUTH_TOKEN'] %>';
    (function() {
      var phraseapp = document.createElement('script'); phraseapp.type = 'text/javascript'; phraseapp.async = true;
      phraseapp.src = ['https://', 'phraseapp.com/assets/phrase/0.1/app.js?', new Date().getTime()].join('');
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(phraseapp, s);
    })();
    </script>
    
In order to tell the i18n gem about the new locales that should be used in production you have to configure the load path as well. When using Rails you can set the load path in your production.rb:

    config.i18n.load_path = Dir[Rails.root.join('phrase', 'locales', '*.yml').to_s]
    
If you're using Sinatra or something similar, you probably want to set it in the config.ru file:

    if ENV['RACK_ENV'] == 'production'
      I18n.load_path = Dir[File.join(File.dirname(__FILE__), 'phrase', 'locales', '*.yml').to_s]
    else
      I18n.load_path = Dir[File.join(File.dirname(__FILE__), 'config', 'locales', '*.yml').to_s]
    end
    
    
#### Deploy application to staging environment

To finally get a look at phrase you simply have to deploy the application to your staging system and open it in the browser.

You now should see your application with the phrase in-place editor on top of it. To create your first user with translation privileges, you have to log into phrase by following the "Login" link in your add-on management panel.

After logging in, you can access the user management under the "Account" menu. Simply create a user with an email address and password of your choice. You can now edit your text content right on the site!


#### Deploy translations

After you have finished translating your site, you will need to push the new translation files to production. In order to do so, you will first have to download them from phrase and add them to your project:

    $ bundle exec phrase pull
    $ git add ./phrase/locales
    $ git commit -m "added new translations"
    
Now you can push the changes to your production repository:

    $ cctrlapp APP_NAME/DEP_NAME push
    $ cctrlapp APP_NAME/DEP_NAME deploy
    
The production system will now use the new locale files including your latest translations!


### Using with other platforms

If you need support for a platform or language that is not yet supported just contact us at [info@phraseapp.com](info@phraseapp.com)


## Workflow

With phrase your translation workflow will get much easier:

1.  Add a new feature to your app that requires translation
2.  Add the new keys to phrase by uploading the locale files to phrase or creating them manually within the translation center
3.  Deploy your new code to your staging environment and let your translators do their work
4.  Download the new locale files from phrase and deploy them to production


## Translation Center

Translation Center lets you manage:

* Locales
* Keys
* Translations
* Users 
* etc.

To log in to Translation Center, view your add-on settings within your CloudControl console and follow the "Login" link next to the phrase addon. This will perform the sign in to phrase where you can manage your account, users, locales and more.

*For more information on the features available within the phrase translation center please see the feature tour at [phraseapp.com/features](https://phraseapp.com/features).*


## Removing the add-on

phrase can be removed via the command line client:

    $ cctrlapp APP_NAME/DEP_NAME addon.remove phrase.PLAN

**Warning:** This will destroy all data associated with your phrase account and cannot be undone!


## Other platforms and languages

phrase supports all common localization formats, including YAML, JSON, Gettext (.po), Properties, XLIFF, Android, iOS, ResX etc. 

You can always access your localization files and translations from within Translation Center.

If you need support for a platform or language that is not yet supported just contact us at [info@phraseapp.com](info@phraseapp.com)


## Support

If you have any questions regarding phrase, feel free to contact us at [phraseapp.com/support](https://phraseapp.com/support).


## Additional resources

Additional resources are available at:

* [Example application using Sinatra](https://github.com/phrase/phrase_example_cloudcontrol_app)
* [Support](https://phraseapp.com/support)
* [Full documentation](https://phraseapp.com/docs)
