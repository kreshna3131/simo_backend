<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Custom\Pagination\CustomPaginator;
use App\Http\Requests\IntegrationResultRequest;
use App\Models\IntegrationResult;
use App\Models\Soap;
use App\Models\SubAssesment;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;

class IntegrationResultController extends Controller
{
    /**
     * Function permission
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:lihat assesment hasil terintegrasi|tambah assesment hasil terintegrasi|ubah assesment hasil terintegrasi', ['only' => ['listing', 'store', 'update']]);
        $this->middleware('permission:lihat assesment hasil terintegrasi', ['only' => ['listing']]);
        $this->middleware('permission:tambah assesment hasil terintegrasi', ['only' => ['store']]);
        $this->middleware('permission:ubah assesment hasil terintegrasi', ['only' => ['update']]);
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

    public function listing(Request $request, $visitId)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $integrationResult = IntegrationResult::query()
                ->where('no_rm', $visits[0]['norm'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('created_by', 'like', '%' . $request->search . '%')
                            ->orWhere('created_role', 'like', '%' . $request->search . '%')
                            ->orWhere('assesment_name', 'like', '%' . $request->search . '%')
                            ->orWhere('info', 'like', '%' . $request->search . '%');
                        if ($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                            $query->orWhere('updated_at', 'like', '%' . $date . '%');
                        }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $integrationResultPaginate = (new CustomPaginator(
                $integrationResult->clone()->forPage($currentPage, $itemPerPage)->get(),
                $integrationResult->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL') . '/visit/' . $visitId . '/integration-result/listing');

            return response()->json($integrationResultPaginate);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive integration result.',
            ], 500);
        }
    }

    /**
     * Menambahkan hasil terintegrasi
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @return void
     */
    public function store(IntegrationResultRequest $request, $visitId)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            IntegrationResult::create(collect($request->validated())->except('rencana_tindak_lanjut')->toArray() + [
                'visit_id' => $visitId,
                'no_rm' => $norm,
                'user_id' => auth()->user()->id,
                'rencana_tindak_lanjut' => json_encode($request->rencana_tindak_lanjut),
                'info' => 'Non Integrasi',
                'assesment_name' => 'Non Integrasi',
                'created_by' => auth()->user()->name,
                'created_role' => auth()->user()->roles->first()->name
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Integration result successfully added.'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add integration result'
            ], 500);
        }
    }

    /**
     * Melihat detail data hasil terintegrasi
     *
     * @param  mixed $integrationResult
     * @return void
     */
    public function edit(IntegrationResult $integrationResult)
    {
        try {
            return response()->json($integrationResult);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve integration result'
            ], 500);
        }
    }

    /**
     * update data hasil terintegrasi
     *
     * @param  mixed $request
     * @param  mixed $integrationResult
     * @return void
     */
    public function update(IntegrationResultRequest $request, IntegrationResult $integrationResult)
    {
        try {
            $integrationResult->update(collect($request->validated())->except('integration', 'rencana_tindak_lanjut')->toArray() + [
                'rencana_tindak_lanjut' => json_encode($request->rencana_tindak_lanjut)
            ]);

            if ($integrationResult->sub_assesment_id) {
                SubAssesment::find($integrationResult->sub_assesment_id)->update(collect($request->validated())->except('integration', 'rencana_tindak_lanjut')->toArray() + [
                    'rencana_tindak_lanjut' => json_encode($request->rencana_tindak_lanjut)
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Integration result successfully updated.'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update integration result'
            ], 500);
        }
    }

    /**
     * update status hasil terintegrasi
     *
     * @param  mixed $request
     * @param  mixed $integrationResult
     * @return void
     */
    public function updateStatus(Request $request, IntegrationResult $integrationResult)
    {
        $data_validated = $request->validate(['status' => 'required']);
        try {
            $integrationResult->update($data_validated);
            if($integrationResult->sub_assesment_id) {
                $integrationResult->subAssesment->assesment->update($data_validated);
                
                $soap_id = explode(" ", $integrationResult->info);
                $soap = Soap::find($soap_id[4]);

                $dataAssesment = [];
                foreach ($soap->assesments as $key => $assesments) {
                    $dataAssesment[] = $assesments->status;
                }
                if (in_array('progress', $dataAssesment)) {
                    $soap->update([
                        'status' => 'progress'
                    ]);
                } else if (count(array_unique($dataAssesment)) === 1 && end($dataAssesment) === 'cancel') {
                    $soap->update([
                        'status' => 'cancel'
                    ]);
                } else {
                    $soap->update([
                        'status' => 'done'
                    ]);
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Integration result successfully updated.'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update integration result'
            ], 500);
        }
    }
   
    /**
     * print PDF hasil perawatan rawat jalan
     *
     * @param  mixed $integrationResult
     * @return void
     */
    public function printPDF(IntegrationResult $integrationResult)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $integrationResult->visit_id);

            if($integrationResult->sub_assesment_id) {
                $soap_id = explode(" ", $integrationResult->info);
                $soap = Soap::find($soap_id[4]);
            } else {
                $soap = "Integration";
            }

            $pdf = Pdf::loadView('pdf.integration-result', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'integration' => $integrationResult,
                'soap' => $soap
            ]);

            return $pdf->download('Hasil Pemeriksaan Rawat Jalan Terintegrasi.pdf');
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to download integration result'
            ], 500);
        }
    }

    /**
     * print PDF resume rawat jalan
     *
     * @param  mixed $visitId
     * @return void
     */
    public function printPdfAll($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $integrations = IntegrationResult::where('no_rm', $norm)->get();

            foreach ($integrations as $key => $integration) {
                if($integration->sub_assesment_id) {
                    $soap_id = explode(" ", $integration->info);
                    $soap = Soap::find($soap_id[4]);
                } else {
                    $soap = "Integration";
                }
                $view = view('pdf.integration-result')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'integration' => $integration,
                    'soap' => $soap
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->download('Hasil Pemeriksaan Rawat Jalan Terintegrasi.pdf');
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to download integration result'
            ], 500);
        }
    }
}
