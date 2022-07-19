<?php

namespace RayNl\SendcloudForSimpleCommerce\Console\Commands;

use Illuminate\Console\Command;
use RayNl\SendcloudForSimpleCommerce\Services\SendcloudService;
use Statamic\Console\RunsInPlease;

class TestSendcloudIntegrationCommand extends Command
{
    use RunsInPlease;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'sendcloud:test-integration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if we can connect to Sendcloud with your provided keys.';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (SendcloudService::init()->canConnectToSendcloud()) {
            $this->info('We can connect to Sendcloud!');

            return true;
        }

        $this->error('We can\'t connect to Sendcloud. Something is going wrong. Check your credentials.');

        return false;
    }
}
