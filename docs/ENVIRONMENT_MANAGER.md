Environment Manager
---

The environment manager currenty to add as many environments as needed and provides a set of functions to determine what is the current one **by checking the host name of the current environment**.


## The Environment Manager
### Getting the Environment Manager

    $env_manager = Bebop::Env();

### Built-in environments
The environment manager comes with 3 built-in empty environments that you can fill in.

- `development`
- `staging`
- `production`

### Environment Manager Usage
#### Getting a single environment

    $env = Bebop::Env('production');
    $env = Bebop::Env()->get('production');
    
#### Adding a single environment

	Bebop::Env()->add('local');
    
#### Replacing a single environment

	Bebop::Env()->replace('development');

#### Checking if an environment exists
	
	Bebop::Env()->exists('development');
	
#### Checking if an environment is the current one
	
	Bebop::Env()->is('development');

#### Getting the current environment
	
	Bebop::Env()->getCurrent();

#### Getting the key of the current environment
	
	Bebop::Env()->getCurrentKey();
	
#### Removing an environment

	Bebop::Env()->remove('development');
	
## Single Environments
### Single Environment Usage

#### Adding a single host

	$env->addHost('localhost');

#### Adding several hosts at once

	$env->addHosts(array(
		'localhost',
		'local.dev'
	);

#### Get all hosts as an associative array

	$env->getHosts();
	
#### Check if an host exists

	$env->hasHost('localhost');
	
#### Check if it is the current one

	$env->isCurrent();
	
#### Get enviroment key

	$env->getKey();
	