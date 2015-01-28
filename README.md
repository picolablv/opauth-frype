Opauth-Frype
=============
[Opauth][1] strategy for Frype (draugiem.lv) authentication.

Implemented based on https://www.draugiem.lv/applications/dev/docs/passport/

Getting started
----------------
1. a. Install Opauth-Frype with git:
   ```bash
   cd path_to_opauth/Strategy
   git clone https://github.com/rixtellab/opauth-frype.git
   ```
1. b. Install Opauth-Frype with composer. Add to your composer.json:
```
{
      "require" : {	 
        "opauth/opauth": ">=0.2.0",
        "rixtellab/opauth-frype": "dev-master"
      }
    }
```     
2. Create Frype (Draugiem.lv) passport application at https://www.draugiem.lv/applications/dev/create/
    

3. Configure Opauth-Frype strategy with at least `App id` and `App key`.

4. Direct user to `http://path_to_opauth/frype` to authenticate

Strategy configuration
----------------------

Required parameters:

```php
<?php
'Frype' => array(
            'app_key' => 'YOUR APP KEY',
            'app_id' => 'YOUR APP ID'
        )
```

License
---------
Opauth-Frype is MIT Licensed  
Copyright © 2014 Andris Šaudinis (http://www.rixtellab.com)

[1]: https://github.com/rixtellab/opauth-frype
