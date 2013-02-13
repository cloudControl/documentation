# Run Command Examples for Ruby

Run command (or SSH session) is really useful for the Ruby programmers to use `rails c` or `rake`
in a deployment's environment.
Here are some examples how it can be used for everyday Ruby tasks such as
running the migrations or using the Rails console.

To migrate the database:

~~~bash
$ ctrlapp APP_NAME/DEPLOYMENT run "rake db:migrate"
~~~

To run the rails console:

~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT run "rails c"
~~~

Here is a full example in which multiple commands are run in bash (it assumes there is a model Post):

~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT run bash
Connecting...
Warning: Permanently added '[10.250.134.126]:19845' (RSA) to the list of known hosts.

u19845@dep8xxzcqz9-19845:~/www$ rake db:migrate
Connecting to database specified by database.yml
Migrating to CreatePosts (20121029153226)
==  CreatePosts: migrating ====================================================
-- create_table(:posts)
   -> 0.0370s
==  CreatePosts: migrated (0.0371s) ===========================================

u19845@dep8xxzcqz9-19845:~/www$ rails c
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

u19845@dep8xxzcqz9-19845:~/www$ exit
Connection to 10.250.134.126 closed.
Connection to ssh.cloudcontrolled.net closed.
~~~
