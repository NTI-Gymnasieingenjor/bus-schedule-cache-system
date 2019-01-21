# Cobalt Lama

Essentially just a cache for UL:s internal API and ResRobots API. The internal API used by UL is used as the primary data source as it is updated in real-ish time and also includes deviations. In we end up on an IP blacklist, or if the internal API is down, the ResRobot API will be used instead. The ResRobot API responses will be pre-cached 7 days (by default, can be changed in config.php). This means that the Cobalt Lama data source is able to reliably provide data for up to 7 days (by default, can be changed in config.php) without an internet connection.

**TL;DR** UL:s internal API is the primary data source, the ResRobot API is used as a failsafe.

```
src/
    cache.php
    util.php
config.php
failover.php
realtime.php
```
