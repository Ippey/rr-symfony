<?php

namespace App\Command;

use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

class WorkerCommand extends Command
{
    protected static $defaultName = 'app:worker';
    protected static $defaultDescription = 'Add a short description for your command';
    private KernelInterface $kernel;

    /**
     * PsrWorkerCommand constructor.
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

    /**
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rrWorker = Worker::create();

        $psr17Factory = new Psr17Factory();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        $psrWorker = new PSR7Worker($rrWorker, $psr17Factory, $psr17Factory, $psr17Factory);

        while ($psrRequest = $psrWorker->waitRequest()) {
            try {
                $symfonyRequest = $httpFoundationFactory->createRequest($psrRequest);
                $symfonyResponse = $this->kernel->handle($symfonyRequest);
                $psrResponse = $psrHttpFactory->createResponse($symfonyResponse);
                $psrWorker->respond($psrResponse);
            } catch (Throwable $e) {
                $psrWorker->getWorker()->error((string) $e);
            }
        }

        return Command::SUCCESS;
    }
}
