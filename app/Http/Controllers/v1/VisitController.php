<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\IcdNine;
use App\Models\IcdTen;
use App\Models\Recipe;
use App\Models\RequestLab;
use App\Models\RequestRad;
use App\Models\RequestRehab;
use App\Models\Soap;
use App\Models\Visit;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use stdClass;

class VisitController extends Controller
{
    /**
     * Function untuk permission ACL  
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:lihat kunjungan|ubah status kunjungan', ['only' => ['listing', 'edit', 'update']]);
        $this->middleware('permission:ubah status kunjungan', ['only' => ['update']]);
    }

    /**
     * Function untuk generate header API SIMRS
     *
     * @return void
     */
    public function headerListing()
    {
        $tgl = new DateTime();
        $plaintext = $tgl->getTimeStamp();
        $strkey = "Kolega2022";
        $enc = $this->stringEncrypt($strkey, $plaintext);
        return [
            'timestamp' => $this->stringDecrypt($strkey, $enc),
            'sign' => $enc
        ];
    }

    /**
     * Function untuk enkripsi header API SIMRS
     *
     * @param  mixed $key
     * @param  mixed $data
     * @return void
     */
    public function stringEncrypt($key, $data)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('SHA256', $key));
        $iv = substr(hex2bin(hash('SHA256', $key)), 0, 16);
        $output = openssl_encrypt($data, $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return base64_encode($output);
    }

    /**
     * Function untuk dekrisp header API SIMRS
     *
     * @param  mixed $key
     * @param  mixed $data
     * @return void
     */
    public function stringDecrypt($key, $data)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('SHA256', $key));
        $iv = substr(hex2bin(hash('SHA256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($data), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return $output;
    }
    
    /**
     * listing kunjungan
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $doctor = $request->dokter ? $request->dokter : '';
            $specialist = $request->spesialis ? $request->spesialis : '';
            $unit = $request->unit ? $request->unit : '';
            $ruang = $request->ruang ? $request->ruang : '';
            $status_visit = $request->status ? $request->status : '';
            $order_by = $request->order_by ? $request->order_by : 'tglawal';
            $order_dir = $request->order_dir ? $request->order_dir : 'desc';
            $header = $this->headerListing();
            $date_start = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $date_end = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            if ($request->periode) {
                $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?tglfrom=' . $date_start . '&tglto=' . $date_end . '&dokter=' . $doctor . '&unit=' . $unit . '&ruang=' . $ruang . '&rows=100000');
            } else {
                $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?dokter=' . $doctor . '&unit=' . $unit . '&ruang=' . $ruang . '&rows=100000');
            }

            if ($visits->status() != 200) {
                return response()->json(['message' => 'API SIMRS error!'], 500);
            }

            if (!isset($visits['msg'])) {
                $data = [];
                foreach ($visits->json() as $key => $visit) {
                    if(RequestLab::where('visit_id', $visit['kode'])->where('is_read_doc', 0)->count() > 0  || RequestRad::where('visit_id', $visit['kode'])->where('is_read_doc', 0)->count() > 0  || Recipe::where('visit_id', $visit['kode'])->where('is_read_doc', 0)->count() > 0) {
                        $is_read = 0;
                    } else {
                        $is_read = 1;
                    }
                    $status = Visit::where('visit_id', $visit['kode'])->first();
                    $visit['tglawal'] = Carbon::parse($visit['tglawal'])->format('d/m/Y H:i:s');
                    $visit['tgllahir'] = Carbon::parse($visit['tgllahir'])->format('d/m/Y');
                    $visit['is_read'] = $is_read;
                    if ($status) {
                        $visit['status'] = $status->visit_status;
                    } else {
                        $visit['status'] = 'waiting';
                    }
                    $data[] = $visit;
                }

                $dataCollec = collect($data);
                if ($request->spesialis) {
                    $dataCollec = $dataCollec->filter(function ($item) use ($specialist) {
                        return $item['spesialisasi'] == $specialist;
                    });
                }
                if ($request->status) {
                    $dataCollec = $dataCollec->filter(function ($item) use ($status_visit) {
                        return $item['status'] == $status_visit;
                    });
                }
                if ($request->search) {
                    $dataCollec = collect($dataCollec)->filter(function ($item) use ($request) {
                        // replace stristr with your choice of matching function
                        return false !== stristr($item['nama'], $request->search) ||
                            false !== stristr(strtolower($item['norm']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['kode']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['tgllahir']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['alamat']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['unit']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['ruang']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['spesialisasi']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['penjamin']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['dokter']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['tglawal']), strtolower($request->search)) ||
                            false !== stristr(strtolower($item['asalpasien']), strtolower($request->search));
                    });
                }
                if ($order_by && $order_dir == 'asc') {
                    $dataCollec = $dataCollec->sortBy($order_by);
                }
                if ($order_by && $order_dir == 'desc') {
                    $dataCollec = $dataCollec->sortByDesc($order_by);
                }
            } else {
                $dataCollec = collect();
            }

            $visitPaginate = (new CustomPaginator(
                $dataCollec->forPage($currentPage, $itemPerpage)->values(),
                $dataCollec->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL') . '/visit/listing');

            return response()->json($visitPaginate);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve visits.',
            ], 500);
        }
    }
    
    /**
     * detail data kunjungan
     *
     * @param  mixed $visitId
     * @return void
     */
    public function edit($visitId)
    {
        try {
            $header = $this->headerListing();

            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);

            if ($visits->status() != 200) {
                return response()->json(['message' => 'API SIMRS error!'], 500);
            }

            $visit = array_key_exists(0, $visits->json()) ? $visits[0] : new stdClass;
            $status = Visit::where('visit_id', $visit['kode'])->first();
            $visit['tglawal'] = formatDate($visit['tglawal'], 'd F Y \j\a\m H:i:s');
            $visit['umur'] = Carbon::parse($visit['tgllahir'])->diff(Carbon::now())->format('%y tahun %m bulan %d hari');
            $visit['tgllahir'] = formatDate($visit['tgllahir'], 'd F Y');
            $visit['tglsep'] = formatDate($visit['tglsep'], 'd F Y \j\a\m H:i:s');
            $visit['tglrujukan'] = formatDate($visit['tglrujukan'], 'd F Y');
            if ($status) {
                $visit['status'] = $status->visit_status;
            } else {
                $visit['status'] = 'waiting';
            }

            return response()->json($visit);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve visit.',
            ], 500);
        }
    }
    
    /**
     * update data kunjungan
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @return void
     */
    public function update(Request $request, $visitId)
    {
        try {
            $visit = Visit::where('visit_id', $visitId)->first();
            if ($visit) {
                $visit->update([
                    'visit_status' => $request->visit_status
                ]);
            }

            Visit::create([
                'visit_id' => $visitId,
                'visit_status' => $request->visit_status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Visit successfully updated.',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update visit.',
            ], 500);
        }
    }

    public function printHistory($visitId, Soap $soap, RequestRad $requestRad, Recipe $recipe, RequestRehab $requestRehab, IcdTen $icdTen, IcdNine $icdNine)
    {
        $html = '';
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $assesments = Soap::select(
                'sub_assesments.template_id'
            )
                ->leftJoin("assesments", "soaps.id", "=", "assesments.soap_id")
                ->leftJoin("sub_assesments", "assesments.id", "=", "sub_assesments.assesment_id")
                ->where('soaps.no_rm', $norm)
                ->latest()
                ->get();
            $radiologi = RequestRad::where('no_rm', $norm)->get();
            $rehab = RequestRehab::where('no_rm', $norm)->get();
            $recipe = Recipe::where('no_rm', $norm)->get();
            $icdnine = IcdNine::where('no_rm', $norm)->get();
            $icdten = IcdTen::where('no_rm', $norm)->get();
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed print history medic.',
            ], 500);
        }
    }
}
