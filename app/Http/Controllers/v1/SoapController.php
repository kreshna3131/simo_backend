<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdultAssesment;
use App\Models\Assesment;
use App\Models\Attribute;
use App\Models\CovidAssesment;
use App\Models\IntegrationResult;
use App\Models\KidsAssesment;
use App\Models\RequestLab;
use App\Models\ResumeResult;
use App\Models\Soap;
use App\Models\SoulDoctorAssesment;
use App\Models\SubAssesment;
use App\Models\Template;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SoapController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:lihat kunjungan', ['only' => ['listing', 'store', 'update']]);
        $this->middleware('permission:lihat histori rekam medis', ['only' => ['printHistorySoap', 'previewHistorySoap']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Function untuk melihat data listing soap
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @return void
     */
    public function listing(Request $request, $visitId)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $soaps = Soap::query()
                ->where('soaps.visit_id', $visitId)
                ->where(function ($query) use ($request) {
                    $query->when($request->filled('search'), function ($query) use ($request) {
                        $date = searchDate($request->search, 'd F Y', 'Y-m-d');
                        $query->where('status', 'like', '%' . $request->search . '%');
                        $query->orWhere('assesment', 'like', '%' . $request->search . '%');
                        $query->orWhere('created_by', 'like', '%' . $request->search . '%');
                        if ($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                            $query->orWhere('updated_at', 'like', '%' . $date . '%');
                        }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');


            $soapPaginate = (new CustomPaginator(
                $soaps->clone()->forPage($currentPage, $itemPerpage)->get(),
                $soaps->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL') . '/soap/listing');

            return response()->json($soapPaginate);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve soaps.',
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * listing Template
     *
     * @return void
     */
    public function listingTemplate($visitId)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);

            $soapCovid = Soap::where('visit_id', $visitId)->get();
            $covidCount = 0;
            foreach ($soapCovid as $key => $action) {
                $covidCount = $covidCount + $action->assesments->where('type', 'Covid')->count();
            }

            $soapGlobal = Soap::where('visit_id', $visitId)->get();
            $globalCount = 0;
            foreach ($soapGlobal as $key => $action) {
                $globalCount = $globalCount + $action->assesments->where('type', 'Global')->count();
            }

            info($this->checkAssesment($visits[0]['norm'], 'Global') == null);

            $data = [];

            $row['id'] = 1;
            $row['type'] = 'Assesmen Covid';
            $row['required'] = 'true';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Covid') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 2;
            $row['type'] = 'Assesmen Umum Dewasa';
            $row['required'] = 'false';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Umum Dewasa') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 3;
            $row['type'] = 'Assesmen Umum Anak';
            $row['required'] = 'false';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Umum Anak') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 4;
            $row['type'] = 'Assesmen Spesialis Anak';
            $row['required'] = 'false';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Spesialis Anak') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 5;
            $row['type'] = 'Assesmen Spesialis Penyakit Dalam';
            $row['required'] = 'false';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Spesialis Penyakit Dalam') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 6;
            $row['type'] = 'Assesmen Global';
            $row['required'] = 'true';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Global') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 7;
            $row['type'] = 'Assesmen Spesialis Syaraf';
            $row['required'] = 'false';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Spesialis Syaraf') == null ? '1' : '0';
            $data[] = $row;

            $row['id'] = 8;
            $row['type'] = 'Assesmen Spesialis Paru';
            $row['required'] = 'false';
            $row['is_new'] = $this->checkAssesment($visits[0]['norm'], 'Spesialis Paru') == null ? '1' : '0';
            $data[] = $row;

            $templates = collect($data);
            $arrayTemplates = [];
            info($this->checkDuplicate($visitId, 'Spesialis'));

            if (auth()->user()->can('tambah assesment covid') || auth()->user()->can('tambah assesment dewasa') || auth()->user()->can('tambah assesment anak') || auth()->user()->can('tambah assesment spesialis penyakit dalam') || auth()->user()->can('tambah assesment spesialis anak') || auth()->user()->can('tambah assesment spesialis syaraf') || auth()->user()->can('tambah assesment spesialis paru')) {

                if ($covidCount == 0 && auth()->user()->can('tambah assesment covid')) {
                    $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Covid');
                }

                if ($globalCount == 0 && $covidCount > 0 && auth()->user()->can('tambah assesment global')) {
                    $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Global');
                }

                if ($globalCount > 0) {
                    if (auth()->user()->can('tambah assesment dewasa') && $this->checkDuplicate($visitId, 'Umum') == null) {
                        $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Umum Dewasa');
                    }
                    if (auth()->user()->can('tambah assesment anak') && $this->checkDuplicate($visitId, 'Umum') == null) {
                        $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Umum Anak');
                    }
                    if (auth()->user()->can('tambah assesment spesialis anak') && $this->checkDuplicate($visitId, 'Spesialis') == null) {
                        $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Spesialis Anak');
                    }
                    if (auth()->user()->can('tambah assesment spesialis penyakit dalam') && $this->checkDuplicate($visitId, 'Spesialis') == null) {
                        $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Spesialis Penyakit Dalam');
                    }
                    if (auth()->user()->can('tambah assesment spesialis syaraf') && $this->checkDuplicate($visitId, 'Spesialis') == null) {
                        $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Spesialis Syaraf');
                    }
                    if (auth()->user()->can('tambah assesment spesialis paru') && $this->checkDuplicate($visitId, 'Spesialis') == null) {
                        $arrayTemplates[] = $templates->firstWhere('type', 'Assesmen Spesialis Paru');
                    }
                }
            }

            return response()->json($arrayTemplates);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve templates'
            ], 500);
        }
    }
    
    public function checkAssesment($noRM, $type)
    {
        $soap = Soap::select(
            'sub_assesments.*'
        )
            ->leftJoin("assesments", "soaps.id", "=", "assesments.soap_id")
            ->leftJoin("sub_assesments", "assesments.id", "=", "sub_assesments.assesment_id")
            ->where('soaps.no_rm', $noRM)
            ->where('assesments.type', $type)
            ->where('assesments.status', 'done')
            ->latest()
            ->first();

        return $soap;
    }

    public function checkDuplicate($visitId, $type)
    {
        $soap = Soap::select(
            'sub_assesments.*'
        )
            ->leftJoin("assesments", "soaps.id", "=", "assesments.soap_id")
            ->leftJoin("sub_assesments", "assesments.id", "=", "sub_assesments.assesment_id")
            ->where('soaps.visit_id', $visitId)
            ->where('assesments.type','like', '%' . $type . '%')
            ->where('assesments.status', 'done')
            ->latest()
            ->first();

        return $soap;
    }

    /**
     * Cek button apakah membuat covid, global atau assesment
     *
     * @param  mixed $visitId
     * @return void
     */
    public function checkButton($visitId)
    {
        try {
            $covid = Soap::where('visit_id', $visitId)->get();
            $covidCount = 0;
            $covidDone = 0;
            foreach ($covid as $key => $action) {
                $covidCount = $covidCount + $action->assesments->where('type', 'Covid')->count();
                $covidDone = $covidDone + $action->assesments->where('type', 'Covid')->where('status', 'done')->count();
            }

            $global = Soap::where('visit_id', $visitId)->get();
            $globalCount = 0;
            $globalDone = 0;
            foreach ($global as $key => $action) {
                $globalCount = $globalCount + $action->assesments->where('type', 'Global')->count();
                $globalDone = $globalDone + $action->assesments->where('type', 'Global')->where('status', 'done')->count();
            }

            $umumDone = $this->checkDuplicate($visitId, 'Umum');
            $spesialisDone = $this->checkDuplicate($visitId, 'Spesialis');

            if ($covidCount == 0 || $covidDone == 0) {
                $action = 'covid';
            } else if ($globalCount == 0 || $globalDone == 0) {
                $action = 'global';
            } else {
                $action = 'assesment';
            }

            if ($covidCount > 0 && $covidDone == 0 ||  $globalCount > 0 && $globalDone == 0 || $umumDone != null && $spesialisDone != null) {
                $is_create = 1;
            } else {
                $is_create = 0;
            }

            return response()->json(['action' => $action, 'is_create' => $is_create]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve check button'
            ], 500);
        }
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
     * Store a newly created resource in storage.
     * SOAP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $visitId)
    {
        $validated_data = $request->validate(['templates' => ['required', 'array']]);
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $soap_number = Soap::where('visit_id', $visitId)->get()->count();
            if(Soap::where('visit_id', $visitId)->count() == 0) {
                $visit_count = Soap::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = Soap::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }

            if ($request->templates[0]["id"] == 6) {
                $covid = SubAssesment::whereHas('assesment', function ($query) use ($visitId) {
                    $query->where('type', 'Covid')
                        ->whereHas('soap', function ($query) use ($visitId) {
                            $query->where('visit_id', $visitId);
                        });
                })->first();

                $soap = Soap::where('visit_id', $visitId)->first();

                $soap->update(['assesment' => 'Covid, Global', 'status' => 'progress']);

                $assesmentData = Assesment::create([
                    'soap_id' => $soap->id,
                    'type' => Template::find(6)->type,
                    'name' => Template::find(6)->name,
                    'user_id' => auth()->user()->id,
                    'status' => 'progress'
                ]);

                if ($request->templates[0]["is_new"] == 0) {
                    $is_new = $this->checkAssesment($visits[0]['norm'], 'Global');

                    $SubAssesment = SubAssesment::create(collect($is_new)->except(['id', 'is_disabled', 'assesments'])->toArray());
                    $SubAssesment->update([
                        'template_id' => 6,
                        'assesment_id' => $assesmentData->id,
                        'created_by' => auth()->user()->name,
                        'updated_by' => null
                    ]);
                } else {
                    $SubAssesment = $covid->replicate();
                    $SubAssesment->save();
                    $SubAssesment->update([
                        'template_id' => 6,
                        'assesment_id' => $assesmentData->id,
                        'created_by' => auth()->user()->name,
                        'updated_by' => null
                    ]);
                }

                ActivityLog::create([
                    'user_name' => auth()->user()->name,
                    'user_role' => auth()->user()->roles->first()->name,
                    'unique_id' => $soap->id,
                    'visit_id' => $visitId,
                    'note' => 'Membuat assesmen ' . Template::find($request->templates[0]["id"])->name . '  untuk soap ke ' . $soap->soap_number . '',
                    'type' => 'Soap',
                    'action' => 'Membuat'
                ]);
            } else {
                $assesment_type = [];
                $soap = Soap::create([
                    'no_rm' => $norm,
                    'visit_id' => $visitId,
                    'created_by' => auth()->user()->name,
                    'soap_number' => $soap_number + 1,
                    'visit_number' => $visit_count,
                    'status' => 'progress',
                    'visibility' => 1
                ]);

                foreach ($request->templates as $key => $template) {
                    $assesment_type[] = Template::find($template["id"])->type;
                    $assesmentData = Assesment::create([
                        'soap_id' => $soap->id,
                        'type' => Template::find($template["id"])->type,
                        'name' => Template::find($template["id"])->name,
                        'user_id' => auth()->user()->id,
                        'status' => 'progress'
                    ]);
                    if ($template["is_new"] == 1) {
                        if($template["id"] != 1) {
                            $global = SubAssesment::whereHas('assesment', function ($query) use ($visitId) {
                                $query->where('type', 'Global')
                                    ->whereHas('soap', function ($query) use ($visitId) {
                                        $query->where('visit_id', $visitId);
                                    });
                            })->first();

                            $SubAssesment = $global->replicate();
                            $SubAssesment->save();
                            $SubAssesment->update([
                                'template_id' => $template["id"],
                                'assesment_id' => $assesmentData->id,
                                'created_by' => auth()->user()->name,
                                'updated_by' => null
                            ]);
                        } else {
                            $SubAssesment = SubAssesment::create([
                                'template_id' => $template["id"],
                                'assesment_id' => $assesmentData->id,
                                'created_by' => auth()->user()->name,
                                'updated_by' => null
                            ]);
                        }

                    } else {
                        $is_new = $this->checkAssesment($visits[0]['norm'], Template::find($template["id"])->type);

                        $SubAssesment = SubAssesment::create(collect($is_new)->except(['id', 'is_disabled', 'assesments'])->toArray());
                        $SubAssesment->update([
                            'template_id' => $template["id"],
                            'assesment_id' => $assesmentData->id,
                            'created_by' => auth()->user()->name,
                            'updated_by' => null
                        ]);
                    }
                }
                $soap->update([
                    'assesment' => implode(', ', $assesment_type),
                ]);

                ActivityLog::create([
                    'user_name' => auth()->user()->name,
                    'user_role' => auth()->user()->roles->first()->name,
                    'unique_id' => $soap->id,
                    'visit_id' => $visitId,
                    'note' => 'Membuat soap untuk assesmen ' . implode(', ', $assesment_type),
                    'type' => 'Soap',
                    'action' => 'Membuat'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Soap sucessfully added.',
                'data' => [
                    'soap_id' => $soap->id,
                    'assesment_id' => $assesmentData->id,
                    'sub_assesment_id' => $SubAssesment->id
                ]
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add soap.',
            ], 500);
        }
    }

    /**
     * Untuk Menambahkan assesment global
     *
     * @param  mixed $visitId
     * @return void
     */
    public function storeGlobal($visitId)
    {
        try {
            $covid = SubAssesment::whereHas('assesment', function ($query) use ($visitId) {
                $query->where('type', 'Covid')
                    ->whereHas('soap', function ($query) use ($visitId) {
                        $query->where('visit_id', $visitId);
                    });
            })->first();

            $soapData = Soap::where('visit_id', $visitId)->first();

            $soapData->update(['assesment' => 'Covid, Global']);

            $assesmentData = Assesment::create([
                'soap_id' => $soapData->id,
                'type' => Template::find(6)->type,
                'name' => Template::find(6)->name,
                'user_id' => auth()->user()->id,
                'status' => 'progress'
            ]);

            $SubAssesment = $covid->replicate();
            $SubAssesment->save();
            $SubAssesment->update([
                'template_id' => 6,
                'assesment_id' => $assesmentData->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Soap sucessfully added.',
                'data' => [
                    'soap_id' => $soapData->id,
                    'assesment_id' => $assesmentData->id,
                    'sub_assesment_id' => $SubAssesment->id
                ]
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    /**
     * print history rekam media soap
     *
     * @param  mixed $visitId
     * @return void
     */
    public function printHistorySoap($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $assesments = Soap::select(
                'soaps.id as soap_id',
                'sub_assesments.*'
            )
                ->leftJoin("assesments", "soaps.id", "=", "assesments.soap_id")
                ->leftJoin("sub_assesments", "assesments.id", "=", "sub_assesments.assesment_id")
                ->where('soaps.no_rm', $norm)
                ->latest()
                ->get();
            $integrations = IntegrationResult::where('no_rm', $norm)->get();
            $resumes = ResumeResult::where('no_rm', $norm)->get();

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

            foreach ($assesments as $key => $assesment) {
                $assesment_data = SubAssesment::find($assesment->id);
                $soap = Soap::find($assesment->soap_id);
                $attributes = Attribute::select(
                    'attribute_template.id',
                    'attributes.type',
                    'attributes.label',
                    'attributes.name',
                    'attribute_template.group_name',
                    'attributes.info',
                )
                    ->leftJoin("attribute_template", "attributes.id", "=", "attribute_template.attribute_id")
                    ->leftJoin("templates", "attribute_template.template_id", "=", "templates.id")
                    ->where('templates.id', $assesment->template_id)
                    ->where('attribute_template.status', 1)
                    ->groupBy('attributes.id')
                    ->orderBy('attribute_template.group_id', 'ASC')
                    ->orderBy('attribute_template.id', 'ASC')
                    ->get();
        
                // info($resultLab);
                $laboratorium = collect();
                $resultLab = collect();
                if ($assesment->laboratorium_id) {
                    $laboratorium = RequestLab::find($assesment->laboratorium_id);
        
                    //X-time
                    date_default_timezone_set('UTC'); 
                    $tStamp = strval(time()-strtotime('1970-01-01 00:00:00')); 
            
                    //X-sign
                    $xcons = "testtesttest";
                    $xkey = "secretkey";
                    $signature = hash_hmac('sha256', $xcons, $xkey, true);
                    $encodedSignature = base64_encode($signature);
            
                    $result = Http::withoutVerifying()->withHeaders(['X-cons' => '330913001', 'X-time' => $tStamp, 'X-sign' => $encodedSignature, 'Accept' => 'application/json'])->get(''.env('LIS_URL').'/api/v1/getResult/json?no_laboratorium=' . $laboratorium->laboratorium_id);
        
                    $resultLab = $result;
                }
        
                $view = view('pdf.assesment', [
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'attributes' => $attributes->groupBy('group_name'),
                    'assesments' => $assesment_data,
                    'resultLab' => $resultLab,
                    'laboratorium' => $laboratorium,
                    'soap' => $soap
                ]);
                $html .= $view->render();
            }
            $pdf = PDF::loadHTML($html);

            return $pdf->download('Histori Rekam Medis SOAP.pdf');
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download history soap.',
            ], 500);
        }
    }

    /**
     * preview history rekam media soap
     *
     * @param  mixed $visitId
     * @return void
     */
    public function previewHistorySoap($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $assesments = Soap::select(
                'soaps.id as soap_id',
                'sub_assesments.*'
            )
                ->leftJoin("assesments", "soaps.id", "=", "assesments.soap_id")
                ->leftJoin("sub_assesments", "assesments.id", "=", "sub_assesments.assesment_id")
                ->where('soaps.no_rm', $norm)
                ->latest()
                ->get();
            $integrations = IntegrationResult::where('no_rm', $norm)->get();
            $resumes = ResumeResult::where('no_rm', $norm)->get();

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

            foreach ($resumes as $key => $resume) {
                // info($resultLab);
                $soap_id = explode(" ", $resume->info);
                $soap = Soap::find($soap_id[4]);
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

            foreach ($assesments as $key => $assesment) {
                $assesment_data = SubAssesment::find($assesment->id);
                $soap = Soap::find($assesment->soap_id);
                $attributes = Attribute::select(
                    'attribute_template.id',
                    'attributes.type',
                    'attributes.label',
                    'attributes.name',
                    'attribute_template.group_name',
                    'attributes.info',
                )
                    ->leftJoin("attribute_template", "attributes.id", "=", "attribute_template.attribute_id")
                    ->leftJoin("templates", "attribute_template.template_id", "=", "templates.id")
                    ->where('templates.id', $assesment->template_id)
                    ->where('attribute_template.status', 1)
                    ->groupBy('attributes.id')
                    ->orderBy('attribute_template.group_id', 'ASC')
                    ->orderBy('attribute_template.id', 'ASC')
                    ->get();
        
                // info($resultLab);
                $laboratorium = collect();
                $resultLab = collect();
                if ($assesment->laboratorium_id) {
                    $laboratorium = RequestLab::find($assesment->laboratorium_id);
        
                    //X-time
                    date_default_timezone_set('UTC'); 
                    $tStamp = strval(time()-strtotime('1970-01-01 00:00:00')); 
            
                    //X-sign
                    $xcons = "testtesttest";
                    $xkey = "secretkey";
                    $signature = hash_hmac('sha256', $xcons, $xkey, true);
                    $encodedSignature = base64_encode($signature);
            
                    $result = Http::withoutVerifying()->withHeaders(['X-cons' => '330913001', 'X-time' => $tStamp, 'X-sign' => $encodedSignature, 'Accept' => 'application/json'])->get(''.env('LIS_URL').'/api/v1/getResult/json?no_laboratorium=' . $laboratorium->laboratorium_id);
        
                    $resultLab = $result;
                }
        
                $view = view('pdf.assesment', [
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'attributes' => $attributes->groupBy('group_name'),
                    'assesments' => $assesment_data,
                    'resultLab' => $resultLab,
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
                'status' => 'failed',
                'message' => 'Failed download history soap.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function show(Soap $soap)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * SOAP
     *
     * @param  \App\Models\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function edit($visitId, Soap $soap)
    {
        try {
            return response()->json($soap);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve soap.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Soap $soap)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Soap  $soap
     * @return \Illuminate\Http\Response
     */
    public function destroy(Soap $soap)
    {
        //
    }
}
