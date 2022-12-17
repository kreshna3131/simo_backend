<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\RequestLab;
use App\Models\ResumeResult;
use App\Models\Soap;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ResumeResultController extends Controller
{
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
            $resumeResults = ResumeResult::query()
                ->where('no_rm', $visits[0]['norm'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('created_by', 'like', '%' . $request->search . '%')
                            ->orWhere('assesment_name', 'like', '%' . $request->search . '%')
                            ->orWhere('tindakan', 'like', '%' . $request->search . '%')
                            ->orWhere('info', 'like', '%' . $request->search . '%');
                        if ($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                        }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $resumeResultPaginate = (new CustomPaginator(
                $resumeResults->clone()->get(),
                $resumeResults->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL') . '/visit/' . $visitId . '/resume-result/listing');

            return response()->json($resumeResultPaginate->toArray() + [
                "unfilled_action_count" => $resumeResults->where('tindakan', null)->count()
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive integration result.',
            ], 500);
        }
    }

    /**
     * melihat data detail hasil resume
     *
     * @param  mixed $resumeResult
     * @return void
     */
    public function edit(ResumeResult $resumeResult)
    {
        try {
            return response()->json($resumeResult);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve resume result'
            ], 500);
        }
    }

    /**
     * update data tindakan hasil resume
     *
     * @param  mixed $request
     * @param  mixed $resumeResult
     * @return void
     */
    public function update(Request $request, ResumeResult $resumeResult)
    {
        $data_validated = $request->validate(['tindakan' => ['required']]);
        try {
            $resumeResult->update($data_validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Resume result successfully updated'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update resume result'
            ], 500);
        }
    }
    
    /**
     * print PDF resume rawat jalan
     *
     * @param  mixed $visitId
     * @return void
     */
    public function printPdf($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $resumes = ResumeResult::where('no_rm', $norm)->get();

            foreach ($resumes as $key => $resume) {
                $soap_id = explode(" ", $resume->info);
                $soap = Soap::find($soap_id[4]);

                // info($resultLab);
                $laboratorium = collect();
                if ($resume->laboratorium_id) {
                    $laboratorium = RequestLab::find($resume->laboratorium_id);
                }

                $view = view('pdf.resume-result', [
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'resume' => $resume,
                    'laboratorium' => $laboratorium,
                    'soap' => $soap
                ]);
                $html .= $view->render();
            }
            $pdf = PDF::loadHTML($html);

            return $pdf->download('Resume Rawat Jalan.pdf');
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to download resume result'
            ], 500);
        }
    }

    /**
     * print PDF resume rawat jalan
     *
     * @param  mixed $visitId
     * @return void
     */
    public function previewPdf($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $resumes = ResumeResult::where('no_rm', $norm)->get();

            foreach ($resumes as $key => $resume) {
                $soap_id = explode(" ", $resume->info);
                $soap = Soap::find($soap_id[4]);

                // info($resultLab);
                $laboratorium = collect();
                if ($resume->laboratorium_id) {
                    $laboratorium = RequestLab::find($resume->laboratorium_id);
                }

                $view = view('pdf.resume-result', [
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'resume' => $resume,
                    'laboratorium' => $laboratorium,
                    'soap' => $soap
                ]);
                $html .= $view->render();
            }
            $pdf = PDF::loadHTML($html);

            return $pdf->stream();
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to download resume result'
            ], 500);
        }
    }
}
