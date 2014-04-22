# Run Command Examples for Ruby

Run command is really useful for the ruby programmers. Here are some examples how it can be used for the everyday ruby tasks such as running migrations or using rails console.

To migrate database:

~~~bash
$ exoapp APP_NAME/DEP_NAME run "rake db:migrate"
~~~

To run rails console:
~~~bash
$ exoapp APP_NAME/DEP_NAME run "rails c"
~~~

Here is a full example in which multiple commands are run in remote bash session:

~~~
$ exoapp APP_NAME/DEP_NAME run bash
Connecting...
Warning: Permanently added '[X.X.X.X]:<PORT>' (RSA) to the list of known hosts.
u<PORT>@<DEP_ID>-<PORT>:~/www$ rails g scaffold Post title:string content:text
      invoke  active_record
Connecting to database specified by database.yml
      create    db/migrate/20121029153226_create_posts.rb
      create    app/models/post.rb
      invoke    test_unit
      create      test/unit/post_test.rb
      create      test/fixtures/posts.yml
      invoke  resource_route
       route    resources :posts
      invoke  scaffold_controller
      create    app/controllers/posts_controller.rb
      invoke    erb
      create      app/views/posts
      create      app/views/posts/index.html.erb
      create      app/views/posts/edit.html.erb
      create      app/views/posts/show.html.erb
      create      app/views/posts/new.html.erb
      create      app/views/posts/_form.html.erb
      invoke    test_unit
      create      test/functional/posts_controller_test.rb
      invoke    helper
      create      app/helpers/posts_helper.rb
      invoke      test_unit
      create        test/unit/helpers/posts_helper_test.rb
      invoke  assets
      invoke    js
      create      app/assets/javascripts/posts.js
      invoke    css
      create      app/assets/stylesheets/posts.css
      invoke  css
      create    app/assets/stylesheets/scaffold.css
u<PORT>@<DEP_ID>-<PORT>:~/www$ rake db:migrate
Connecting to database specified by database.yml
Migrating to CreatePosts (20121029153226)
==  CreatePosts: migrating ====================================================
-- create_table(:posts)
   -> 0.0370s
==  CreatePosts: migrated (0.0371s) ===========================================

u<PORT>@<DEP_ID>-<PORT>:~/www$ rails c
Connecting to database specified by database.yml
Loading production environment (Rails 3.2.8)
irb(main):001:0> Post.all
  Post Load (1.1ms)  SELECT `posts`.* FROM `posts`
=> []
irb(main):002:0> p = Post.new title: "my title", content: "my content"
=> #<Post id: nil, title: "my title", content: "my content", created_at: nil, updated_at: nil>
irb(main):003:0> p.save
   (1.1ms)  BEGIN
  SQL (1.2ms)  INSERT INTO `posts` (`content`, `created_at`, `title`, `updated_at`) VALUES ('my content', '2012-10-29 15:33:42', 'my title', '2012-10-29 15:33:42')
   (16.7ms)  COMMIT
=> true
irb(main):004:0> Post.all
  Post Load (1.3ms)  SELECT `posts`.* FROM `posts`
=> [#<Post id: 1, title: "my title", content: "my content", created_at: "2012-10-29 15:33:42", updated_at: "2012-10-29 15:33:42">]
irb(main):005:0> exit
u<PORT>@<DEP_ID>-<PORT>:~/www$ exit
Connection to X.X.X.X closed.
Connection to sshforwarder.app.exo.io closed.
~~~

The same could be accomplished if the multiple individual commands were chained:

~~~
$ exoapp APP_NAME/DEPLOYMENT run "rails g scaffold Post title:string content:text && rake db:migrate && rails c"
~~~

The previous example is quite artificial and it's usefulness in the real world would be questionable. The changes to the database are retained, but all the generated files are lost. Nevertheless it demonstrates more complex usage of the run command and gives a bit of insight in it's power.
