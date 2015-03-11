# PhraseApp: Software translation management

Edit and share any software language file online. Work with your team translators or get professional translations from our translation partners. Use our In-Context Editor for in-place copywriting and translation changes with support for Ruby on Rails, Symfony, Django, Flask, Sinatra & AngularJS.

Save hours spent on file conversions and locale file management. Automate translation workflows.

## Features

### Collaboration with Translators
Invite your translators and developers to work together on the translation of your application.

### Professional Translations
Order professional translations for your application with a few clicks

### Translation Center
Manage all of your locales and translations in the powerful Translation Center.

### Automation and Continuous Integration
Automate your translation workflows with our API

### In-Context Editor
Translate and edit copy of your application on your website in the browser.

### Contextual View
Every translation can be viewed directly on the site where it actually occurs. Your translators will better understand the meaning of text portions.

### API
Integrate our fast and secure API to automate your content workflows.

### Quick Setup
Integrate PhraseApp in your application in minutes

## Provisioning the add-on

PhraseApp can be added to cloudControl via the command line client:

    $ cctrlapp APP_NAME/DEP_NAME addon.add phrase.PLAN

Once the add-on has been provisioned the `PHRASE_AUTH_TOKEN` will be available within your list of credentials. It contains your authentication token that lets you connect to the PhraseApp API:

    $ cctrlapp APP_NAME/DEP_NAME addon | grep PHRASE_AUTH_TOKEN
    PHRASE_AUTH_TOKEN: d219d3abcgcxBahs72K1


## Installation and Usage

The PhraseApp In-Context-Editor can be used best in a separate staging environment for copywriters and translators. If you haven't done so already, we recommend you set up a staging environment as explained in the [cloudControl Documentation](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#development-staging-and-production-environments). This staging environment will be used by your translators to edit your website content.
Of course, you can use your local development environment for integration as well. If you decide to do so, simply apply the steps used for the "staging" environment to your "development" environment during the following steps.

If you have any questions, contact us at [https://phraseapp.com/en/contact](https://phraseapp.com/en/contact). We will guide you through the installation process.


### phrase command line tool ###

As a developer: When you're on a Mac or Linux system and have a version of ruby installed, you can access PhraseApp via your command line by installing the [phrase ruby gem](https://rubygems.org/gems/phrase):

    gem install phrase

With this client you can upload translations to the API and export updated translations (language files, locales) from PhraseApp as described in the next sections.


### Setup using Django, Symfony or AngularJS

Setup with Symfony, Django or AngularJS is explained in our [Installation Guide](http://docs.phraseapp.com/guides/in-context-editor/setup/)

### Setup using Ruby on Rails or Sinatra

*The following integration guide assumes your application uses bundler for gem dependency management.*


#### Add the gem

Add the phrase gem to your staging environment:
    
    group :development, :staging do
      gem 'phrase'
    end

and install the gem with the bundle command:

    $ bundle install
    

#### Initialize phrase gem

1) You need to enable phrase within your application and provide the authentication token. You can do this by adding the following two lines to an initializer.

Rails: Add this to a new initializer in config/initializers/phrase.rb:

    Phrase.enabled = true
    Phrase.auth_token = ENV['PHRASE_AUTH_TOKEN']
  
Sinatra: Add this to your main application file (e.g. "application.rb"):

    Phrase.enabled = true
    Phrase.auth_token = ENV['PHRASE_AUTH_TOKEN']
  
Sinatra: You might also have to require phrase in your app file to load phrase when you start your application in "staging" environment:

    if ENV['RACK_ENV'] == 'staging'
      require "phrase"
    end

2) Now you configure phrase with your phrase add-on PHRASE_AUTH_TOKEN. we assume that "en" English is your default language in the example:

    $ bundle exec phrase init --secret=PHRASE_AUTH_TOKEN --default-locale=en

This will generate a .phrase config file in your project folder that stores your PHRASE_AUTH_TOKEN. We recommend adding it to your .gitignore file to not accidently expose this through your code repository.

