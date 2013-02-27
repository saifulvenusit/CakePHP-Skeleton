_This template includes more information than a typical project requires, both to provide hints on possible things to include, as well as to make the process of filling it largely a matter of deleting information that is not applicable. Specifically; be sure to remove any notes and comments in italics, like this one. By convention, pseudo-variables you should replace are typically in ALLCAPS._


# [ProjectName](http://github.com/loadsys/PROJECT) #

_Brief app description. Why does it exist? Who uses it? Arbitrary 4 sentence limit._

* Production URL: http://PROJECT.com
* Staging URL: http://PROJECT.loadsysdev.com
* Project Management URL: http://loadsys.basecamphq.com/PROJECT
* Loadsys Project Docs: http://123.writeboard.com/MANAGERS_WRITEBOARD


## Environment ##

_**Always** include the minimum PHP version, PHP extensions (and versions) utilized, database software version, and any other **external** programs used. Think in particular about the production environment, even if a tool (like memcached) is not used locally in development._

* [CakePHP](https://github.com/cakephp/cakephp/tree/2.1.1) v2.1.1
* PHP v5.3+
	* ImageMagick (imagick) v6.0.3 / v6.7.8-10 
	* SSL2 (openssl)
	* Memcache (memcache)
* MySQL v5+
* Memcached (production)

_If there is a script to configure the environment (PHP, extensions, etc.), document its usage **in addition to** the actual requirements list._

* ruby (rvm|rbenv 1.9.3 preferably)
* bundle gem  ```gem install bundle```

_There are no "optional" installs. If a project has tests, developers are expected to be able to run them locally as well as they can run the app itself._


### Included Libaries and Submodules ###

_List any external packages that are either directly a part of the repo, or included as submodules. Include links to the package's homepage or repo, and the version number in use (if applicable). The list below is pre-populated with the submodules included in this CakePHP-Skeleton repo, and common add-ons._

* [DebugKit](https://github.com/cakephp/debug_kit/tree/2.0) v2.0
* [Loadsys Migrations](https://github.com/cakephp/debug_kit.git)
* [lessphp](https://github.com/leafo/lessphp)

* [jQuery](https://github.com/jquery/jquery/tree/1.7.1) v1.7.1
* [modernizr](https://github.com/Modernizr/Modernizr/tree/v2.0.6) v2.0.6
* [Twitter Bootstrap](https://github.com/twitter/bootstrap/tree/v2.0.0) v2.0.x
* [Backbone.js](http://backbonejs.org) v0.5.3
* [Underscore.js](http://underscorejs.org) v1.2.3


### cron Tasks ###

_Document anything that is expected to run outside of a normal web browser interface here. Include when it is supposed to run and any details about permissions, logging, etc._

```0 0,12	* * *	/var/sites/webroot/app/console/TASK > /var/sites/webroot/app/tmp/log/TASK.log 2>&1```




## Installation ##

_In general, document the series of steps necessary to set up the project on a new system (development or production). If there is a setup shell script, don't document its internal steps (the script itself does that), just how to run it. If setup is manual, list each step in order._

### Prep ###

1. Configure a webroot.
1. Create a new blank database.
1. Assign a user permissions to that database.

_Only keep the relevant section below for the given project._

_Automated instructions_

1. (The database will be configured during the clone process below.)

_Manual Instructions_

1. For development sites you need to set up the database config something like this:

		var $default = array(
			'datasource' => 'Database/Mysql',
			'persistent' => false,
			'host' => 'localhost',
			'database' => 'database',
			'login' => 'root',
			'password' => '',
			'prefix' => '',
		);

The bare schema for the database is located in ```config/db/db.sql```. You may also need to apply updates from ```config/db/db_updates.sql``` as well.


### Source Code ###

_Outline the process of getting the app ready to work on in a development environment._

1. Install the application in your webroot.

		git clone git@github.com:loadsys/PROJECT.git ./
		git submodule update --init --resursive
		ln -s /PATH/TO/cake/lib/Cake Lib/Cake
		cp Config/core.dev.php Config/core.php
		Console/cake setup
		Console/cake Migrations.migration run all
		

_Include "first time" steps that only need to be done once, but that a new dev wouldn't otherwise know to do at all._

1. If this is the first time you are setting up the application, there is a seed shell to start yourself off with a a test user and sample data:

		Console/cake seed

### Writeable Directories ###

_If there are setup and/or push scripts, these directories should be codified into them, but still should be documented here, especially if there are unusual ones._

* app/tmp/*
* app/plugins/uploads/files
* app/webroot/img/cache
* app/webroot/files



## Development ##

_Information a developer would need to work on the project in the "correct" way. (Tests, etc.)_

### Configuration ###

_The following sections are currently just copied from an active project as an example._

Configuration settings are stored in 3 places.

#### config/bootstrap.php ####
Default and/or production configs should be placed in here and then overwritten in one of the locations below.

#### config/config-{env}.php ####
Environment specific configs (dev, prod, etc) are placed in these files in a $config array. Anything placed in this file will override the defaults.

#### config/config.php ####
You can put local config settings like "debug" in here. These are specific to the installation and do not get committed to the repository. These will overwrite anything in the environment or default configs.

### Required Configuration Overrides ###

_Document anything that **must** be set locally for development. The goal is to avoid "gotchas" that cost devs time troubleshooting._

The following items are required to be overridden in your local config.php:

1. Common.base_url
Fixes generated urls by the Router class when used from shells.

		// Set to only the tld and any subdirectories without a trailing slash.
		Configure::write('Common.base_url', 'example.com/subfolder/subfolder');

### Migrations ###

_Always document the process for making changes to the database schema, even if it's just "Add an entry to the end of db\_updates.sql."_

The database is maintained using the CakePHP migrations plugin. Run the below command to update your database. The very first time you run this command, your database should be blank.

	sh .scripts/migrations.sh all

Run the command below after making database changes. When prompted to update schema.php, choose yes and then choose overwrite.

	sh .scripts/migrations.sh generate


## Testing ##

_If the project has a test suite (and it should!), document how to run tests at least, and where to write new ones (especially if the project is older or non-standard.)_

Automated testing can be done through the browser like normal (by visiting http://domain/test).

Command line automated test running is also possible with Guard. To install all the necessary gems, you'll first need Bundler.

``` bash
gem install bundler
```

Depending on how your ruby is installed, you may need `sudo`. Now use bundler to install the gems required for the automated testing.

``` bash
bundle install
```

Again, this may need `sudo`. This will install Guard and a lib for watching the filesystem. To start the file watcher, simply use the `guard` command.

``` bash
bundle exec guard
```

This will block the terminal while it waits for file changes. New files should get picked up as well. The `bundle exec` part is only required if you don't have ruby installed through rbenv or rvm. It makes the command run with the gemfile specified gems and versions.


## Immersion ##

_This section may make more sense to include with the "Project" documentation instead of the "repo" README..._

New users should all run through these steps to get familiar with the app and the features available.

__TBD__


## License ##

Copyright (c) 2013 CLIENT