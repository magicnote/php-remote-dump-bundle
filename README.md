# RemoteDumpBundle

A tool used for remote debug php code.

## Installation
RemoteDumpBundle depends on nodejs.

### Composer
Using Composer for installation:

```
{
    "require": {
        "wusuopu/remote-dump-bundle": "dev-master",
    }
}
```


### app/AppKernel.php
Register the RemoteDumpBundle in 'dev' && 'test' enviroment:

```
public function registerBundles()
{
    // ...
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
    // ...
        $bundles[] = new Wusuopu\RemoteDumpBundle\RemoteDumpBundle();
    }
}
```

## Nodejs
Install nodejs packages.

```
$ cd SocketServer
$ npm install
```

## Usage
Start web server:

```
$ cd SocketServer
$ node app.js
```

Then open the url `http://127.0.0.1:9090/` in webbrowser.


Use in php:

```php
use Wusuopu\RemoteDumpBundle\Util\DumpUtil;
DumpUtil::dump($data, $url = "http://127.0.0.1:9090/");
```

Now in the webbrowser, the data will be displayed.