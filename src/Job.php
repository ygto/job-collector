<?php namespace JobCollector;


interface Job
{
    public function handle();

    public function rollback();

    public function onSuccess();

    public function onError();
}