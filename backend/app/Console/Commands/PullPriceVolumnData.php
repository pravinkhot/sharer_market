<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DateTime;
use DateInterval;
use DatePeriod;
use App\Helpers\DataProvider;
use App\Models\PriceVolumn as PriceVolumnModel;

class PullPriceVolumnData extends Command
{
    private $baseUrl = 'https://www.nseindia.com/content/historical/EQUITIES/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pullData:price-volumn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull data regarding price volumn.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $destinationDir = storage_path('share_market/price_volumn/');
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $lastPriceVolumnData = PriceVolumnModel::orderBy('id', 'desc')->first();

        $begin = new DateTime($lastPriceVolumnData->traded_at);
        $begin->add(new DateInterval('P1D'));
        $end = new DateTime('now');
        $begin->setTime(0,0); 
        $end->setTime(12,0);
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        echo 'Start Script: ' . date("Y-m-d H:i:s", time()) . PHP_EOL;

        foreach ($period as $dt) {
            $year = $dt->format('Y');
            $month = strtoupper($dt->format('M'));
            $day = $dt->format('d');

            $fileName = 'cm'.$day.$month.$year.'bhav.csv';
            $sourceLocationUrl = $this->baseUrl.$year.'/'.$month.'/'.$fileName.'.zip';
            $destinationLocationUrl = $destinationDir.$fileName.'.zip';
            DataProvider::putDataFromRemoteToLocal(
                $sourceLocationUrl,
                $destinationLocationUrl
            );

            $zipArchive = new \ZipArchive();
            if ($zipArchive->open($destinationLocationUrl)) {
                for ($i = 0; $i < $zipArchive->numFiles; $i++) {
                    if ($zipArchive->extractTo($destinationDir, array($zipArchive->getNameIndex($i)))) {
                        echo 'File extracted to ' . $destinationDir . $zipArchive->getNameIndex($i);
                    }
                }
                $zipArchive->close();
                unlink($destinationLocationUrl);
            }

            $csvFilePath = $destinationDir.$fileName;
            $csvFileData = @fopen($csvFilePath, 'r+');

            $result = [];
            if ($csvFileData) {
                while (($line = fgetcsv($csvFileData)) !== false) {
                    $result[] = [
                        'symbol' => $line[0],
                        'series' => $line[1],
                        'open' => $line[2],
                        'high' => $line[3],
                        'low' => $line[4],
                        'close' => $line[5],
                        'last' => $line[6],
                        'prev_close' => $line[7],
                        'total_traded_quantity' => $line[8],
                        'total_traded_value' => $line[9],
                        'traded_at' => '2019-10-01',
                        'total_trades' => $line[11],
                        'isin' => $line[12],
                        'created_at' => new DateTime('now'),
                        'updated_at' => new DateTime('now')
                    ];
                }
                fclose($csvFileData);

                array_shift($result);
                PriceVolumnModel::insert($result);
                unlink($csvFilePath);
            }
        }
        echo 'End Script: ' . date("Y-m-d H:i:s", time()) . PHP_EOL;
    }
}
