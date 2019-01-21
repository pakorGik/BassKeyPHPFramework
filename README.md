# ***BassKeyPHP Framework***

Light MVC framework for ***PHP7*** with example bundle and configurations.

## Getting Started

***BassKeyPHP*** framework was created to make management of application by domain address easy. Configuration is created in .yml files. Assumptions bundle structure, and views running are similar to Symfony framework.

### Prerequisites

To install BKPHP we will need server PHP7 or more and a http server as well, for example apache2 or nginx.

## Installing

Clone framework repository with name of your project.

```
git clone https://github.com/pakorGik/BassKeyPHPFramework exampleProject
cd exampleProject
```

or clone project and change the name of the directory. 

```
git clone https://github.com/pakorGik/BassKeyPHPFramework
mv -v BassKeyPHPFramework exampleProject
cd exampleProject
```

## Installing composer

Framework use [composer](https://getcomposer.org/) tool.
After clone project necessary is install it by composer.
<br/>

If have installed composer tool:

```
composer install
```

Or use composer.phar file:

```
php composer.phar install
```

### Server configuration

***app/Kernel.php*** is the initial file where execution begins and executes all the framework processes.<br />
It is necessary to create rewrite rules in server, to redirect all paths to file ***app/Kernel.php***

### Example apache2 server configuration

In project directory file ***.htaccess*** is the file with apache2 server rules. <br />
All paths will be redirected to ***app/Kernel.php*** except ***assets*** directory.

```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{REQUEST_URI} !/assets/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ ./app/Kernel.php [L,QSA]
</IfModule>
``` 

For the rewrite rules to work correctly, it is necessary to enable rewrite rules in apache2 server configuration.<br />
How to turn on rewrite engine issue: [stackoverflow.com](https://stackoverflow.com/questions/869092/how-to-enable-mod-rewrite-for-apache-2-2)

# Simple configuration of framework 

***Example configuration for LocalHost domain.***

Framework BKPHP was created to make managing many domains possible. 
That is why every configuration depends on the domain.<br /><br />

***app/Config/base_config.yml*** is main configuration file. This is where all the other config files will be included. <br />

Below is example configuration, for domain ***"localhost"***, and base url ***"localhost/exampleProject/"***.

```
#file: app/Config/base_config.yml

localhost:
  domain: "localhost"
  base-url: "localhost/exampleProject/"
  config:
    imports:
      - { resource: '%root_dir%/LocalHost/config.yml' }
```

Constance variable ***%root_dir%*** is a path to ***app*** directory. <br />
Snippet below refers to importing config from LocalHost directory.

```
  config:
    imports:
      - { resource: '%root_dir%/LocalHost/config.yml' }
```

Directory: ***app/Config/LocalHost***<br />
***File list:***

* config.yml
* database.yml
* memcache.yml
* routing.yml

***'config.yml'*** imports other files from this directory - to configure database, memcache and routing.

## Routing configuration

File ***app/Config/LocalHost/routing.yml***

```
home:
  address: /
  defaults: { _controller: "DemoBundle:Demo:Init" }
```

In this file, there is example routing for home page. It is essential to declare unique name, define address and set config for action.
Action is a method in controller class. In this example:

```
  defaults: { _controller: "DemoBundle:Demo:Init" }
```

From directory ***/src*** from bundle ***DemoBundle*** from directory ***Controllers*** from class ***Demo***[file ***Demo.php***]. 
Will be executed method ***InitAction***.
All the methods that are actions end in 'Action'. 
So in configuration we don't explicitly specify as it is implicitly understood by the framework.


Class ***src/DemoBundle/Controllers/Demo***

```
namespace DemoBundle\Controllers;

use BassKey\Components\System\Controller;

class Demo extends Controller
{
    public function InitAction() { ... }
}
```

## Bundles | Controllers | Actions

***BassKeyPHP*** framework makes it easy to manage different parts of applications using bundle structure.<br />
***Bundle*** is a directory in ***/src*** directory. Name of bundle is optional.

### Bundles

Mandatory directory:

* Controllers
* Views

Optional directory:

* Config
* Entity
* Models

Note: These bundles should be created in a way to make easy transfer between projects possible.

### Controllers

Controller is a class created in ***src\BundleName\Controllers*** directory. This class must extend ***BassKey\Components\System\Controller\Controller*** class. In this class, we can declare actions and actions execute views.

### Actions

Example action:

```
    public function InitAction() { ... }
```

Define parameters and turn on developer tool to see details of controller and view for easy debugging. 

```
    /*
     * Example parameters, transfer to view
     */
    $exampleParameters = array(
        "title" => "John",
        "students" => array("Peter", "Adam", "Tom"),
    );
    
    /*
     * Developer tool
     * Show controllers details
     */
    $this->runDevDebugger();
```

Render view using php views template.

```
    /*
     * Render view php type
     */
    $this->RenderPhp("DemoBundle:Demo:home", $exampleParameters);
```

Render view using twig template:

```    
    /*
     * Render view twig type
     */
    $this->RenderTwig("DemoBundle:Demo:home", $exampleParameters);
```

Both of these functions have two parameters.

Parameter ***"DemoBundle:Demo:home"***

Is a rendering path of view file. First part "DemoBundle" is bundle name, "Demo" is name of directory inside View directory: 
***src\DemoBundle\Views\Demo***. "home" is name of file.<br />

Parameter ***$exampleParameters***
Is array with parameters are will be sand to view element.

***RenderTwig*** method will looking for file with extensions:
* .twig.html
* .twig
* .html.twig

***RenderPHP*** method will be looking for file with extensions:    
* .php
* .html

## Generally
Above is simple configuration and basic functions of framework. For more information check wiki(not ready yet). 

## Author

**Paweł Korczak** *PaKor*

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details
