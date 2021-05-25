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
{"message":"Hello World!","path":"src\/Controller\/HelloWorldController.php"}

 %  % wrk http://localhost:8080/ 
Running 10s test @ http://localhost:8080/
  2 threads and 10 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.99ms  588.65us  20.88ms   86.98%
    Req/Sec     2.54k   156.34     2.77k    77.23%
  51037 requests in 10.10s, 11.78MB read
Requests/sec:   5053.13
Transfer/sec:      1.17MB

```