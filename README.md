This YAWIK module pushes and manages job openings to/on Stackoverflow Talent via its API.

Requirements
------------

- A running YAWIK
- An authorization code from stackoverflow.com
  (obtain one via talent@stackoverflow.com)

Installation
------------

Checkout this repository into you modules directory of your YAWIK installation and enable the module by:

<pre>
cp modules/YawikStackoverflowApi/config/YawikStackoverflowApi.module.php.dist config/autoload/YawikStackoverflowApi.module.php
</pre>

Or install the module using composer

<pre>
composer require cross-solution/yawik-stackoverflow-api
</pre>

Licence 
-------

MIT

https://github.com/cross-solution/YawikStackoverflowApi/blob/master/LICENSE
