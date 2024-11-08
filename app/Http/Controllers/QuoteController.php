<?php

namespace App\Http\Controllers;

use App\Exceptions\PolygonApiException;
use App\Http\Requests\QuoteRequest;
use App\Models\Quote;
use App\Services\PolygonService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="Polygon Quotes API", version="1.0")
 */
class QuoteController extends Controller
{
    protected $polygonService;

    public function __construct(PolygonService $polygonService)
    {
        $this->polygonService = $polygonService;
    }

    /**
     * @OA\Get(
     *     path="/api/quotes",
     *     summary="Get quotes data",
     *     tags={"Quotes"},
     *      @OA\Parameter(
     *         name="ticker",
     *         in="query",
     *         description="The name of the ticker",
     *         @OA\Schema(
     *             type="string",
     *             default="NVDA"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Fixed date",
     *     ),
     *      @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date of the date range",
     *         @OA\Schema(
     *             type="string",
     *             example="2024-01-01"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date of the date range",
     *         @OA\Schema(
     *             type="string",
     *             example="2024-11-30"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Date period",
     *         @OA\Schema(
     *             type="string",
     *             example="year"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="null"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
            *                   property="id",
            *                   type="integer",
            *                   example=1
            *               ),
            *               @OA\Property(
            *                   property="ticker",
            *                   type="string",
            *                   example="NVDA"
            *               ),
            *               @OA\Property(
            *                   property="date",
            *                   type="string",
            *                   example="2022-11-02T00:00:00.000000Z"
            *               ),
            *               @OA\Property(
            *                   property="open",
            *                   type="integer",
            *                   example=13.85
            *               ),
            *               @OA\Property(
            *                   property="high",
            *                   type="integer",
            *                   example=14.21
            *               ),
            *               @OA\Property(
            *                   property="low",
            *                   type="integer",
            *                   example=13.21
            *               ),
            *               @OA\Property(
            *                   property="close",
            *                   type="integer",
            *                   example=13.22
            *               ),
            *               @OA\Property(
            *                   property="volume",
            *                   type="integer",
            *                   example=672627840
            *               ),
            *               @OA\Property(
            *                   property="created_at",
            *                   type="string",
            *                   example="2024-11-01T09:16:06.000000Z"
            *               ),
            *               @OA\Property(
            *                   property="updated_at",
            *                   type="string",
            *                   example="2024-11-01T09:16:06.000000Z"
            *               )
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
    public function index(Request $request) {
        $query = Quote::query();

        $ticker = $request->query('ticker', 'NVDA');

        if ($request->date) {
            $query->where('date', $request->date);
        } else if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } else if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        } else if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        } else if ($request->period) {
            if ($request->has('period')) {
                $period = $request->period;
                $today = Carbon::today();

                switch ($period) {
                    case 'day':
                        $query->where('date', '>=', $today->subDays(1));
                        break;
                    case 'month':
                        $query->where('date', '>=', $today->subMonth());
                        break;
                    case '6months':
                        $query->where('date', '>=', $today->subMonths(6));
                        break;
                    case 'year':
                        $query->where('date', '>=', $today->subYear());
                        break;
                }
            }
        }

        $quotes = $query->where('ticker', $ticker)->orderBy('date', 'asc')->get();

        return response()->json([
            'data'  => $quotes,
            'error' => null
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/polygon-quotes",
     *     summary="Get polygon quotes",
     *     tags={"Polygon Quotes"},
     *      @OA\Parameter(
     *         name="ticker",
     *         in="query",
     *         description="The name of the ticker",
     *         @OA\Schema(
     *             type="string",
     *             default="NVDA"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Date period",
     *         @OA\Schema(
     *             type="string",
     *             default="year"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     * @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="null"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                      @OA\Property(
     *                          property="t",
     *                          type="integer",
     *                          example=1672549200000
     *                      ),
     *                      @OA\Property(
     *                          property="vw",
     *                          type="integer",
     *                          example=36.4972
     *                      ),
     *                      @OA\Property(
     *                          property="o",
     *                          type="integer",
     *                          example=14.851
     *                      ),
     *                      @OA\Property(
     *                          property="n",
     *                          type="integer",
     *                          example=148640502
     *                      ),
     *                      @OA\Property(
     *                          property="h",
     *                          type="integer",
     *                          example=14.21
     *                      ),
     *                      @OA\Property(
     *                          property="l",
     *                          type="integer",
     *                          example=13.21
     *                      ),
     *                      @OA\Property(
     *                          property="c",
     *                          type="integer",
     *                          example=13.22
     *                      ),
     *                      @OA\Property(
     *                          property="v",
     *                          type="integer",
     *                          example=672627840
     *                      )
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
    public function getPolygonQuotes(QuoteRequest $request)
    {
        $ticker = $request->query('ticker');
        $period = $request->query('period', 'day');

        try {
            $quote = $this->polygonService->getQuotes($ticker, $period);

            return response()->json($quote);

        } catch (PolygonApiException $e) {
            return response()->json([
                'status' => $e->getStatusCode(),
                'error' => $e->getMessage(),
                'data' => $e->getError()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred',
                'data' => null
            ], 500);
        }
    }

    /** * @OA\Post(
     *     path="/api/quotes/history",
     *     summary="Get polygon quotes",
     *     tags={"Quotes"},
     *     @OA\Parameter(
     *         name="ticker",
     *         in="query",
     *         description="The name of the ticker",
     *         @OA\Schema(
     *             type="string",
     *             default="NVDA"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="years",
     *         in="query",
     *         description="Years count",
     *         @OA\Schema(
     *             type="integer",
     *             default="2"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="true"
     *             ),
     *         )
     *     )
     * )
     */
    public function fillQuotesHistory(QuoteRequest $request) {
        $ticker = $request->query('ticker');
        $years = $request->query('years', 2);

        $to = Carbon::now()->format('Y-m-d');
        $from = Carbon::now()->subYears($years)->format('Y-m-d');
        $period = 'day';

        try {
            $quotes = $this->polygonService->getQuotes($ticker, $period , $from, $to);

            if (isset($quotes['data'])) {
                foreach ($quotes['data'] as $quoteData) {
                    Quote::updateOrCreate(
                        [
                            'ticker' => $ticker,
                            'date' => Carbon::createFromTimestamp($quoteData['t'] / 1000)->format('Y-m-d')
                        ],
                        [
                            'open' => $quoteData['o'],
                            'high' => $quoteData['h'],
                            'low' => $quoteData['l'],
                            'close' => $quoteData['c'],
                            'volume' => $quoteData['v']
                        ]
                    );
                }
            }

            return response()->json(['status' => true]);

        } catch (\Exception $e) {
            return response()->json('Failed to fetch historical quotes', 500);
        }
    }
}