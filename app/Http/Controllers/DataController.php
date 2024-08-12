<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Models\Division;
use App\Models\Pegawai;

class DataController extends Controller
{
    public function divisi(Request $request)
    {
        $query = Division::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $divisions = $query->paginate(5);

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'divisions' => $divisions->items(),
            ],
            'pagination' => [
                'current_page' => $divisions->currentPage(),
                'per_page' => $divisions->perPage(),
                'total' => $divisions->total(),
                'last_page' => $divisions->lastPage(),
            ],
        ]);
    }

    public function pegawai(Request $request)
    {
        $query = Pegawai::with('division');
    
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
    
        $pegawai = $query->paginate(5);
    
        $Employees = collect($pegawai->items())->map(function ($pegawai) {
            return [
                'id' => $pegawai->id,
                'image' => $pegawai->image,
                'name' => $pegawai->name,
                'phone' => $pegawai->phone,
                'division' => $pegawai->division ? [
                    'id' => $pegawai->id_divisi,
                    'name' => $pegawai->division["name"],
                ] : null,
                'position' => $pegawai->position,
            ];
        })->toArray(); 
    
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'employees' => $Employees, 
            ],
            'pagination' => [
                'current_page' => $pegawai->currentPage(),
                'per_page' => $pegawai->perPage(),
                'total' => $pegawai->total(),
                'last_page' => $pegawai->lastPage(),
            ],
        ]);
    }

    public function create(Request $request) {
        $storeData = $request->all();
        $validator = Validator::make($storeData, [
            'image' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'division' => 'required|exists:divisions,id',
            'position' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try{
            Pegawai::create($storeData);
            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully Created!',
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed',
            ]);
        }
    }

    public function update(Request $request, $id) {
        $pegawai = Pegawai::find($id);
        
        $storeData = $request->all();
        Validator::make($storeData, [
           'image' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'division' => 'required|exists:divisions,id',
            'position' => 'required',
        ]);

        try{
            $pegawai->update($storeData);
            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully Updated!',
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed',
            ]);
        }
    }

    public function destroy($id) {
        $pegawai = pegawai::find($id);
        try{
            $pegawai->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully Deleted!',
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to Delete',
            ]);
        }
    }
}
