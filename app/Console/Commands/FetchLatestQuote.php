<?php

namespace App\Console\Commands;

use App\Models\Quote;
use App\Services\PolygonService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchLatestQuote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:fetch-latest {ticker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest stock quote for a specific ticker and store it in the database';

    protected $polygonService;


    public function __construct(PolygonService $polygonService)
    {
        parent::__construct();
        $this->polygonService = $polygonService;
    }
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ticker = $this->argument('ticker');

        $this->info("Fetching previous quote for ticker {$ticker}");

        try {
            $previousQuote = $this->polygonService->getPreviousQuote($ticker);

            if (isset($previousQuote['data'])) {
                $previousQuoteData = $previousQuote['data'][0];

                Quote::updateOrCreate(
                    [
                        'ticker' => $ticker,
                        'date' => Carbon::now()->format('Y-m-d')
                    ],
                    [
                        'open' => $previousQuoteData['o'],
                        'high' => $previousQuoteData['h'],
                        'low' => $previousQuoteData['l'],
                        'close' => $previousQuoteData['c'],
                        'volume' => $previousQuoteData['v']
                    ]
                );

                $this->info("Successfully saved previous quote for ticker {$ticker}.");
            } else {
                $this->error("No previous quote data found for ticker {$ticker}.");
            }
        } catch (\Exception $e) {
            $this->error("Failed to fetch previous quote: {$e->getMessage()}");
        }
    }
}
