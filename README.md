# Road Runner Symfony Example

## Install and Run

```
composer install
vendor/bin/rr get
chmod 755 rr
composer run server
```

## Result

```
 % curl http://localhost:8080/
{"message":"Welcome to your new controller!","path":"src\/Controller\/HelloWorldController.php"}

 % wrk http://localhost:8080/
Running 10s test @ http://localhost:8080/
  2 threads and 10 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.60ms  616.10us  19.39ms   95.77%
    Req/Sec     3.17k   178.29     3.41k    75.50%
  63165 requests in 10.00s, 12.83MB read
Requests/sec:   6314.88
Transfer/sec:      1.28MB

```