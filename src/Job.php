<?php namespace Ygto\JobCollector;


interface Job
{
    /**
     * @return $this
     */
    public function handle();

    public function rollback();

    /**
     * @return array
     */
    public function onSuccess();

    /**
     * @return array
     */
    public function onError();
}
