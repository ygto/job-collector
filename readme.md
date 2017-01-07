**Job Collector**
==================

    don't mess around so much when writing code.

Installation
------------
    composer require ygto/job-collector
or 

    "require": {
        "ygto/job-collector": "^1.0"
    }
JobCollector\Job
--
**JobCollector\Job** interface has 4 methods
- handle()  first **handle** method run.
- rollback()  if **handle** method ***throw exception*** then **rollback** method run. 
- onSuccess()  if **handle** method run successfully then **onSuccess** run  and keep the method's return ;
- onError() if **handle** method ***throw exception*** then **onError** run and keep the method's return ;

Collector
--
**JobCollector\Collector** has 4 methods

- push(\JobCollector\Job $job) push job to collector
- handle() run pushed jobs if all jobs run successfully return ***true*** else return ***false***
- getSuccess() return ***onSuccess*** methods return
- getError() return ***onError*** methods return

Usage
----

//GetPayment.php
```
<?php namespace Jobs;

use JobCollector\Job;

class GetPayment implements Job
{
    protected $user;
    protected $order;
    protected $payment;

    protected $success = 'payment handled successfully.';
    protected $error = 'there is a error in payment';

    public function __construct($user, $order, $payment)
    {
        $this->user = $user;
        $this->order = $order;
        $this->payment = $payment;
    }

    public function handle()
    {
        if (!$this->payment->getPayment($this->user, $this->order)) {
            //payment error setted;
            $this->error = $this->payment->getError();
            throw new \Exception($this->error);
        }
    }

    public function rollback()
    {
        $this->payment->refundPayment($this->user, $this->order);
    }

    public function onSuccess()
    {
        return $this->success;
    }

    public function onError()
    {
        return $this->error;
    }
}
```
//ExampleController.php

```
<?php

use JobCollector\Collector;
use Jobs\CheckUserBalance;
use Jobs\GetPayment;
use Jobs\PrintPayslip;

class ExampleController
{

    public function checkout(User $user, Order $order, Payment $payment, PdfLibrary $pdf)
    {
        $collector = new JobCollector\Collector();
        $collector->push(new CheckUserBalance($user, $order))
            ->push(new GetPayment($user, $order, $payment))
            ->push(new PrintPayslip($user, $order, $pdf));

        if ($collector->handle()) {
            //$collector->getSuccess();
        } else {
            //$collector->getError();
        }
    }
}
```