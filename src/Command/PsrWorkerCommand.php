<?php

namespace App\Command;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Spiral\RoadRunner\Http\HttpWorker;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class PsrWorkerCommand extends Command
{
    protected static $defaultName = 'app:psr-worker';
    protected static $defaultDescription = 'Add a short description for your command';
    private ServerRequestFactoryInterface $serverRequestFactory;
    private StreamFactoryInterface $streamFactory;
    private UploadedFileFactoryInterface $uploadedFileFactory;
    private KernelInterface $kernel;

    /**
     * PsrWorkerCommand constructor.
     * @param string $name
     * @param KernelInterface $kernel
     * @param ServerRequestFactoryInterface $serverRequestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UploadedFileFactoryInterface $uploadedFileFactory
     */
    public function __construct(string $name = null, KernelInterface $kernel, ServerRequestFactoryInterface $serverRequestFactory, StreamFactoryInterface $streamFactory, UploadedFileFactoryInterface $uploadedFileFactory)
    {
        parent::__construct($name);
        $this->kernel = $kernel;
        $this->serverRequestFactory = $serverRequestFactory;
        $this->streamFactory = $streamFactory;
        $this->uploadedFileFactory = $uploadedFileFactory;
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
        $psrWorker = new PSR7Worker($rrWorker, $this->serverRequestFactory, $this->streamFactory, $this->uploadedFileFactory);

        while ($worker->waitRequest()) {
            try {
                $request = Request::createFromGlobals();
                $response = $this->kernel->handle($request);
                $worker->respond($response->getStatusCode(), $response->getContent());
            } catch (\Throwable $e) {
                $psrWorker->getWorker()->error((string)$e);
            }
        }
        return Command::SUCCESS;
    }
}
