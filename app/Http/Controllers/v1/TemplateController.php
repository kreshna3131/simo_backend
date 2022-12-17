<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeTemplate;
use App\Models\Group;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat pengaturan assesmen|ubah pengaturan assesmen', ['only' => ['listing', 'updateStatus', 'edit', 'listingAttribute', 'updateStatusAttribute', 'editAttribute', 'update']]);
         $this->middleware('permission:lihat pengaturan assesmen', ['only' => ['listing', 'listingAttribute']]);
         $this->middleware('permission:ubah pengaturan assesmen', ['only' => ['updateStatus', 'edit', 'updateStatusAttribute', 'editAttribute', 'update']]);
    }
    
    /**
     * listing template
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $active_data = Template::where('visibility', 1)->get()->count();
            $templates = Template::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $date = searchDate($request->search, 'd F Y', 'Y-m-d');
                $query->where('type', 'like', '%' . $request->search . '%');
                $query->orWhere('name', 'like', '%' . $request->search . '%');
                if($date != 'date invalid') {
                    $query->orWhere('updated_at', 'like', '%' . $date . '%');
                } 
            })
            ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);

            $dataPaginate = (new CustomPaginator(
                $templates->clone()->forPage($currentPage, $itemPerpage)->get(),
                $templates->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/template/listing');

            return response()->json(array_merge(
                $dataPaginate->toArray(), 
                ['active_data' => $active_data]
            ));
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve soap.',
            ], 500);
        }
    }
    
    /**
     * update status template
     *
     * @param  mixed $request
     * @param  mixed $template
     * @return void
     */
    public function updateStatus(Request $request, Template $template)
    {
        try {
            $validated = $request->validate(['visibility' => ['required', 'boolean']]);
            $template->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Template sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update template.',
            ], 500);
        }
    }
    
    /**
     * detail data template
     *
     * @param  mixed $template
     * @return void
     */
    public function edit(Template $template)
    {
        try {
            return response()->json($template);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve template.',
            ], 500);
        }
    }
    
    /**
     * listing Attribute
     *
     * @param  mixed $request
     * @param  mixed $template
     * @return void
     */
    public function listingAttribute(Request $request, Template $template)
    {
        try {
            $attributes = Attribute::select(
                'attribute_template.id', 
                'attributes.id as attr_id', 
                'attributes.type', 
                'attributes.label', 
                'attribute_template.group_name', 
                'attribute_template.status'
            )
            ->leftJoin("attribute_template", "attributes.id", "=", "attribute_template.attribute_id")
            ->leftJoin("templates", "attribute_template.template_id", "=", "templates.id")
            ->where('templates.id', $template->id)
            ->groupBy('attributes.id')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('label', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('group'), function ($query) use ($request) {
                $query->where('attribute_template.group_name', $request->group);
            });

            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);

            $dataPaginate = (new CustomPaginator(
                $attributes->clone()->forPage($currentPage, $itemPerpage)->get(),
                $attributes->clone()->get()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/template/list-attribute/'.$template->id);

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
     * update Status Attribute
     *
     * @param  mixed $request
     * @param  mixed $template
     * @param  mixed $attribute
     * @return void
     */
    public function updateStatusAttribute(Request $request, Template $template, Attribute $attribute)
    {
        try {
            $validated_data =  $request->validate(['status' => ['required', 'boolean']]);
            $pivot = $attribute->templates()->where('template_id', $template->id)->first()->pivot;
            $temp = ["keluhan",
            "riwayat_penyakit_sekarang",
            "riwayat_penyakit_dahulu",
            "riwayat_penyakit_keluarga",
            "keadaan_umum",
            "tindakan_resusitasi",
            "tekanan_darah",
            "frekuensi_nadi",
            "frekuensi_napas",
            "berat_badan",
            "tinggi_badan",
            "suhu_badan",
            "gds",
            "laboratorium_id",
            "ekg",
            "xray",
            "diagnosis_kerja",
            "diagnosis_banding",
            "rencana_terapi",
            "rencana_tindak_lanjut",
            "diagnosis_keperawatan",
            "implementasi"];
            info(!in_array($attribute->name, $temp));
            if(($template->type == 'Umum Dewasa' || $template->type == 'Umum Anak' || $template->type == 'Spesialis Anak' || $template->type == 'Spesialis Penyakit Dalam' || $template->type == 'Spesialis Syaraf' || $template->type == 'Spesialis Paru') && !in_array($attribute->name, $temp) == 0 && $request->status == 0) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'This column required.',
                ], 500);
            } else {
                AttributeTemplate::find($pivot->id)->update($validated_data);
            }

            if($template->type == 'Umum Dewasa' && str_contains($attribute->name, 'nutrisional') !== FALSE) {
                AttributeTemplate::find($pivot->id)->update([
                    'rules' => 'nullable'
                ]);
            } else {
                AttributeTemplate::find($pivot->id)->update([
                    'rules' => 'required'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status Attribute success updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update status attribute.',
            ], 500);
        }
    }
    
    /**
     * detail data Attribute
     *
     * @param  mixed $template
     * @param  mixed $attribute
     * @return void
     */
    public function editAttribute(Template $template, Attribute $attribute)
    {
        try {
            $attributes = Attribute::select(
                'attribute_template.id', 
                'attributes.type', 
                'attributes.label', 
                'attribute_template.group_name', 
                'attribute_template.status', 
            )
            ->leftJoin("attribute_template", "attributes.id", "=", "attribute_template.attribute_id")
            ->leftJoin("templates", "attribute_template.template_id", "=", "templates.id")
            ->where('templates.id', $template->id)
            ->where('attributes.id', $attribute->id)
            ->groupBy('attributes.id')
            ->first();

            return response()->json($attributes);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve template.',
            ], 500);
        }
    }
    
    /**
     * list Group
     *
     * @return void
     */
    public function listGroup()
    {
        try {
            $groups = Group::all();

            return response()->json($groups);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve groups.',
            ], 500);
        }
    }
        
    /**
     * update status dan group attribute
     *
     * @param  mixed $request
     * @param  mixed $template
     * @param  mixed $attribute
     * @return void
     */
    public function update(Request $request, Template $template, Attribute $attribute)
    {
        try {
            $validated_data =  $request->validate(['status' => ['required', 'boolean'], 'group_name' => ['required', 'string']]);
            $pivot = $attribute->templates()->where('template_id', $template->id)->first()->pivot;
            $group_id = Group::where('name', $request->group_name)->first()->id;
            AttributeTemplate::find($pivot->id)->update($validated_data);
            AttributeTemplate::find($pivot->id)->update([
                'group_id' => $group_id
            ]);

            if($template->type == 'Umum Dewasa' && str_contains($attribute->name, 'nutrisional') !== FALSE) {
                AttributeTemplate::find($pivot->id)->update([
                    'rules' => 'nullable'
                ]);
            } else {
                AttributeTemplate::find($pivot->id)->update([
                    'rules' => 'required'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status Attribute success updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update status attribute.',
            ], 500);
        }
    }
}
