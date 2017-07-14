<?php namespace JobCollector;

class Collector
{
    protected $collection = [];
    protected $handled = [];
    protected $onSuccess = [];
    protected $onError;

    /**
     * @param Job $job
     * @return $this
     */
    public function push(Job $job)
    {
        $this->collection[] = $job;

        return $this;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $result = true;
        $activeJob = null;
        try {
            foreach ($this->collection as $job) {
                $activeJob = $job;
                $this->handled[] = $job;
                $job->handle();
                $this->onSuccess[] = $job->onSuccess();
            }
        } catch (\Exception $e) {
            $this->onError = $activeJob->onError();
            $this->rollback();

            return false;
        }

        return $result;
    }

    protected function rollback()
    {
        while ($this->handled) {
            $job = array_pop($this->handled);
            $job->rollback();
        }
    }

    public function getSuccess()
    {
        return $this->onSuccess;
    }

    public function getError()
    {
        return $this->onError;
    }
}
