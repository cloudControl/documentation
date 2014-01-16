# Userbin


[Userbin](https://userbin.com) is the easiest way to setup, use and maintain a secure user authentication system for both your web and mobile apps, while keeping the users in your own database.

Userbin provides a set of login, signup, and password reset forms that drop right into your application without any need of styling or writing markup. Connect your users via traditional logins or third party social networks. We take care of linking accounts across networks, resetting passwords and sending out necessary emails, while keeping everything safe and secure.



## Adding the Userbin add-on
To add the Userbin add-on use the addon.add command.

```bash
$ cctrlapp APP_NAME/DEP_NAME addon.add userbin.PLAN
```

Replace `userbin.PLAN` with a valid plan, e.g. `userbin.free`.

When added, Userbin automatically creates a new account for you. You can access Userbin for your deployment within the [web console](https://www.cloudcontrol.com/console) (go to the specific deployment, choose "Add-Ons" tab and click Userbin login).


## Using with Ruby on Rails


### Installation and configuration

Add the `userbin` gem to your `Gemfile`

```ruby
gem "userbin"
```

Install the gem

```bash
bundle install
```

Create `config/initializers/userbin.rb` and configure your credentials.
> If you don't configure the `app_id` and `api_secret`, the Userbin module will read the `USERBIN_APP_ID` and `USERBIN_API_SECRET` environment variables.

```ruby
Userbin.configure do |config|
  config.app_id = "YOUR_APP_ID"
  config.api_secret = "YOUR_API_SECRET"
end
```

Implement getter and setter for your user model. For more information about the available attributes in the profile see the [Userbin profile](https://userbin.com/docs/profile) documentation.

```ruby
config.find_user = -> (userbin_id) do
  User.find_by_userbin_id(userbin_id)
end

# will be called when a user signs up
config.create_user = -> (profile) do
  User.create! do |user|
    user.userbin_id = profile.id
    user.email      = profile.email
    user.photo      = profile.image
  end
end
```

Migrate your users to include a reference to the Userbin profile:

```bash
rails g migration AddUserbinIdToUsers userbin_id:integer:index
rake db:migrate
```


### Authenticating users

Userbin keeps track of the currently logged in user which can be accessed through `current_user` in controllers, views, and helpers. This automatically taps into libraries such as the authorization library [CanCan](https://github.com/ryanb/cancan).

```erb
<% if current_user %>
  <%= current_user.email %>
<% else %>
  Not logged in
<% end %>
```

To set up a controller with user authentication, just add this `before_filter`:

```ruby
class ArticlesController < ApplicationController
  before_filter :authorize!

  def index
    current_user.articles
  end
end
```

> You can always access the [Userbin profile](https://userbin.com/docs/profile) for the logged in user as `current_profile` when you need to access information that you haven't persisted in your user model.



## Using with Node.js

### Installation and configuration

Install the `userbin` package.

```bash
$ npm install userbin
```

Include the Userbin node packages in your app.js or server.js.

```javascript
var userbin = require('userbin');
```

Configure the Userbin module with the credentials you got from signing up.

```javascript
userbin.config({
  appId: 'YOUR_APP_ID',
  apiSecret: 'YOUR_API_SECRET'
});
```

> If you don't configure the `appId` and `apiSecret`, the Userbin module will read the `USERBIN_APP_ID` and `USERBIN_API_SECRET` environment variables.

Insert the Userbin authentication middleware after the cookieParser (and add the cookieParser if not present):

```javascript
app.use(connect.cookieParser()); // or express.cookieParser()
app.use(userbin.authenticate());
```

Implement getter and setter for your user model. For more information about the available attributes in the profile see the [Userbin profile](https://userbin.com/docs/profile) documentation.

```javascript
userbin.config({
  findUser: function(userbinId, done) {
    User.findOne({ userbinId: userbinId }, function(err, user) {
      done(user);
    });
  },

  createUser: function(profile, done) {
    var user = User.new({
      userbinId : profile.id,
      email     : profile.email,
      photo     : profile.image
    });
    user.save(function(err, user) {
      done(user);
    });
  }
})
```


### Authenticating users

Userbin keeps track of the currently logged in user which can be accessed in your controllers and views through the `req.currentUser` property which needs to be explicitly passed to your views.

Insert the `userbin.authorize()` middleware to protect a route fron non-logged in users.

```javascript
app.get('/account', userbin.authorize(), function(req, res) {
  res.render('account', {currentUser: req.currentUser});
});
```

> You can always access the [Userbin profile](https://userbin.com/docs/profile) for the logged in user as `req.currentProfile` when you need to access information that you haven't persisted in your user model.



## Using with PHP

### Installation and configuration

Download Userbin into your project:

```bash
$ curl -O https://raw.github.com/userbin/userbin-php/master/userbin.php
```

Download the configuration file to the same directory:

```bash
$ curl -O https://raw.github.com/userbin/userbin-php/master/userbin.conf.php
```

Configure the Userbin module with the credentials you got from signing up.

```php
<?php
$config = array(
  'app_id'     => 'YOUR APP ID',
  'app_secret' => 'YOUR APP SECRET',
);
```

Implement getter and setter for your user model. For more information about the available attributes in the profile see the [Userbin profile](https://userbin.com/docs/profile) documentation.

```php
<?php
$config = array(
  'find_user' => function($id) {
    $user = User::model()->find('id=?', array($id));
    return $user;
  },

  'create_user' => function($profile) {
    $user = new User;
    $user->email = $profile['email'];
    $user->image = $profile['image'];
    $user->save();
    return $user->id;
  },
);
```

> If you don't configure the `appId` and `apiSecret`, the Userbin module will read the `USERBIN_APP_ID` and `USERBIN_API_SECRET` environment variables.

All you need to is to include `userbin.php` at the top of your files, configure it with you App ID and API secret, and finally run the Userbin authentication sync. The `authenticate` method will make sure that the user session is refreshed when it expires.

> If you're not using [output buffering](http://php.net/manual/en/book.outcontrol.php) this needs to be done before any output has been written since Userbin will modify headers.

```php
<?php require_once 'userbin.php'; ?>
```

Include [Userbin.js](https://userbin.com/js/v0) at the bottom of all your web pages to enable form helpers and session handling.

```php
      ...
      <?= Userbin::javascript_include_tag(); ?>
  </body>
</html>
```


### Authenticating users

```php
<?php Userbin::authenticate(); ?>
```

Userbin keeps track of the currently logged in user:

```php
<? if (Userbin::current_user()): ?>
  <? $user = Userbin::current_user() ?>
  Welcome to your account, <?= $user['email'] ?>
<? else: ?>
  Not logged in
<? endif; ?>
```

Put the `authorize` method at the top of a file to halt the execution and render a login page if the user is not logged in:

```php
<?php Userbin::authorize(); ?>
```

> You can always access the [Userbin profile](https://userbin.com/docs/profile) for the logged in user as `Userbin::current_profile()` when you need to access information that you haven't persisted in your user model.


## Using with mobile

Please see the documentation for [iOS](https://userbin.com/docs/ios) and [Android](https://userbin.com/docs/android) on how to get started with Userbin in mobile apps.

## Forms

Once you have set up authentication it's time to choose among the different ways of integrating Userbin into your application.

### Ready-made forms

The easiest and fastest way to integrate login and signup forms is to use the Userbin Widget, which provides a set of ready-made views which can be customized to blend in with your current user interface. These views open up in a popup, and on mobile browsers they open up a new window tailored for smaller devices.

`rel` specifies action; possible options are `login` and `logout`.

```html
<a href="/account" rel="login">Log in</a>
<a href="/account" rel="signup">Sign up</a>
```

### Social buttons

Instead of signing up your users with a username and password, you can offer them to connect with a social identity like Facebook or LinkedIn. To use these button you must first configure your social identiy providers from the [dashboard](https://userbin.com/dashboard). It is also possible to connect a social identity to an already logged in user and the two accounts will be automatically linked.

`rel` determines action. If the user didn't exist before, it's created, otherwise it's logged in.

```html
<a href="/account" rel="connect-facebook">Connect with Facebook</a>
<a href="/account" rel="connect-linkedin">Connect with LinkedIn</a>
```

### Custom forms

The ready-made forms are fairly high level, so you might prefer to use Userbin with your own markup to get full control over looks and behavior.

If you create a form with `name` set to `login` or `signup`, the user will be sent to the URL specified by `action` after being successfully processed at Userbin.

Inputs with name `email` and `password` are processed, others are ignored.

If you add an element with the class `error-messages`, it will be automatically set to `display: block` and populated with a an error message when something goes wrong. So make sure to it is `display: hidden` by default.

```html
<form action="/account" name="signup">
  <span class="error-messages"></span>
  <div class="row">
    <label>E-mail</label>
    <input name="email" type="text"></input>
  </div>
  <div class="row">
    <label>Password</label>
    <input name="password" type="password"></input>
  </div>
  <button type="submit">Sign up</button>
</form>
```

### Log out

Clears the session and redirects the user to the specified URL.

```html
<a href="/" rel="logout">Log out</a>
```


## Dashboard

With Userbin you get an admin dashboard out of the box.

- Invite, update, remove and ban users
- Log in as any of your users for debugging
- Configure user validation, access rights and login methods
- See who is using your web or mobile app in real-time.
- Customize copy and appearance of your transactional emails.


## Removing Userbin

```bash
$ cctrlapp APP_NAME/DEP_NAME addon.remove userbin.PLAN
```

## Internal access credentials

You can view your Userbin crendentials via:

```bash
$ cctrlapp APP_NAME/DEP_NAME addon userbin.PLAN
```

```bash
Addon                    : userbin.PLAN

 Settings
   USERBIN_APP_ID           : 689751686362727
   USERBIN_API_SECRET       : P1pBdtjAJvQP7qsqz5zrLC1pDCxBJqFw
```

## Support & feedback

Any non-support related issues or product feedback is welcome by [email](mailto:support@userbin.com).
