This YAWIK module pushes and manages job openings to/on Stackoverflow Talent via its API.

Requirements
------------

- A running YAWIK >= 0.28
- An authorization code from stackoverflow.com
  (obtain one via talent@stackoverflow.com)

Installation
------------

Checkout this repository into you modules directory of your YAWIK installation and enable the module by:

<pre>
cp modules/StackoverflowApi/config/StackoverflowApi.module.php.dist config/autoload/StackoverflowApi.module.php
</pre>

Or install the module using composer

<pre>
composer require yawik/stackoverflow-api
</pre>

Licence 
-------

MIT

https://github.com/yawik/StackoverflowApi/blob/master/LICENSE
