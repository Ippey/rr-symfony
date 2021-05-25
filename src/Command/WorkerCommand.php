<?php

namespace App\Command;

use Spiral\RoadRunner\Http\HttpWorker;
use Spiral\RoadRunner\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class WorkerCommand extends Command
{
    protected static $defaultName = 'app:worker';
    protected static $defaultDescription = 'Add a short description for your command';
    private KernelInterface $kernel;

    /**
     * PsrWorkerCommand constructor.
     * @param string|null $name
     * @param KernelInterface $kernel
     */
    public function __construct(string $name = null, KernelInterface $kernel)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rrWorker = Worker::create();
        $worker = new HttpWorker($rrWorker);

        while ($worker->waitRequest()) {
            try {
                $request = Request::createFromGlobals();
                $response = $this->kernel->handle($request);
                $worker->respond($response->getStatusCode(), $response->getContent());
            } catch (\Throwable $e) {
                $worker->getWorker()->error((string)$e);
            }
        }
        return Command::SUCCESS;
    }
}