3) Upload any existing locale files from your app:

    $ bundle exec phrase push ./config/locales/`
    
You've now uploaded all existing translations and translation keys to PhraseApp.
    
4) To download updated translations from PhraseApp and use them in "production", you use the "phrase pull"-command:

    $ bundle exec phrase pull

Per default phrase does not overwrite existing locale files. This is to keep formal locale files (like time format and currency configurations) from being accidentally overwritten. Instead it stores downloaded locale files in the new folder "phrase/locales/". In order to load locales from this new path (in addition to your locales in "config/locales") in the "production" environment you have to add the folder to the i18n load path. 

Rails: Add this line to your "production.rb" configuration file:

    config.i18n.load_path = Dir[Rails.root.join('phrase', 'locales', '*.yml').to_s]
    
Sinatra: You can set the i18n load path in the config.ru file:

    if ENV['RACK_ENV'] == 'production'
      I18n.load_path = Dir[File.join(File.dirname(__FILE__), 'phrase', 'locales', '*.yml').to_s]
    else
      I18n.load_path = Dir[File.join(File.dirname(__FILE__), 'config', 'locales', '*.yml').to_s]
    end
    
5) To enable the In-Context Editor in an environment, simply add the javascript to your application layout file:
    
    <script>
    var phrase_auth_token = '<%= ENV['PHRASE_AUTH_TOKEN'] %>';
    (function() {
      var phraseapp = document.createElement('script'); phraseapp.type = 'text/javascript'; phraseapp.async = true;
      phraseapp.src = ['https://', 'phraseapp.com/assets/phrase/0.1/app.js?', new Date().getTime()].join('');
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(phraseapp, s);
    })();
    </script>

Make sure that this javascript is loaded only in the environment you want your copywriters and translators to work on the text on and ensure that the In-Context Editor is enabled by configuring this in your initializer (as shown above):

    Phrase.enabled = true

#### Deploy application to staging environment

To finally get a look at the In-Context Editor you simply have to deploy your application to a staging system and view the site in your web browser.

You should see your application with the PhraseApp In-Context Editor loaded. To create your first user with translation privileges, you have to log into PhraseApp by following the "Login" link in your Add-On Management Panel.

After logging in, you can access the User Management from the "Account" menu. Simply create a user with an email and password of your choice and send out an invite. After signing in your translator can now edit your copywriting live on your staging application!


#### Deploy translations

After you have finished translating your application or feature, you will need to update your translation files for a production release.

1) Download the updated language files from PhraseApp using "phrase pull":

    $ bundle exec phrase pull

2) Commit the changed language files:

    $ git add ./phrase/locales
    $ git commit -m "added new translations"

3) Push the changes to your production repository:

    $ cctrlapp APP_NAME/DEP_NAME push
    $ cctrlapp APP_NAME/DEP_NAME deploy
    
4) The production system now uses the new locale files including your latest translations.

**Be aware that file caches may cause updates to not change the content of your application in the browser immediately. Your engineering team will know whether this could be the case or not.**

## Workflow

With phrase your translation workflow will get much easier:

1.  Add a new feature to your app that requires translation
2.  Add the new keys to phrase by uploading the locale files to phrase
3.  Deploy your new code to your staging environment and tell your translators to edit the copy in their language
4.  Non-visible keys can be translated in our Translation Center web interface
5.  Download the updated locale files from PhraseApp using "phrase pull" and deploy them to production


## Translation Center

Translation Center lets you manage:

* Locales/Languages
* Keys
* Translations
* Users 
* Tags
* Webhooks

To sign in to your Translation Center, view your Add-on Settings within your cloudControl console and follow the "Login" link next to the Phrase Add-on. After signing in to PhraseApp you can manage your account, users, locales and more.

*For more information on the features available within the phrase translation center please see the feature tour at [phraseapp.com/features](https://phraseapp.com/features).*


## Removing the add-on

phrase can be removed via the command line client:

    $ cctrlapp APP_NAME/DEP_NAME addon.remove phrase.PLAN

**Warning:** This will destroy all data associated with your phrase account and cannot be undone!


## Other platforms and languages

PhraseApp supports all common localization formats, including YAML, JSON, Gettext, Properties, XLIFF, Android, iOS, ResX and many more.

You can always access your localization files and translations from within the PhraseApp Translation Center.

If you need support for a platform or language that is not yet supported just contact us at [https://phraseapp.com/en/contact](https://phraseapp.com/en/contact)


## Support

If you have any questions regarding phrase, feel free to contact us at [https://phraseapp.com/en/contact](https://phraseapp.com/en/contact).


## Additional resources

Additional resources are available at:

* [PhraseApp Feature Overview](https://phraseapp.com/features)
* [PhraseApp Documentation](http://docs.phraseapp.com/)
* [PhraseApp In-Context Editor Setup](http://docs.phraseapp.com/guides/in-context-editor/setup/)
* [Support Contact](https://phraseapp.com/en/contact)
* [Example cloudControl application using Sinatra](https://github.com/phrase/phrase_example_cloudcontrol_app)
