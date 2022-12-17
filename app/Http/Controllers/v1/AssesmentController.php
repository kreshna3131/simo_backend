<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assesment;
use App\Models\Soap;
use App\Models\SubAssesment;
use App\Models\Template;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssesmentController extends Controller
{
    /**
     * listing assesment
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @param  mixed $soap
     * @return void
     */
    public function listing(Request $request, $visitId, Soap $soap)
    {
        try {
            $data = [];
            info(auth()->user()->can('lihat deteksi dini kewaspadaan terhadap COVID 19'));
            $assesments = Assesment::where('soap_id', $soap->id)
                ->where(function ($query) {
                    $query->when(auth()->user()->can('lihat deteksi dini kewaspadaan terhadap COVID 19'), function ($template) {
                        $template->where(function ($template) {
                            return $template->where('name', 'Deteksi Dini Kewaspadaan Terhadap COVID 19');
                        });
                    })
                        ->when(auth()->user()->can('lihat assesmen awal keperawatan rawat jalan'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Awal Keperawatan Rawat Jalan');
                            });
                        })
                        ->when(auth()->user()->can('lihat assesmen awal keperawatan pasien anak rawat jalan'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Awal Keperawatan Pasien Anak Rawat Jalan');
                            });
                        })
                        ->when(auth()->user()->can('lihat assesmen awal medis anak'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Awal Medis Anak');
                            });
                        })
                        ->when(auth()->user()->can('lihat assesmen awal medis penyakit dalam'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Awal Medis Penyakit Dalam');
                            });
                        })
                        ->when(auth()->user()->can('lihat assesmen global'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Global');
                            });
                        })
                        ->when(auth()->user()->can('lihat assesmen awal medis syaraf'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Awal Medis Syaraf');
                            });
                        })
                        ->when(auth()->user()->can('lihat assesmen awal medis paru'), function ($template) {
                            $template->orWhere(function ($template) {
                                return $template->where('name', 'Assesment Awal Medis Paru');
                            });
                        })
                        ->when(!auth()->user()->hasAnyPermission(['lihat deteksi dini kewaspadaan terhadap COVID 19', 'lihat assesmen awal keperawatan rawat jalan', 'lihat assesmen awal keperawatan pasien anak rawat jalan', 'lihat assesmen awal medis spesialis anak', 'lihat assesmen awal medis spesialis penyakit dalam', 'lihat assesmen awal medis spesialis syaraf', 'lihat assesmen awal medis spesialis paru']), function ($template) {
                            return $template->where('name', 'no-data');
                        });
                })
                ->get();

            foreach ($assesments as $key => $assesment) {
                $row = [];
                $row['id']  = $assesment->id;
                $row['type']  = $assesment->type;
                $row['title']  = $assesment->name;
                $row['document']  = '1 Sub dokumen';
                // if ($assesment->type == 'Covid' || $assesment->type == 'Spesialis Penyakit Dalam') {
                // } else if ($assesment->type == 'Umum Dewasa') {
                //     $row['document']  = '3 Sub dokumen';
                // } else {
                //     $row['document']  = '2 Sub dokumen';
                // }
                $row['user']  = $assesment->user_id ? $assesment->user->name : '-';
                $row['role']  = $assesment->user_id ? $assesment->user->roles->first()->name : '-';
                $row['allow'] = $assesment->allow;
                $row['status']  = $assesment->status;
                $row['created_at']  = $assesment->created_at;
                $row['updated_at']  = $assesment->updated_at;
                $data[] = $row;
            }

            $dataCollec = collect($data);

            if ($request->search) {
                $dataCollec = collect($dataCollec)->filter(function ($item) use ($request) {
                    // replace stristr with your choice of matching function
                    return false !== stristr($item['type'], $request->search) ||
                        false !== stristr(strtolower($item['title']), strtolower($request->search)) ||
                        false !== stristr(strtolower($item['document']), strtolower($request->search)) ||
                        false !== stristr(strtolower($item['user']), strtolower($request->search)) ||
                        false !== stristr(strtolower($item['role']), strtolower($request->search)) ||
                        false !== stristr(strtolower($item['created_at']), strtolower($request->search)) ||
                        false !== stristr(strtolower($item['updated_at']), strtolower($request->search));
                });
            }

            if ($request->order_by && $request->order_dir == 'asc') {
                $dataCollec = $dataCollec->sortBy($request->order_by);
            }
            if ($request->order_by && $request->order_dir == 'desc') {
                $dataCollec = $dataCollec->sortByDesc($request->order_by);
            }

            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);

            $dataPaginate = (new CustomPaginator(
                $dataCollec->forPage($currentPage, $itemPerpage)->values(),
                $dataCollec->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL') . '/soap/' . $soap->id . '/assesment/listing');

            return response()->json($dataPaginate);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve soap.',
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
     * Menambahkan assesment
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @param  mixed $soap
     * @return void
     */
    public function store(Request $request, $visitId, Soap $soap)
    {
        $request->validate(['templates' => ['required']]);
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $template_type = [];
            foreach ($request->templates as $key => $template) {
                $template_type[] = Template::find($template['id'])->type;
                if ($request->templates[0]["id"] == 6) {
                    $covid = SubAssesment::whereHas('assesment', function ($query) use ($visitId) {
                        $query->where('type', 'Covid')
                            ->whereHas('soap', function ($query) use ($visitId) {
                                $query->where('visit_id', $visitId);
                            });
                    })->first();
    
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
    
                    // ActivityLog::create([
                    //     'user_name' => auth()->user()->name,
                    //     'user_role' => auth()->user()->roles->first()->name,
                    //     'unique_id' => $soap->id,
                    //     'visit_id' => $visitId,
                    //     'note' => 'Membuat assesmen ' . Template::find($request->templates[0]["id"])->name . '  untuk soap ke ' . $soap->soap_number . '',
                    //     'type' => 'Soap',
                    //     'action' => 'Membuat'
                    // ]);
                } else {
                    $assesmentData = Assesment::create([
                        'soap_id' => $soap->id,
                        'type' => Template::find($template['id'])->type,
                        'name' => Template::find($template['id'])->name,
                        'user_id' => auth()->user()->id,
                        'status' => 'progress',
                    ]);
    
                    if ($template["is_new"] == 1) {
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
                        $is_new = $this->checkAssesment($norm, Template::find($template["id"])->type);
    
                        $SubAssesment = SubAssesment::create(collect($is_new)->except(['id', 'is_disabled', 'assesments'])->toArray());
                        $SubAssesment->update([
                            'template_id' => $template["id"],
                            'assesment_id' => $assesmentData->id,
                            'created_by' => auth()->user()->name,
                            'updated_by' => null
                        ]);
                    }

                    $assesments = Assesment::where('soap_id', $soap->id)->get();
                    foreach ($assesments as $key => $assesment) {
                        $assesment_type[] = $assesment->type;
                    }
        
                    $soap->update([
                        'assesment' => implode(', ', $assesment_type),
                        'status' => 'progress',
                        'updated_at' => now()
                    ]);

                }
            }

            
            
            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $soap->id,
                'visit_id' => $visitId,
                'note' => 'Membuat assesmen ' . implode(', ', $template_type) . '  untuk soap ke ' . $soap->soap_number . '',
                'type' => 'Soap',
                'action' => 'Membuat'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Assesment sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add assesment.',
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

    /**
     * Update status assesment
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @param  mixed $soap
     * @param  mixed $assesment
     * @return void
     */
    public function updateStatus(Request $request, $visitId, Soap $soap, Assesment $assesment)
    {
        try {
            if (
                auth()->user()->can('ubah status assesment covid') && $assesment->type == 'Covid' ||
                auth()->user()->can('ubah status assesment dewasa') && $assesment->type == 'Umum Dewasa' || auth()->user()->can('ubah status assesment anak') && $assesment->type == 'Umum Anak' || auth()->user()->can('ubah status assesment spesialis anak') && $assesment->type == 'Spesialis Anak' || auth()->user()->can('ubah status assesment spesialis penyakit dalam') && $assesment->type == 'Spesialis Penyakit Dalam' || auth()->user()->can('ubah status assesment spesialis syaraf') && $assesment->type == 'Spesialis Syaraf' || auth()->user()->can('ubah status assesment spesialis paru') && $assesment->type == 'Spesialis Paru'
            ) {
                $assesment->update([
                    'status' => $request->status
                ]);

                // info($assesment->type);

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
            } else {
                return response()->json([
                    'status' => 'unautorized',
                ], 403);
            }

            function updateStatusLog($req)
            {
                switch ($req) {
                    case 'done':
                        return 'Selesai';

                    case 'cancel':
                        return 'Batal';

                    default:
                        return 'Progress';
                }
            }
            // info($assesment->type);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $assesment->id,
                'visit_id' => $visitId,
                'soap_id' => $soap->id,
                'dokumen_id' => $assesment->id,
                'note' => 'Mengubah status Assesmen ' . $assesment->type . ' menjadi ' . updateStatusLog($request->status),
                'type' => 'Assesmen',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Assesment sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update assesment.',
            ], 500);
        }
    }

    /**
     * Lihat assesment
     *
     * @param  mixed $visitId
     * @param  mixed $soap
     * @param  mixed $assesment
     * @return void
     */
    public function edit($visitId, Soap $soap, Assesment $assesment)
    {
        try {
            // $data = [];
            // $data['type'] = $assesment->type;
            // $data['name'] = $assesment->name;
            // $data['allow'] = auth()->user()->id == $assesment->user_id ? 1 : 0;
            // $data['status'] = $assesment->status;
            // $dataCollec = collect($data);
            return response()->json($assesment);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve assesment.',
            ], 500);
        }
    }
}
