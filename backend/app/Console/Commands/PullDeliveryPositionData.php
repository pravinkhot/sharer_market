<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DateTime;
use DateInterval;
use DatePeriod;
use App\Helpers\DataProvider;
use App\Models\DeliveryPosition as DeliveryPositionModel;

class PullDeliveryPositionData extends Command
{
    private $baseUrl = 'https://www1.nseindia.com/archives/equities/mto';

    private $priceVolumnBaseUrl = 'https://www1.nseindia.com/content/historical/EQUITIES/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pullData:delivery-position';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull data regarding delivery position.';

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
        $lastDeliveryPositionData = DeliveryPositionModel::orderBy('id', 'desc')->first();

        $begin = new DateTime($lastDeliveryPositionData->traded_at ?? '2020-01-01');
        $begin->add(new DateInterval('P1D'));
        $end = new DateTime('now');
        $begin->setTime(0,0);
        $end->setTime(12,0);
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        echo 'Start Script: ' . date("Y-m-d H:i:s", time()) . PHP_EOL;
        foreach ($period as $dt) {
            $result = [];

            //Pull delivery position data
            $deliveryPositionData = $this->getDeliveryPositionData($dt);

            //Pull price volume data
            $priceVolumnData = $this->getPriceVolumnData($dt);

            if ($deliveryPositionData['success'] === true || $priceVolumnData['success']  === true) {
                $tempData = [];
                foreach ($deliveryPositionData['data'] as $key => $value) {
                    if (isset($priceVolumnData['data'][$key])) {
                        $tempData[$key] = array_merge($value, $priceVolumnData['data'][$key]);
                    }
                }
                DeliveryPositionModel::insert($tempData);
            }
        }
        echo 'End Script: ' . date("Y-m-d H:i:s", time()) . PHP_EOL;
    }

    private function getDeliveryPositionData(DateTime $dt)
    {
        $result = [];
        $success = false;
        $fileData = DataProvider::pullDataFromRemote($this->baseUrl.'/MTO_'.$dt->format('dmY').'.DAT');
        if ($fileData !== false) {
            $explodeFileDataResult = explode("\n", utf8_encode($fileData));
            foreach ($explodeFileDataResult as $key => $value) {
                $explodeResult = explode(",", $value);
                if ($key >= 4 && !empty($explodeResult[2])) {
                    $result[$explodeResult[2]] = [
                        'symbol' => $explodeResult[2],
                        'series' => $explodeResult[3],
                        'traded_quantity' => $explodeResult[4],
                        'deliverable_quantity' => $explodeResult[5],
                        'deliverable_quantity_percentage' => $explodeResult[6],
                        'traded_at' => $dt->format('Y-m-d'),
                        'created_at' => new DateTime('now'),
                        'updated_at' => new DateTime('now')
                    ];
                }
            }
            $success = true;
        }
        return [
            'success' => $success,
            'data' => $result
        ];
    }

    private function getPriceVolumnData(DateTime $dt)
    {
        $destinationDir = storage_path('share_market/price_volumn/');
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $year = $dt->format('Y');
        $month = strtoupper($dt->format('M'));
        $day = $dt->format('d');

        $fileName = 'cm'.$day.$month.$year.'bhav.csv';
        $sourceLocationUrl = $this->priceVolumnBaseUrl.$year.'/'.$month.'/'.$fileName.'.zip';
        $destinationLocationUrl = $destinationDir.$fileName.'.zip';
        DataProvider::putDataFromRemoteToLocal(
            $sourceLocationUrl,
            $destinationLocationUrl
        );

        $zipArchive = new \ZipArchive();
        if ($zipArchive->open($destinationLocationUrl)) {
            for ($i = 0; $i < $zipArchive->numFiles; $i++) {
                if ($zipArchive->extractTo($destinationDir, array($zipArchive->getNameIndex($i)))) {
                    //echo 'File extracted to ' . $destinationDir . $zipArchive->getNameIndex($i);
                }
            }
            $zipArchive->close();
            unlink($destinationLocationUrl);
        }

        $csvFilePath = $destinationDir.$fileName;
        $csvFileData = @fopen($csvFilePath, 'r+');

        $result = [];
        $success = false;
        if ($csvFileData) {
            $success = true;
            while (($line = fgetcsv($csvFileData)) !== false) {
                $result[$line[0]] = [
                    'symbol' => $line[0],
                    'series' => $line[1],
                    'open_price' => $line[2],
                    'high_price' => $line[3],
                    'low_price' => $line[4],
                    'close_price' => $line[5],
                    'last_price' => $line[6],
                    'prev_close_price' => $line[7],
                    'traded_quantity' => $line[8],
                    'traded_value' => $line[9],
                    'total_trades' => $line[11],
                    'traded_at' => $dt->format('Y-m-d'),
                    'isin' => $line[12]
                ];
            }
            fclose($csvFileData);
            array_shift($result);
            unlink($csvFilePath);
        }

        return [
            'success' => $success,
            'data' => $result
        ];
    }
}
