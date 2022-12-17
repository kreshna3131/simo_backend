<?php

namespace App\Http\Controllers\v1;

use Carbon\Carbon;
use App\Models\Recipe;
use App\Models\Concoction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Custom\Pagination\CustomPaginator;
use App\Models\Soap;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\Http;

class RecipeController extends Controller
{
    /**
     * Function permission
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:melihat permintaan resep di kunjungan|tambah permintaan resep di kunjungan|mengelola permintaan di farmasi',['only' => ['listingRecipeUser', 'storeRecipe', 'listingRecipe']]);
         $this->middleware('permission:melihat permintaan resep di kunjungan', ['only' => ['listingRecipeUser']]);
         $this->middleware('permission:tambah permintaan resep di kunjungan', ['only' => ['storeRecipe']]);
         $this->middleware('permission:mengelola permintaan di farmasi', ['only' => ['listingRecipe']]);
         $this->middleware('permission:lihat histori rekam medis', ['only' => ['printHistoryRecipe', 'previewHistoryRecipe']]);
    }

    /**
     * Listing permintaan E-Resep untuk user (selain Apoteker)
     *
     * @param  mixed $var
     * @return void
     */
    public function listingRecipeUser(Request $request, $visitId)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $recipes = Recipe::query()
                ->where('visit_id', $visitId)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        info($date);
                        $query->where("name", "like", "%{$request->search}%")
                            ->orWhere("unique_id", "like", "%{$request->search}%")
                            ->orWhere("info", "like", "%{$request->search}%")
                            ->orWhere("type", "like", "%{$request->search}%")
                            ->orWhere("created_by", "like", "%{$request->search}%")
                            ->orWhere("nota_number", "like", "%{$request->search}%")
                            ->orWhere("status", "like", "%{$request->search}%");
                        if($date != 'date invalid') {
                            $query->orWhere("created_at", "like", "%{$date}%");
                        }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $recipePaginate = (new CustomPaginator(
                $recipes->clone()->forPage($currentPage, $itemPerPage)->get(),
                $recipes->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/visit/'.$visitId.'/recipe/listing');

            return response()->json($recipePaginate);
        }

        catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve recipe'
            ], 500);
        }
    }

    /**
     * Listing e-resep untuk apoteker
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @return void
     */
    public function listingRecipe(Request $request)
    {
        try {
            $dateStart = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $dateEnd = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $recipes = Recipe::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where("name", "like", "%{$request->search}%")
                        ->orwhere(function ($query) use($request) {
                            $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                            $query->where("unique_id", "like", "%{$request->search}%")
                                ->orWhere("info", "like", "%{$request->search}%")
                                ->orWhere("type", "like", "%{$request->search}%")
                                ->orWhere("created_by", "like", "%{$request->search}%")
                                ->orWhere("nota_number", "like", "%{$request->search}%")
                                ->orWhere("status", "like", "%{$request->search}%");
                            if ($date != 'date invalid') {
                                $query->orWhere("created_at", "like", "%{$date}%");
                            }
                        });
                })
                ->when($request->filled('status'), function ($query) use($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('is_read'), function ($query) use($request) {
                    $query->where('is_read_apo', $request->is_read);
                })
                ->when($request->filled('periode'), function ($query) use($dateStart, $dateEnd) {
                    $query->whereBetween('created_at', ["$dateStart  00:00:00", "$dateEnd 23:59:59"]);
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $recipePaginate = (new CustomPaginator(
                $recipes->clone()->forPage($currentPage, $itemPerPage)->get(),
                $recipes->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/visit/recipe/listing');

            return response()->json(array_merge(
                $recipePaginate->toArray(),
                ['is_read_count' => $recipes->clone()->where('is_read_apo', 0)->count()]
            ));
        }

        catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve recipe'
            ], 500);
        }
    }

    /**
     * Listing untuk tambah resep
     *
     * @return void
     */
    public function listingStoreRecipe()
    {
        try {
            $data = [];

            $row['type'] = 'Racikan';
            $data[] = $row;

            $row['type'] = 'Non Racikan';
            $data[] = $row;

            $templates = collect($data);
            $racikan = collect();
            $nonracikan = collect();

            $templates = $templates->merge($racikan);
            $templates = $templates->merge($nonracikan);

            return response()->json($templates);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve listing eresep'
            ], 500);
        }
    }

    /**
     * Menambahkan E-Resep
     *
     * @param  mixed $request
     * @return void
     */
    public function storeRecipe(Request $request, $visitId)
    {
        $request->validate(['type' => 'required']);
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $nama = $visits->status() == 200 ? $visits[0]['nama'] : '';
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            if(Recipe::where('visit_id', $visitId)->count() == 0) {
                $visit_count = Recipe::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = Recipe::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }

            $data = [];
            foreach ($request->type as $key => $type) {
                $data[] = $type;
            }

            $number = Recipe::where('visit_id', $visitId)->get()->count();
            $lastTodayRecipe = Recipe::query()->today()->latest()->first();
            $eresep = Recipe::create([
                'visit_id' => $visitId,
                'no_rm' => $norm,
                'unique_id' => Recipe::generateUniqueId($lastTodayRecipe),
                'name' => 'Resep ke '. $number + 1,
                'status' => 'waiting',
                'type' => implode(', ', $data),
                'info' => '0 Obat dan 0 Komentar',
                'user_id' => auth()->user()->id,
                'is_read_apo' => 0,
                'is_read_doc' => 1,
                'created_by' => auth()->user()->name,
                'created_for' => $nama,
                'visit_number' => $visit_count
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $eresep->unique_id,
                'request_id' => $eresep->id,
                'visit_id' => $visitId,
                'note' => 'Membuat permintaan resep jenis '. implode(', ', $data),
                'type' => 'Resep',
                'action' => 'Membuat'
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Eresep successfully added.'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add eresep'
            ], 500);
        }
    }

    /**
     * Untuk melihat detail data e-resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function editRecipe(Recipe $recipe)
    {
        try {
            return response()->json($recipe);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve eresep'
            ], 500);
        }
    }

    /**
     * Method untuk mengubah status E-Resep
     *
     * @return void
     */
    public function updateStatusRecipe(Request $request, Recipe $recipe)
    {
        $validated = $request->validate(['status' => 'required', 'nota_number' => 'required_if:status,done']);
        try {

            $recipe->update($validated);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Mengubah status permintaan resep menjadi '. $recipe->logStatus($validated['status']),
                'type' => 'Resep',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Recipe status successfully updated',
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update recipe status'
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
     * Print PDF E-Resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function printPDF(Recipe $recipe)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $recipe->visit_id);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $concoctions = $recipe->concoctions;
            $nonconcoctions = $recipe->nonConcoctions;
            $user = $recipe->user;
            $weight_body = $this->checkAssesment($norm, 'Global');

            $pdf = Pdf::loadView('pdf.recipe', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'recipe' => $recipe,
                'concoctions' => $concoctions,
                'nonconcoctions' => $nonconcoctions,
                'user' => $user,
                'weight' => $weight_body
            ]);

            return $pdf->download('E-Resep -'.$recipe->unique_id.'.pdf');
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf request rad.',
            ], 500);
        }
    }

    /**
     * Preview PDF E-Resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function previewPDF(Recipe $recipe)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $recipe->visit_id);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $concoctions = $recipe->concoctions;
            $nonconcoctions = $recipe->nonConcoctions;
            $user = $recipe->user;
            $weight_body = $this->checkAssesment($norm, 'Global');

            $pdf = Pdf::loadView('pdf.recipe', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'recipe' => $recipe,
                'concoctions' => $concoctions,
                'nonconcoctions' => $nonconcoctions,
                'user' => $user,
                'weight' => $weight_body 
            ]);

            return $pdf->stream();
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf request rad.',
            ], 500);
        }
    }

    /**
     * Print PDF E-Resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function printHistoryRecipe($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $recipes = Recipe::where('no_rm', $norm)->get();

            foreach ($recipes as $key => $recipe) {
                $concoctions = $recipe->concoctions;
                $nonconcoctions = $recipe->nonConcoctions;
                $user = $recipe->user;
                $weight_body = $this->checkAssesment($norm, 'Global');
                $view = view('pdf.recipe')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'recipe' => $recipe,
                    'concoctions' => $concoctions,
                    'nonconcoctions' => $nonconcoctions,
                    'user' => $user,
                    'weight' => $weight_body
                ]);
                $html .= $view->render();
            }
            info($norm);
            $pdf = Pdf::loadHTML($html);

            return $pdf->download('Histori Rekam Medis Resep.pdf');
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf recipe.',
            ], 500);
        }
    }

    /**
     * Preview PDF E-Resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function previewHistoryRecipe($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $recipes = Recipe::where('no_rm', $norm)->get();

            foreach ($recipes as $key => $recipe) {
                $concoctions = $recipe->concoctions;
                $nonconcoctions = $recipe->nonConcoctions;
                $user = $recipe->user;
                $weight_body = $this->checkAssesment($norm, 'Global');
                $view = view('pdf.recipe')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'recipe' => $recipe,
                    'concoctions' => $concoctions,
                    'nonconcoctions' => $nonconcoctions,
                    'user' => $user,
                    'weight' => $weight_body
                ]);
                $html .= $view->render();
            }
            info($norm);
            $pdf = Pdf::loadHTML($html);

            return $pdf->stream();
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf recipe.',
            ], 500);
        }
    }
}
