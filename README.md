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

or:

```php
$this->getContainer()->get('wusuopu.remote_dump')->dump($data);
```

Now in the webbrowser, the data will be displayed.

### Config
It register some servie listeners for kernel event. You can disable/enable these config. The followings are default config.

```yml
wusuopu_remote_dump_listener_enable: true
wusuopu_remote_dump_listener.request: true
wusuopu_remote_dump_listener.controller: true
wusuopu_remote_dump_listener.view: true
wusuopu_remote_dump_listener.response: true
wusuopu_remote_dump_listener.finish_request: true
wusuopu_remote_dump_listener.terminate: false
wusuopu_remote_dump_listener.exception: true
```
